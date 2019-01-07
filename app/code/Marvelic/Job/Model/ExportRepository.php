<?php


namespace Marvelic\Job\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Marvelic\Job\Api\ExportRepositoryInterface;
use Marvelic\Job\Model\ResourceModel\Export\CollectionFactory as ExportCollectionFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Reflection\DataObjectProcessor;
use Marvelic\Job\Api\Data\ExportSearchResultsInterfaceFactory;
use Marvelic\Job\Model\ResourceModel\Export as ResourceExport;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Marvelic\Job\Api\Data\ExportInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;

class ExportRepository implements ExportRepositoryInterface
{

    protected $resource;

    protected $extensionAttributesJoinProcessor;

    protected $dataObjectProcessor;

    protected $extensibleDataObjectConverter;
    protected $exportFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $dataExportFactory;

    protected $exportCollectionFactory;

    private $collectionProcessor;

    protected $searchResultsFactory;


    /**
     * @param ResourceExport $resource
     * @param ExportFactory $exportFactory
     * @param ExportInterfaceFactory $dataExportFactory
     * @param ExportCollectionFactory $exportCollectionFactory
     * @param ExportSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceExport $resource,
        ExportFactory $exportFactory,
        ExportInterfaceFactory $dataExportFactory,
        ExportCollectionFactory $exportCollectionFactory,
        ExportSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->exportFactory = $exportFactory;
        $this->exportCollectionFactory = $exportCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataExportFactory = $dataExportFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Marvelic\Job\Api\Data\ExportInterface $export
    ) {
        /* if (empty($export->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $export->setStoreId($storeId);
        } */
        
        $exportData = $this->extensibleDataObjectConverter->toNestedArray(
            $export,
            [],
            \Marvelic\Job\Api\Data\ExportInterface::class
        );
        
        $exportModel = $this->exportFactory->create()->setData($exportData);
        
        try {
            $this->resource->save($exportModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the export: %1',
                $exception->getMessage()
            ));
        }
        return $exportModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($exportId)
    {
        $export = $this->exportFactory->create();
        $this->resource->load($export, $exportId);
        if (!$export->getId()) {
            throw new NoSuchEntityException(__('Export with id "%1" does not exist.', $exportId));
        }
        return $export->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->exportCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Marvelic\Job\Api\Data\ExportInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Marvelic\Job\Api\Data\ExportInterface $export
    ) {
        try {
            $exportModel = $this->exportFactory->create();
            $this->resource->load($exportModel, $export->getExportId());
            $this->resource->delete($exportModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Export: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($exportId)
    {
        return $this->delete($this->getById($exportId));
    }
}
