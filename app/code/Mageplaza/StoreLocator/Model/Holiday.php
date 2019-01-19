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
 * Class Holiday
 *
 * @method Holiday setLocationsIds(array $data)
 * @method Holiday setIsChangedLocationList(\bool $flag)
 * @method Holiday setIsLocationGrid(\bool $flag)
 * @method Holiday setAffectedLocationIds(array $ids)
 * @method array getLocationsIds()
 * @method bool getIsLocationGrid()
 *
 * @package Mageplaza\StoreLocator\Model
 */
class Holiday extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_storelocator_holiday';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_storelocator_holiday';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_storelocator_holiday';

    /**
     * @var string
     */
    protected $_idFieldName = 'holiday_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\StoreLocator\Model\ResourceModel\Holiday');
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
    public function getLocationIds()
    {
        if (!$this->hasData('location_ids')) {
            $ids = $this->_getResource()->getLocationIds($this);
            $this->setData('location_ids', $ids);
        }

        return (array)$this->_getData('location_ids');
    }
}
