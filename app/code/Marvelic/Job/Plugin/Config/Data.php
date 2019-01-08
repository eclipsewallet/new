<?php
/**
 * Prepare cron jobs data
 */

namespace Marvelic\Job\Plugin\Config;

use Marvelic\Job\Model\ExportFactory;
use Psr\Log\LoggerInterface;
use Marvelic\Job\Cron\Export;

class Data
{
    /**
     * @var ExportFactory
     */
    protected $_exportJobFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */

    protected $jobCodeExportPattern = 'marvelic_export_jobs_run_id_%u';

    /**
     * Data constructor.
     *
     * @param ExportFactory $exportFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ExportFactory $exportFactory,
        LoggerInterface $logger
    ) {
        $this->_exportJobFactory = $exportFactory;
        $this->logger = $logger;
    }

    /**
     * Implement cron jobs created via admin panel into system cron jobs generated from xml files
     *
     * @param \Magento\Cron\Model\Config\Data $subject
     * @param                                 $result
     *
     * @return mixed
     */
    public function afterGetJobs(\Magento\Cron\Model\Config\Data $subject, $result)
    {
        $result = $this->scopeCrons($result, $this->_exportJobFactory, Export::class, $this->jobCodeExportPattern);
        return $result;
    }

    /**
     * @param $result
     * @param $factory
     * @param $instance
     * @param $pattern
     *
     * @return mixed
     */
    protected function scopeCrons($result, $factory, $instance, $pattern)
    {
        $jobCollection = $factory->create()->getCollection();
        $jobCollection->addFieldToFilter('is_active', 1);
        $jobCollection->addFieldToFilter('cron', ['neq' => '']);
        $jobCollection->load();
        foreach ($jobCollection as $job) {
            $jobName = sprintf($pattern, $job->getId());
            $result['default'][$jobName] = [
                'name' => $jobName,
                'instance' => $instance,
                'method' => 'execute',
                'schedule' => $job->getCron()
            ];
        }

        return $result;
    }
}
