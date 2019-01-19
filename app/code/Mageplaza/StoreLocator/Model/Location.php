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

namespace Mageplaza\StoreLocator\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Location
 *
 * @method Location setHolidaysIds(array $data)
 * @method Location setIsChangedHolidayList(\bool $flag)
 * @method Location setIsHolidayGrid(\bool $flag)
 * @method Location setAffectedHolidayIds(array $ids)
 * @method array getHolidaysIds()
 * @method bool getIsHolidayGrid()
 *
 * @package Mageplaza\StoreLocator\Model
 */
class Location extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_storelocator_location';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_storelocator_location';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_storelocator_location';

    /**
     * @var string
     */
    protected $_idFieldName = 'location_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\StoreLocator\Model\ResourceModel\Location');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHolidayIds()
    {
        if (!$this->hasData('holiday_ids')) {
            $ids = $this->_getResource()->getHolidayIds($this);
            $this->setData('holiday_ids', $ids);
        }

        return (array)$this->_getData('holiday_ids');
    }
}
