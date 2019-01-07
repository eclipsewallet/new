<?php


namespace Marvelic\Job\Model\ResourceModel;

class Export extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('marvelic_job_export', 'export_id');
    }
}
