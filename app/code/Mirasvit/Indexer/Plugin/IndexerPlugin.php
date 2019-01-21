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


namespace Mirasvit\Indexer\Plugin;

use Magento\Indexer\Model\Indexer;
use Mirasvit\Indexer\Api\Service\HistoryServiceInterface;

class IndexerPlugin
{
    /**
     * @var HistoryServiceInterface
     */
    private $historyService;

    /**
     * @var int
     */
    private $lastHistoryId;

    /**
     * @var bool
     */
    private $isFinished;

    public function __construct(
        HistoryServiceInterface $historyService
    ) {
        $this->historyService = $historyService;
    }

    /**
     * @param Indexer $indexer
     * @return void
     */
    public function beforeReindexAll(Indexer $indexer)
    {
        $this->isFinished = false;
        $this->lastHistoryId = $this->historyService->start($indexer->getId(), __('Reindex All'));

        register_shutdown_function([$this, 'onShutdown']);

    }

    /**
     * @param Indexer $indexer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterReindexAll(Indexer $indexer)
    {
        $this->historyService->finish($this->lastHistoryId);
        $this->isFinished = true;
    }

    /**
     * @return void
     */
    public function onShutdown()
    {
        if (!$this->isFinished) {
            $this->historyService->finish($this->lastHistoryId, 'Error');
        }
    }
}