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

namespace Mageplaza\StoreLocator\Block;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Mageplaza\StoreLocator\Helper\Data as HelperData;
use Mageplaza\StoreLocator\Helper\Image as HelperImage;
use Mageplaza\StoreLocator\Model\Config\Source\System\MapStyle;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use Mageplaza\StoreLocator\Model\LocationFactory;

/**
 * Class Frontend
 * @package Mageplaza\StoreLocator\Block
 */
class Frontend extends Template implements BlockInterface
{
    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timeZone;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var LocationFactory
     */
    protected $locationFactory;

    /**
     * @var HolidayFactory
     */
    protected $holidayFactory;

    /**
     * @var HelperImage
     */
    protected $_helperImage;

    /**
     * @var BlockFactory
     */
    protected $_blockFactory;

    /**
     * Frontend constructor.
     *
     * @param Context $context
     * @param DateTime $dateTime
     * @param HelperData $helperData
     * @param LocationFactory $locationFactory
     * @param HolidayFactory $holidayFactory
     * @param HelperImage $helperImage
     * @param BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        HelperData $helperData,
        LocationFactory $locationFactory,
        HolidayFactory $holidayFactory,
        HelperImage $helperImage,
        BlockFactory $blockFactory,
        array $data = []
    )
    {
        $this->_dateTime = $dateTime;
        $this->_timeZone = $context->getLocaleDate();
        $this->_helperData = $helperData;
        $this->locationFactory = $locationFactory;
        $this->holidayFactory = $holidayFactory;
        $this->_helperImage = $helperImage;
        $this->_blockFactory = $blockFactory;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        if ($this->getRequest()->getFullActionName() != 'mpstorelocator_storelocator_store' &&
            $this->getRequest()->getFullActionName() != 'mpstorelocator_storelocator_view'
        ) {
            return parent::_prepareLayout();
        }

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->_storeManager->getStore()->getBaseUrl()
            ])
                ->addCrumb($this->_helperData->getRoute(), $this->getBreadcrumbsData());
        }

        $this->applySeoCode();

        return parent::_prepareLayout();
    }

    /**
     * @return $this
     */
    public function applySeoCode()
    {
        $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($this->getStoreLocatorTitle(true))));

        $description = $this->_helperData->getSeoSetting('meta_description');
        $this->pageConfig->setDescription($description);

        $keywords = $this->_helperData->getSeoSetting('meta_keywords');
        $this->pageConfig->setKeywords($keywords);

        return $this;
    }

    /**
     * Retrieve HTML title value separator (with space)
     *
     * @return string
     */
    public function getTitleSeparator()
    {
        $separator = (string)$this->_helperData->getConfigValue('catalog/seo/title_separator');

        return ' ' . $separator . ' ';
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getStoreLocatorTitle($meta = false)
    {
        $pageTitle = $this->_helperData->getPageTitle() ?: __('Find a store');
        if ($meta) {
            $title = $this->_helperData->getSeoSetting('meta_title') ?: $pageTitle;

            return [$title];
        }

        return $pageTitle;
    }

    /**
     * @return array
     */
    protected function getBreadcrumbsData()
    {
        $label = $this->_helperData->getPageTitle() ?: __('Find a store');

        $data = [
            'label' => $label,
            'title' => $label
        ];

        if ($this->getRequest()->getFullActionName() != 'mpstorelocator_storelocator_store' &&
            $this->getRequest()->getFullActionName() != 'mpstorelocator_storelocator_view'
        ) {
            $data['link'] = $this->_helperData->getStoreLocatorUrl();
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getLocationList()
    {
        $locations = $this->locationFactory->create()->getCollection()->addFieldToFilter('status', 1);
        $list = $this->filterLocation($locations);

        return $list;
    }

    /**
     * filter location by store
     *
     * @param $locations
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function filterLocation($locations)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $result = [];
        foreach ($locations as $location) {
            $locationStores = explode(',', $location->getStoreIds());
            if (in_array($storeId, $locationStores) || in_array(0, $locationStores)) {
                array_push($result, $location);
            }
        }

        return $result;
    }

    /**
     * Get Zoom default in configuration
     *
     * @return int|mixed
     */
    public function getZoom()
    {
        $zoom = $this->_helperData->getMapSetting('zoom_default');

        return $zoom ? $zoom : 12;
    }

    /**
     * get filter radius config
     *
     * @return array|bool
     */
    public function getFilterRadius()
    {
        $config = $this->_helperData->getMapSetting('filter_radius');

        return $config ? explode(',', $config) : false;
    }

    /**
     * convert km to miles
     *
     * @param $distance
     *
     * @return mixed
     */
    public function convertKmtoMiles($distance)
    {
        $config = $this->_helperData->getMapSetting('distance_unit');

        if ($config == 1) {
            return $distance;
        }

        return $distance * 0.621371;
    }

    /**
     * Get text Distance Unit
     *
     * @return string
     */
    public function getUnitText()
    {
        $config = $this->_helperData->getMapSetting('distance_unit');

        if ($config == 1) {
            return 'Miles';
        }

        return 'Km';
    }

    /**
     * @param $img
     *
     * @return string
     */
    /**
     * @param $img
     *
     * @return string
     */
    /**
     * @param $img
     *
     * @return string
     */
    /**
     * @param $img
     *
     * @return string
     */
    /**
     * @param $img
     *
     * @return string
     */
    public function getUrlImg($img)
    {
        return $this->getViewFileUrl('Mageplaza_StoreLocator::media/' . $img);
    }

    /**
     * get default radius filter config
     *
     * @return int|mixed
     */
    public function getDefaultRadius()
    {
        $config = $this->_helperData->getMapSetting('default_radius');

        return $config ? $config : 10000;
    }

    /**
     * @param $location
     *
     * @return array|string
     */
    public function getStoreImages($location)
    {
        $images = [];
        $imageJson = $location->getImages();
        if ($imageJson) {
            $images = HelperData::jsonDecode($imageJson);
        }

        return $images;
    }

    /**
     * @param $location
     *
     * @return array|mixed
     */
    public function getStoreMainImage($location)
    {
        $mainImage = [];
        $images = $this->getStoreImages($location);
        if ($images) {
            foreach ($images as $image) {
                if ($image['position'] = '1') {
                    $mainImage = $image;
                    break;
                }
            }
        }

        return $mainImage;
    }

    /**
     * @param $location
     *
     * @return string
     */
    public function getStoreMainImageUrl($location)
    {
        $mainImage = $this->getStoreMainImage($location);
        $url = (isset($mainImage['file'])) ? $this->resizeImage($mainImage['file'], '100x') : $this->getDefaultImgUrl();

        return $url;
    }

    /**
     * @param $location
     *
     * @return \Magento\Framework\Phrase|mixed
     */
    /**
     * @param $location
     *
     * @return \Magento\Framework\Phrase|mixed
     */
    /**
     * @param $location
     *
     * @return \Magento\Framework\Phrase|mixed
     */
    /**
     * @param $location
     *
     * @return \Magento\Framework\Phrase|mixed
     */
    /**
     * @param $location
     *
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getStoreMainImageAlt($location)
    {
        $alt = __('Store Image');
        $mainImage = $this->getStoreMainImage($location);
        if (isset($mainImage['label'])) {
            $alt = (!empty($mainImage['label'])) ? $mainImage['label'] : __('Store Image');
        }

        return $alt;
    }

    /**
     * Get Store Location by id
     *
     * @param $locationId
     *
     * @return \Mageplaza\StoreLocator\Model\Location
     */
    public function getStoreLocation($locationId)
    {
        return $this->locationFactory->create()->load($locationId);
    }

    /**
     * Check holiday of store location
     *
     * @param $holidayIds
     * @param $currentTime
     *
     * @return bool
     */
    public function checkHoliday($holidayIds, $currentTime)
    {
        foreach ($holidayIds as $holidayId) {
            $holiday = $this->holidayFactory->create()->load($holidayId);

            if ($holiday->getStatus() && $currentTime >= strtotime($holiday->getFrom()) && $currentTime <= strtotime($holiday->getTo())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get open/close notify for each story
     *
     * @param \Mageplaza\StoreLocator\Model\Location $location
     *
     * @return \Magento\Framework\Phrase
     * @throws \Zend_Serializer_Exception
     */
    public function getOpenCloseNotify($location)
    {
        $dateTime = (new \DateTime($this->_dateTime->date(), new \DateTimeZone('UTC')));
        $dateTime->setTimezone(new \DateTimeZone($location->getTimeZone()));
        $currentDayOfWeek = strtolower($dateTime->format('l'));
        $currentTime = strtotime($dateTime->format('H:i'));
        $holidayIds = $this->locationFactory->create()->getResource()->getHolidayIdsByLocation($location->getLocationId());

        if ($this->checkHoliday($holidayIds, $currentTime)) {
            $result = 'Closed';
        } else {
            switch ($currentDayOfWeek) {
                case HelperData::MONDAY:
                    $openTime = $location->getOperationMon();
                    $result = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::TUESDAY:
                    $openTime = $location->getOperationTue();
                    $result = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::WEDNESDAY:
                    $openTime = $location->getOperationWed();
                    $result = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::THURSDAY:
                    $openTime = $location->getOperationThu();
                    $result = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::FRIDAY:
                    $openTime = $location->getOperationFri();
                    $result = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::SATURDAY:
                    $openTime = $location->getOperationSat();
                    $result = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                default:
                    $openTime = $location->getOperationSun();
                    $result = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
            }
        }

        return $result;
    }

    /**
     * Get open/close time alert for each day in week
     *
     * @param $dayOpenTime
     * @param $currentTime
     *
     * @return \Magento\Framework\Phrase
     * @throws \Zend_Serializer_Exception
     */
    public function getOpenCloseTime($dayOpenTime, $currentTime)
    {
        $openTime = $this->_helperData->unserialize($dayOpenTime);
        $unit = ((float)$openTime['from'][0] > 12) ? 'PM' : 'AM';

        $fromTime = $openTime['from'][0] . ':' . $openTime['from'][1];
        $toTime = $openTime['to'][0] . ':' . $openTime['to'][1];

        if ($openTime['value']) {
            if ($currentTime >= strtotime($fromTime) && $currentTime <= strtotime($toTime)) {
                $result = __('Open now: ' . $fromTime . '-' . $toTime);
            } else {
                $result = __('Open at ' . $fromTime . ' ' . $unit);
            }
        } else {
            $result = __('Open at ' . $fromTime . $unit);
        }

        return $result;
    }

    /**
     * Resize Image Function
     *
     * @param $image
     * @param null $size
     * @param string $type
     *
     * @return string
     */
    public function resizeImage($image, $size = null, $type = '')
    {
        if (!$image) {
            return $this->getDefaultImageUrl();
        }

        return $this->_helperImage->resizeImage($image, $size, $type);
    }

    /**
     * JSON data locations
     *
     * @return string
     * @throws \Zend_Serializer_Exception
     */
    public function getDataLocations()
    {
        $locations = $this->getLocationList();
        $locationsData = [];

        foreach ($locations as $location) {
            $locationsData[] = [
                'id'          => $location->getLocationId(),
                'lat'         => $location->getLatitude(),
                'lng'         => $location->getLongitude(),
                'name'        => $location->getName(),
                'street'      => $location->getStreet(),
                'state'       => $location->getStateProvince(),
                'city'        => $location->getCity(),
                'country'     => $location->getCountry(),
                'postal'      => $location->getPostalCode(),
                'phone1'      => $location->getPhoneOne(),
                'phone2'      => $location->getPhoneTwo(),
                'web'         => $location->getWebsite(),
                'time'        => $this->getOpenCloseNotify($location),
                'image'       => $this->getStoreMainImageUrl($location),
                'fax'         => $location->getFax(),
                'mail'        => $location->getEmail(),
                'description' => $location->getDescription(),
                'markerUrl'   => $this->getMakerIconUrl(),
                "category"    => "Restaurant",
                "address"     => $location->getStreet(),
                "address2"    => "",
                "url"         => $location->getUrlKey()
            ];
        }

        return HelperData::jsonEncode($locationsData);
    }

    /**
     * Get Data config location
     * @return string
     */
    public function getDataConfigLocation()
    {
        $defaultStore = $this->_helperData->getDefaultStoreLocation();
        $defaultLat = null;
        $defaultLng = null;

        if ($defaultStore) {
            $defaultLat = $defaultStore->getLatitude();
            $defaultLng = $defaultStore->getLongitude();
        }

        $data = [
            'zoom'                      => $this->getZoom(),
            'markerIcon'                => $this->getMakerIconUrl(),
            'dataLocations'             => $this->getUrl('mpstorelocator/storelocator/locationsdata'),
            'infowindowTemplatePath'    => $this->getViewFileUrl('Mageplaza_StoreLocator::templates/infowindow-description.html'),
            'listTemplatePath'          => $this->getViewFileUrl('Mageplaza_StoreLocator::templates/location-list-description.html'),
            'KMLinfowindowTemplatePath' => $this->getViewFileUrl('Mageplaza_StoreLocator::templates/kml-infowindow-description.html'),
            'KMLlistTemplatePath'       => $this->getViewFileUrl('Mageplaza_StoreLocator::templates/kml-location-list-description.html'),
            'isFilter'                  => $this->isFilter(),
            'isFilterRadius'            => $this->isEnableFilterRadius(),
            'locationIdDetail'          => $this->_helperData->getLocationIdFromRouter(),
            'urlSuffix'                 => $this->_helperData->getUrlSuffix(),
            'router'                    => $this->_helperData->getRoute(),
            'isDefaultStore'            => $this->checkIsDefaultStore(),
            'defaultLat'                => $defaultLat,
            'defaultLng'                => $defaultLng
        ];

        return HelperData::jsonEncode($data);
    }

    /**
     * @return bool
     */
    public function checkIsDefaultStore()
    {
        if ($this->_helperData->getDefaultStoreLocation()) {
            return true;
        }

        return false;
    }

    /**
     * Get Url marker Icon
     *
     * @return string
     */
    public function getMakerIconUrl()
    {
        if ($this->_helperData->getMapSetting('marker_icon')) {
            return $this->_helperImage->getBaseMediaUrl() . '/' . $this->_helperImage->getMediaPath($this->_helperData->getMapSetting('marker_icon'), 'marker_icon');
        }

        return $this->getUrlImg('marker.png');
    }

    /**
     * Get Default Img Url
     *
     * @return string
     */
    public function getDefaultImgUrl()
    {
        if ($this->_helperData->getConfigGeneral('upload_default_image')) {
            return $this->_helperImage->getBaseMediaUrl() . '/' . $this->_helperImage->getMediaPath($this->_helperData->getConfigGeneral('upload_default_image'), 'image');
        }

        return $this->getUrlImg('defaultImg.png');
    }

    /**
     * Get first store
     *
     * @return \Magento\Framework\DataObject
     */
    public function getFirstStore()
    {
        return $this->locationFactory->create()->getCollection()->getFirstItem();
    }

    /**
     * @param $storeDay
     *
     * @return string
     * @throws \Zend_Serializer_Exception
     */
    public function getDayTime($storeDay)
    {
        $openTime = $this->_helperData->unserialize($storeDay);
        $fromTime = $openTime['from'][0] . ':' . $openTime['from'][1];
        $toTime = $openTime['to'][0] . ':' . $openTime['to'][1];
        if ($openTime['value']) {
            $result = $fromTime . ' - ' . $toTime;
        } else {
            $result = __('<span style="color: red">Closed</span>');
        }

        return $result;
    }

    /**
     * @param $location
     *
     * @return string
     * @throws \Exception
     */
    public function getCurrentDay($location)
    {
        $dateTime = (new \DateTime($this->_dateTime->date(), new \DateTimeZone('UTC')));
        $dateTime->setTimezone(new \DateTimeZone($location->getTimeZone()));
        $currentDayOfWeek = strtolower($dateTime->format('l'));

        return $currentDayOfWeek;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMapTemplate()
    {
        $mapType = $this->_helperData->getMapSetting('style');

        if ($mapType == MapStyle::STYLE_DEFAULT) {
            return '[]';
        }
        if ($mapType == MapStyle::STYLE_CUSTOM) {
            return $this->_helperData->getMapSetting('custom_style');
        }

        return $this->_helperData->getMapTheme($mapType);
    }

    /**
     * get config Filter by current position
     *
     * @return bool
     */
    public function isEnableFilterRadius()
    {
        if ($this->_helperData->getConfigGeneral('filter_store/current_position') == 1) {
            return true;
        }

        return false;
    }

    /**
     * get config filter store
     *
     * @return bool
     */
    public function isFilter()
    {
        if ($this->_helperData->getConfigGeneral('filter_store/enabled') == 1) {
            return true;
        }

        return false;
    }
}
