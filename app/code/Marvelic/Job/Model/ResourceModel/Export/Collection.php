<?php


namespace Marvelic\Job\Model\ResourceModel\Export;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Marvelic\Job\Model\Export::class,
            \Marvelic\Job\Model\ResourceModel\Export::class
        );
    }
}
