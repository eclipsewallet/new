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


namespace Mirasvit\Indexer\Repository;

use Mirasvit\Indexer\Api\Repository\HistoryRepositoryInterface;
use Mirasvit\Indexer\Model\ResourceModel\History\CollectionFactory;
use Mirasvit\Indexer\Model\HistoryFactory;
use Mirasvit\Indexer\Api\Data\HistoryInterface;

class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        HistoryFactory $historyFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->historyFactory = $historyFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->historyFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function save(HistoryInterface $history)
    {
        /** @var \Mirasvit\Indexer\Model\History $history */
        $history->save();

        return $history;
    }

    /**
     * {@inheritdoc}
     */
    public function get($historyId)
    {
        return $this->historyFactory->create()->load($historyId);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(HistoryInterface $history)
    {
        /** @var \Mirasvit\Indexer\Model\History $history */
        $history->delete();

        return true;
    }
}