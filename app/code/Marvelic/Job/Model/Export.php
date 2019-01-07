<?php


namespace Marvelic\Job\Model;

use Marvelic\Job\Api\Data\ExportInterface;
use Marvelic\Job\Api\Data\ExportInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Export extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $exportDataFactory;

    protected $_eventPrefix = 'marvelic_job_export';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ExportInterfaceFactory $exportDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Marvelic\Job\Model\ResourceModel\Export $resource
     * @param \Marvelic\Job\Model\ResourceModel\Export\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExportInterfaceFactory $exportDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Marvelic\Job\Model\ResourceModel\Export $resource,
        \Marvelic\Job\Model\ResourceModel\Export\Collection $resourceCollection,
        array $data = []
    ) {
        $this->exportDataFactory = $exportDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve export model with export data
     * @return ExportInterface
     */
    public function getDataModel()
    {
        $exportData = $this->getData();
        
        $exportDataObject = $this->exportDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $exportDataObject,
            $exportData,
            ExportInterface::class
        );
        
        return $exportDataObject;
    }
}
