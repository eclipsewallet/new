<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-indexer
 * @version   1.0.12
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Indexer\Service;

use Mirasvit\Indexer\Api\Data\HistoryInterface;
use Mirasvit\Indexer\Api\Repository\HistoryRepositoryInterface;
use Mirasvit\Indexer\Api\Service\HistoryServiceInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mirasvit\Indexer\Model\Config;

class HistoryService implements HistoryServiceInterface
{
    /**
     * @var array
     */
    private static $pointers = [];

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        HistoryRepositoryInterface $historyRepository,
        DateTime $dateTime,
        Config $config
    ) {
        $this->historyRepository = $historyRepository;
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function start($indexerId, $summary)
    {
        $history = $this->historyRepository->create();
        $history->setIndexerId($indexerId)
            ->setSummary($summary)
            ->setStartedAt($this->dateTime->gmtDate())
            ->setTickAt($this->dateTime->gmtDate());

        $history = $this->historyRepository->save($history);

        $id = $history->getId();

        $this->lock($id);

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function finish($historyId, $message = '')
    {
        $history = $this->historyRepository->get($historyId);

        $startedAt = strtotime($history->getStartedAt());
        $executionTime = strtotime($this->dateTime->gmtDate()) - $startedAt;

        $history->setExecutionTime($executionTime);
        $history->setMessage($message);

        $this->historyRepository->save($history);

        $this->unlock($historyId);
    }

    /**
     * {@inheritdoc}
     */
    public function tick($historyId)
    {
        $history = $this->historyRepository->get($historyId);

        if ($history->getTickAt() != $this->dateTime->gmtDate()) {
            $history->setTickAt($this->dateTime->gmtDate());

            $this->historyRepository->save($history);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecentHistory()
    {
        $collection = $this->historyRepository->getCollection()
            ->setOrder(HistoryInterface::STARTED_AT, 'desc')
            ->setPageSize(100);

        return $collection;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isLocked($id)
    {
        $pointer = $this->getLockPointer($id);

        if (@flock($pointer, LOCK_EX | LOCK_NB)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    private function lock($id)
    {
        $pointer = $this->getLockPointer($id);
        @flock($pointer, LOCK_EX | LOCK_NB);

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    private function unlock($id)
    {
        $pointer = $this->getLockPointer($id);
        @flock($pointer, LOCK_UN);
        @fclose($pointer);

        return true;
    }

    /**
     * @param int $id
     * @return resource
     */
    private function getLockPointer($id)
    {
        if (!isset(self::$pointers[$id])) {
            $file = $this->config->getTmpPath() . '/indexer' . $id;
            if (file_exists($file)) {
                $pointer = fopen($file, 'w+');
            } else {
                $pointer = fopen($file, 'w+');
                fwrite($pointer, $id);
                chmod($file, 0777);
            }

            self::$pointers[$id] = $pointer;
        }

        return self::$pointers[$id];
    }
}