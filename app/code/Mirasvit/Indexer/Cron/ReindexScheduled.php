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



namespace Mirasvit\Indexer\Cron;

use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;
use Mirasvit\Indexer\Api\Data\StateInterface;
use Mirasvit\Indexer\Model\Config;

class ReindexScheduled
{
    /**
     * @var IndexerCollectionFactory
     */
    private $indexerCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        IndexerCollectionFactory $indexerCollectionFactory,
        Config $config
    ) {
        $this->indexerCollectionFactory = $indexerCollectionFactory;
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->config->updateLastRun();

        $lockFile = $this->config->getTmpPath() . '/indexer.cli.lock';
        $lockPointer = fopen($lockFile, "w");

        if (flock($lockPointer, LOCK_EX | LOCK_NB)) {
            foreach ($this->indexerCollectionFactory->create() as $indexer) {
                /** @var \Magento\Indexer\Model\Indexer $indexer */

                if ($indexer->getStatus() == StateInterface::STATUS_SCHEDULED) {
                    $indexer->reindexAll();
                }
            }

            flock($lockPointer, LOCK_UN);
        }

        fclose($lockPointer);
    }
}
