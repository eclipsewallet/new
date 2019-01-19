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

namespace Mageplaza\StoreLocator\Model\ResourceModel;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\StoreLocator\Helper\Data;

/**
 * Class Location
 * @package Mageplaza\StoreLocator\Model\ResourceModel
 */
class Location extends AbstractDb
{
    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var string
     */
    protected $_holidayLocationTable;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Location constructor.
     *
     * @param Context $context
     * @param DateTime $dateTime
     * @param ManagerInterface $eventManager
     * @param Data $helperData
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        ManagerInterface $eventManager,
        Data $helperData,
        $connectionName = null
    )
    {
        $this->_dateTime = $dateTime;
        $this->_eventManager = $eventManager;
        $this->_helperData = $helperData;

        parent::__construct($context, $connectionName);

        $this->_holidayLocationTable = $this->getTable('mageplaza_storelocator_location_holiday');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_storelocator_location', 'location_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }
        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->_dateTime->date());
        }
        $object->setUpdatedAt($this->_dateTime->date());

        $object->setUrlKey(
            $this->_helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getIsDefaultStore()) {
            $adapter = $this->getConnection();
            $select = $adapter->select()->from(
                $this->getMainTable(),
                'location_id'
            )->where(
                'location_id <> ?',
                (int)$object->getId()
            );
            $currentLocationIds = $adapter->fetchCol($select);
            if ($currentLocationIds) {
                $currentLocationIds = implode(',', $currentLocationIds);
                $sql = "Update " . $this->getMainTable() . " Set is_default_store = '0' where location_id IN (" . $currentLocationIds . ')';
                $adapter->query($sql);
            }
        }

        $this->saveHolidayRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \Mageplaza\StoreLocator\Model\Location $location
     *
     * @return array
     */
    public function getHolidayIds(\Mageplaza\StoreLocator\Model\Location $location)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->_holidayLocationTable,
            'holiday_id'
        )
            ->where(
                'location_id = ?',
                (int)$location->getId()
            );

        return $adapter->fetchCol($select);
    }

    /**
     * @param \Mageplaza\StoreLocator\Model\Location $location
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveHolidayRelation(\Mageplaza\StoreLocator\Model\Location $location)
    {
        $location->setIsChangedHolidayList(false);
        $id = $location->getId();
        $holidays = $location->getHolidaysIds();

        if ($holidays === null) {
            if ($location->getIsHolidayGrid()) {
                $holidays = [];
            } else {
                return $this;
            }
        }

        $holidays = array_keys($holidays);
        $oldHolidays = $location->getHolidayIds();
        $insert = array_diff($holidays, $oldHolidays);
        $delete = array_diff($oldHolidays, $holidays);
        $adapter = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['holiday_id IN(?)' => $delete, 'location_id=?' => $id];
            $adapter->delete($this->_holidayLocationTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $holidayId) {
                $data[] = [
                    'location_id' => (int)$id,
                    'holiday_id'  => (int)$holidayId
                ];
            }
            $adapter->insertMultiple($this->_holidayLocationTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $holidayIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'mageplaza_storelocator_location_change_holidays',
                ['location' => $location, 'holiday_ids' => $holidayIds]
            );

            $location->setIsChangedHolidayList(true);
            $holidayIds = array_keys($insert + $delete);
            $location->setAffectedHolidayIds($holidayIds);
        }

        return $this;
    }

    /**
     * @param $urlKey
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isDuplicateUrlKey($urlKey)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'location_id')
            ->where('url_key = :url_key');
        $binds = ['url_key' => $urlKey];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param $locationId
     *
     * @return array
     */
    public function getHolidayIdsByLocation($locationId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->_holidayLocationTable,
            'holiday_id'
        )
            ->where('location_id = ?', $locationId);

        return $adapter->fetchCol($select);
    }
}
