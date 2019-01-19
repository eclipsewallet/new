<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Model\Import;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportExportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper as ResourceHelper;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportHelper;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Import\Location\RowValidatorInterface as ValidatorInterface;
use Mageplaza\StoreLocator\Model\LocationFactory;

/**
 * Class Location
 * @package Mageplaza\StoreLocator\Model\Import
 */
class Location extends AbstractEntity
{
    const COL_NAME = 'name';

    const COL_STATUS = 'status';

    const COL_DESCRIPTION = 'description';

    const COL_STORE_IDS = 'store_ids';

    const COL_CITY = 'city';

    const COL_COUNTRY = 'country';

    const COL_STREET = 'street';

    const COL_STATE_PROVINCE = 'state_province';

    const COL_POSTAL_CODE = 'postal_code';

    const COL_URL_KEY = 'url_key';

    const COL_TIME_ZONE = 'time_zone';

    const COL_LAT = 'latitude';

    const COL_LNG = 'longitude';

    const COL_MONDAY = 'operation_mon';

    const COL_TUESDAY = 'operation_tue';

    const COL_WEDNESDAY = 'operation_wed';

    const COL_THURSDAY = 'operation_thu';

    const COL_FRIDAY = 'operation_fri';

    const COL_SATURDAY = 'operation_sat';

    const COL_SUNDAY = 'operation_sun';

    const COL_IS_TIME_ZONE_CONFIG = 'is_config_time_zone';

    const COL_IS_WEBSITE_CONFIG = 'is_config_website';

