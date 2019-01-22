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



namespace Mirasvit\Indexer\Controller\Adminhtml\Indexer;

use Magento\Framework\DataObject;
use Mirasvit\Indexer\Controller\Adminhtml\Indexer;
use Magento\Framework\App\ObjectManager;
use Mirasvit\Indexer\Model\Config;
use Mirasvit\Indexer\Service\HistoryService;
use Magento\Backend\App\Action;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Indexer\Model\ResourceModel\Indexer\State\CollectionFactory as StateCollectionFactory;
use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListAction extends Indexer
{
    /**
     * @var HistoryService
     */
    private $historyService;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        HistoryService $historyService,
        TimezoneInterface $timezone,
        IndexerRegistry $indexerRegistry,
        StateCollectionFactory $stateCollectionFactory,
        IndexerCollectionFactory $indexerCollectionFactory,
        Config $config,
        Action\Context $context
    ) {
        $this->historyService = $historyService;
        $this->timezone = $timezone;
        $this->config = $config;

        parent::__construct($indexerRegistry, $stateCollectionFactory, $indexerCollectionFactory, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = [
            'indexer' => [],
            'history' => [],
        ];

        $om = ObjectManager::getInstance();;
        foreach ($this->indexerCollectionFactory->create() as $indexer) {
            /** @var \Magento\Indexer\Block\Backend\Grid\Column\Renderer\Status $statusRenderer */
            $statusRenderer = $om->create('Magento\Indexer\Block\Backend\Grid\Column\Renderer\Status');
            $statusRenderer->setColumn(new DataObject([
                'getter' => 'getStatus',
            ]));

            /** @var \Magento\Indexer\Block\Backend\Grid\Column\Renderer\Updated $updatedRenderer */
            $updatedRenderer = $om->create('Magento\Indexer\Block\Backend\Grid\Column\Renderer\Updated');
            $updatedRenderer->setColumn(new DataObject([
                'getter' => 'getLatestUpdated',
            ]));

            /** @var \Mirasvit\Indexer\Block\Adminhtml\Grid\Column\Renderer\Queue $queueRenderer */
            $queueRenderer = $om->create('Mirasvit\Indexer\Block\Adminhtml\Grid\Column\Renderer\Queue');

            $indexerData = new DataObject([
                'indexer_id'     => $indexer->getId(),
                'latest_updated' => $indexer->getLatestUpdated(),
                'status'         => $indexer->getStatus(),
            ]);
            $data['indexer'][$indexer->getId()] = [
                'indexer_status'  => $statusRenderer->render($indexerData),
                'indexer_updated' => $updatedRenderer->render($indexerData),
                'indexer_queue'   => $queueRenderer->render($indexerData),
            ];
        }

        foreach ($this->historyService->getRecentHistory() as $history) {
            /** @var \Mirasvit\Indexer\Api\Data\HistoryInterface $history */
            $indexerName = $history->getIndexerId();

            try {
                $indexerName = $this->indexRegistry->get($history->getIndexerId())->getTitle();
            } catch (\Exception $e) {
            }

            $status = 'error';

            if ($this->historyService->isLocked($history->getId())) {
                $status = 'working';
            } elseif ($history->getExecutionTime() !== null) {
                $status = 'success';
            }


            $data['history'][] = [
                'status'        => $status,
                'summary'       => $history->getSummary() . ' ' . $history->getMessage(),
                'indexer'       => $indexerName,
                'startedAt'     => $history->getStartedAt(),
                'executionTime' => $history->getExecutionTime() !== null ? $history->getExecutionTime() . ' sec' : '-',
            ];
        }

        $data['lastRunTime'] = $this->config->getLastRunTime();

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();

        $response->representJson(\Zend_Json::encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function _processUrlKeys()
    {
        return true;
    }
}
