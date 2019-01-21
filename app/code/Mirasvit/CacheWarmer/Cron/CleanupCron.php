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
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.12
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Cron;

use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Cron\Model\Schedule;
use Mirasvit\CacheWarmer\Api\Data\JobInterface;
use Mirasvit\CacheWarmer\Api\Data\LogInterface;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Data\TraceInterface;
use Mirasvit\CacheWarmer\Api\Repository\JobRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\LogRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\TraceRepositoryInterface;
use Mirasvit\CacheWarmer\Model\Config;

class CleanupCron
{
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var JobRepositoryInterface
     */
    private $jobRepository;

    /**
     * @var LogRepositoryInterface
     */
    private $logRepository;

    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        JobRepositoryInterface $jobRepository,
        LogRepositoryInterface $logRepository,
        TraceRepositoryInterface $traceRepository,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        Config $config
    ) {
        $this->pageRepository            = $pageRepository;
        $this->jobRepository             = $jobRepository;
        $this->logRepository             = $logRepository;
        $this->traceRepository             = $traceRepository;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->resource                    = $resource;
        $this->config                    = $config;
    }

    /**
     * @return void
     */
    public function execute()
    {
        // Delete old jobs
        $jobCollection = $this->jobRepository->getCollection();

        $jobCollection->addFieldToFilter(
            [
                JobInterface::FINISHED_AT,
                JobInterface::STARTED_AT,
            ],
            [
                ['lteq' => date('Y-m-d H:i:s', time() - 2 * 24 * 60 * 60)],
                ['lteq' => date('Y-m-d H:i:s', time() - 2 * 24 * 60 * 60)],
            ]
        );

        /** @var JobInterface $job */
        foreach ($jobCollection as $job) {
            $this->jobRepository->delete($job);
        }

        $maxRecordsNumber = 900;

        // Delete old logs
        $cnt = $this->resource->getConnection()->fetchOne(
            "SELECT COUNT(*) FROM {$this->resource->getTableName(LogInterface::TABLE_NAME)};"
        );
        $n = $cnt - $maxRecordsNumber;
        if ($n > 100) {
            $this->resource->getConnection()->query(
                "DELETE FROM {$this->resource->getTableName(LogInterface::TABLE_NAME)} ORDER BY log_id ASC LIMIT $n"
            );
        }


        // Delete old traces
        $cnt = $this->resource->getConnection()->fetchOne(
            "SELECT COUNT(*) FROM {$this->resource->getTableName(TraceInterface::TABLE_NAME)};"
        );
        $n = $cnt - $maxRecordsNumber;
        if ($n > 100) {
            $this->resource->getConnection()->query(
                "DELETE FROM {$this->resource->getTableName(TraceInterface::TABLE_NAME)} ORDER BY trace_id ASC LIMIT $n"
            );
        }

        // Delete ignored pages
        $offset         = 0;
        $limit          = 1000;
        $pageCollection = $this->pageRepository->getCollection();
        $select         = $pageCollection->getSelect();
        $select->limit($limit, $offset);

        while ($pageCollection->count()) {
            /** @var PageInterface $page */
            foreach ($pageCollection as $page) {
                if ($this->config->isIgnoredPage($page)) {
                    $this->pageRepository->delete($page);
                }
            }
            $pageCollection->clear();
            $offset += $limit;
            $select->limit($limit, $offset);
        }

        // Delete old cron jobs (running)
        $scheduleCollection = $this->scheduleCollectionFactory->create();
        $scheduleCollection->addFieldToFilter('status', Schedule::STATUS_RUNNING);
        $scheduleCollection->addFieldToFilter(
            'scheduled_at',
            ['lteq' => date('Y-m-d H:i:s', time() - 60 * 60)]
        );
        foreach ($scheduleCollection as $schedule) {
            $schedule->delete();
        }
    }
}