    const TABLE_ENTITY = 'mageplaza_storelocator_location';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_ID_IS_EMPTY => 'Empty',
    ];

    /**
     * @var array
     */
    protected $_permanentAttributes = [self::COL_URL_KEY, self::COL_CITY, self::COL_COUNTRY, self::COL_STREET, self::COL_POSTAL_CODE];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        self::COL_NAME,
        self::COL_STATUS,
        self::COL_DESCRIPTION,
        self::COL_STORE_IDS,
        self::COL_CITY,
        self::COL_COUNTRY,
        self::COL_STREET,
        self::COL_STATE_PROVINCE,
        self::COL_POSTAL_CODE,
        self::COL_URL_KEY,
        self::COL_TIME_ZONE,
        self::COL_LAT,
        self::COL_LNG,
        self::COL_MONDAY,
        self::COL_TUESDAY,
        self::COL_WEDNESDAY,
        self::COL_THURSDAY,
        self::COL_FRIDAY,
        self::COL_SATURDAY,
        self::COL_SUNDAY,
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @var array
     */
    protected $_validators = [];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_connection;

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var LocationFactory
     */
    protected $_locationFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Location constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportExportHelper $importExportData
     * @param ImportHelper $importData
     * @param ResourceConnection $resource
     * @param ResourceHelper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param StoreManagerInterface $storeManager
     * @param LocationFactory $locationFactory
     * @param Data $helperData
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportExportHelper $importExportData,
        ImportHelper $importData,
        ResourceConnection $resource,
        ResourceHelper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        StoreManagerInterface $storeManager,
        LocationFactory $locationFactory,
        Data $helperData
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->_storeManager = $storeManager;
        $this->_locationFactory = $locationFactory;
        $this->_helperData = $helperData;
    }

    /**
     * @return array
     */
    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'mageplaza_storelocator_location';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $title = false;
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        if (!isset($rowData[self::COL_URL_KEY]) || empty($rowData[self::COL_URL_KEY])) {
            $this->addRowError(ValidatorInterface::ERROR_ID_IS_EMPTY, $rowNum);

            return false;
        }
        if (!isset($rowData[self::COL_CITY]) || empty($rowData[self::COL_CITY])) {
            $this->addRowError(ValidatorInterface::ERROR_ID_IS_EMPTY, $rowNum);

            return false;
        }
        if (!isset($rowData[self::COL_COUNTRY]) || empty($rowData[self::COL_COUNTRY])) {
            $this->addRowError(ValidatorInterface::ERROR_ID_IS_EMPTY, $rowNum);

            return false;
        }
        if (!isset($rowData[self::COL_STREET]) || empty($rowData[self::COL_STREET])) {
            $this->addRowError(ValidatorInterface::ERROR_ID_IS_EMPTY, $rowNum);

            return false;
        }
        if (!isset($rowData[self::COL_STATE_PROVINCE]) || empty($rowData[self::COL_STATE_PROVINCE])) {
            $this->addRowError(ValidatorInterface::ERROR_ID_IS_EMPTY, $rowNum);

            return false;
        }
        if (!isset($rowData[self::COL_POSTAL_CODE]) || empty($rowData[self::COL_POSTAL_CODE])) {
            $this->addRowError(ValidatorInterface::ERROR_ID_IS_EMPTY, $rowNum);

            return false;
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create advanced location data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } else if (Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } else if (Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }

        return true;
    }

    /**
     * Save location
     *
     * @return $this
     * @throws \Exception
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();

        return $this;
    }

    /**
     * Replace location
     *
     * @return $this
     * @throws \Exception
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();

        return $this;
    }

    /**
     * Deletes location data from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
        $ids = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowId = $rowData[self::COL_URL_KEY];
                    $ids[] = $rowId;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($ids) {
            $this->deleteEntityFinish(array_unique($ids), self::TABLE_ENTITY);
        }

        return $this;
    }

    /**
     * Save and replace location
     *
     * @return $this
     * @throws \Exception
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $ids = [];
        $storeIds = [];
        $storeList = $this->_storeManager->getStores();
        foreach ($storeList as $store) {
            $storeIds[] = $store->getId();
        }
        array_push($storeIds, '0');
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_ID_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $rowId = $rowData[self::COL_URL_KEY];
                $ids[] = $rowId;
                $dataStoreIds = [];
                $isStoreIdsDefault = true;
                if (isset($rowData[self::COL_STORE_IDS])) {
                    $dataStoreIds = explode(',', $rowData[self::COL_STORE_IDS]);
                } else {
                    $isStoreIdsDefault = false;
                }
                foreach ($dataStoreIds as $item) {
                    if (!in_array($item, $storeIds)) {
                        $isStoreIdsDefault = false;
                    }
                }
                $entityList[$rowId][] = [
                    self::COL_NAME                => $rowData[self::COL_NAME],
                    self::COL_STATUS              => (isset($rowData[self::COL_STATUS]) && in_array($rowData[self::COL_STATUS], ['1', '0'])) ? $rowData[self::COL_STATUS] : '1',
                    self::COL_DESCRIPTION         => $rowData[self::COL_DESCRIPTION],
                    self::COL_STORE_IDS           => ($isStoreIdsDefault) ? $rowData[self::COL_STORE_IDS] : '0',
                    self::COL_CITY                => $rowData[self::COL_CITY],
                    self::COL_COUNTRY             => $rowData[self::COL_COUNTRY],
                    self::COL_STREET              => $rowData[self::COL_STREET],
                    self::COL_STATE_PROVINCE      => $rowData[self::COL_STATE_PROVINCE],
                    self::COL_POSTAL_CODE         => $rowData[self::COL_POSTAL_CODE],
                    self::COL_URL_KEY             => $rowData[self::COL_URL_KEY],
                    self::COL_TIME_ZONE           => $this->_helperData->getStoreTimeSetting('time_zone'),
                    self::COL_LAT                 => $rowData[self::COL_LAT] ?: 20.9790643,
                    self::COL_LNG                 => $rowData[self::COL_LNG] ?: 105.7854772,
                    self::COL_MONDAY              => $this->_helperData->getConfigOpenTime(Data::MONDAY),
                    self::COL_TUESDAY             => $this->_helperData->getConfigOpenTime(Data::TUESDAY),
                    self::COL_WEDNESDAY           => $this->_helperData->getConfigOpenTime(Data::WEDNESDAY),
                    self::COL_THURSDAY            => $this->_helperData->getConfigOpenTime(Data::THURSDAY),
                    self::COL_FRIDAY              => $this->_helperData->getConfigOpenTime(Data::FRIDAY),
                    self::COL_SATURDAY            => $this->_helperData->getConfigOpenTime(Data::SATURDAY),
                    self::COL_SUNDAY              => $this->_helperData->getConfigOpenTime(Data::SUNDAY),
                    self::COL_IS_TIME_ZONE_CONFIG => 1,
                    self::COL_IS_WEBSITE_CONFIG   => 1
                ];
            }
            if (Import::BEHAVIOR_REPLACE == $behavior) {
                if ($ids) {
                    if ($this->deleteEntityFinish(array_unique($ids), self::TABLE_ENTITY)) {
                        $this->saveEntityFinish($entityList, self::TABLE_ENTITY);
                    }
                }
            } else if (Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, self::TABLE_ENTITY);
            }
        }

        return $this;
    }

    /**
     * @param array $entityData
     * @param $table
     *
     * @return $this
     * @throws \Exception
     */
    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $locationModel = $this->_locationFactory->create();
            $tableName = $this->_resource->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    if ($locationModel->getResource()->isDuplicateUrlKey($row['url_key']) != null) {
                        $where = ['location_id = ?' => (int)$locationModel->getResource()->isDuplicateUrlKey($row['url_key'])];
                        $this->_connection->update($tableName, $row, $where);
                    } else {
                        $entityIn[] = $row;
                    }
                }
            }
            foreach ($entityIn as $item) {
                $locationModel->setData($item)->save();
            }
        }

        return $this;
    }

    /**
     * @param array $ids
     * @param $table
     *
     * @return bool
     */
    protected function deleteEntityFinish(array $ids, $table)
    {
        if ($table && $ids) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->_resource->getTableName($table),
                    $this->_connection->quoteInto('url_key IN (?)', $ids)
                );

                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }
}