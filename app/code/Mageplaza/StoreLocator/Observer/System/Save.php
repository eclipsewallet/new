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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Observer\System;

use Magento\Framework\Event\ObserverInterface;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\LocationFactory;

/**
 * Class Save
 * @package Mageplaza\StoreLocator\Observer\System
 */
class Save implements ObserverInterface
{
    /**
     * @var LocationFactory
     */
    protected $_locationFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Save constructor.
     *
     * @param LocationFactory $locationFactory
     * @param Data $helperData
     */
    public function __construct(
        LocationFactory $locationFactory,
        Data $helperData
    )
    {
        $this->_locationFactory = $locationFactory;
        $this->_helperData = $helperData;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $locationCollection = $this->_locationFactory->create()->getCollection();

        foreach ($locationCollection as $location) {
            if ($location->getIsConfigWebsite()) {
                $location->setWebsite($this->_helperData->getConfigGeneral('website'))->save();
            }
            if ($location->getIsConfigTimeZone()) {
                $location->setTimeZone($this->_helperData->getStoreTimeSetting('time_zone'))->save();
            }
            if ($location->getOperationMon()) {
                if (isset($this->_helperData->unserialize($location->getOperationMon())['use_system_config'])) {
                    $location->setOperationMon($this->_helperData->getConfigOpenTime(Data::MONDAY))->save();
                }
            }
            if ($location->getOperationTue()) {
                if (isset($this->_helperData->unserialize($location->getOperationTue())['use_system_config'])) {
                    $location->setOperationTue($this->_helperData->getConfigOpenTime(Data::TUESDAY))->save();
                }
            }
            if ($location->getOperationWed()) {
                if (isset($this->_helperData->unserialize($location->getOperationWed())['use_system_config'])) {
                    $location->setOperationWed($this->_helperData->getConfigOpenTime(Data::WEDNESDAY))->save();
                }
            }
            if ($location->getOperationThu()) {
                if (isset($this->_helperData->unserialize($location->getOperationThu())['use_system_config'])) {
                    $location->setOperationThu($this->_helperData->getConfigOpenTime(Data::THURSDAY))->save();
                }
            }
            if ($location->getOperationFri()) {
                if (isset($this->_helperData->unserialize($location->getOperationFri())['use_system_config'])) {
                    $location->setOperationFri($this->_helperData->getConfigOpenTime(Data::FRIDAY))->save();
                }
            }
            if ($location->getOperationSat()) {
                if (isset($this->_helperData->unserialize($location->getOperationSat())['use_system_config'])) {
                    $location->setOperationSat($this->_helperData->getConfigOpenTime(Data::SATURDAY))->save();
                }
            }
            if ($location->getOperationSun()) {
                if (isset($this->_helperData->unserialize($location->getOperationSun())['use_system_config'])) {
                    $location->setOperationSun($this->_helperData->getConfigOpenTime(Data::SUNDAY))->save();
                }
            }
        }
    }
}
