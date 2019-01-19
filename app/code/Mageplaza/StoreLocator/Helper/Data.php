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

namespace Mageplaza\StoreLocator\Helper;

use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\StoreLocator\Model\LocationFactory;

/**
 * Class Data
 * @package Mageplaza\StoreLocator\Helper
 */
class Data extends AbstractData
{
    const MONDAY = 'monday';

    const TUESDAY = 'tuesday';

    const WEDNESDAY = 'wednesday';

    const THURSDAY = 'thursday';

    const FRIDAY = 'friday';

    const SATURDAY = 'saturday';

    const SUNDAY = 'sunday';

    const CONFIG_MODULE_PATH = 'storelocator';

    /**
     * @var TranslitUrl
     */
    protected $_translitUrl;

    /**
     * Parent layout of the block
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var Filesystem
     */
    protected $_fileSystem;

    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * @var LocationFactory
     */
    protected $locationFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TranslitUrl $translitUrl
     * @param LayoutInterface $layout
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param LocationFactory $locationFactory
     * @param Image $imageHelper
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TranslitUrl $translitUrl,
        LayoutInterface $layout,
        Filesystem $filesystem,
        DirectoryList $directoryList,
        LocationFactory $locationFactory,
        Image $imageHelper
    )
    {
        $this->_translitUrl = $translitUrl;
        $this->_layout = $layout;
        $this->_fileSystem = $filesystem;
        $this->_directoryList = $directoryList;
        $this->_imageHelper = $imageHelper;
        $this->locationFactory = $locationFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Get Map Setting in Configuration
     *
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getMapSetting($code, $storeId = null)
    {
        return $this->getModuleConfig('map_setting/' . $code, $storeId);
    }

    /**
     * Get Store Time Setting in Configuration
     *
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getStoreTimeSetting($code, $storeId = null)
    {
        return $this->getModuleConfig('time_default/' . $code, $storeId);
    }

    /**
     * Get Seo Setting in Configuration
     *
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSeoSetting($code, $storeId = null)
    {
        return $this->getModuleConfig('seo/' . $code, $storeId);
    }

    /**
     * Get page title confituration
     *
     * @return mixed
     */
    public function getPageTitle()
    {
        return ($this->getConfigGeneral('title')) ?: __('Find a store');
    }

    /**
     * Get page url
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPageUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        return $baseUrl . $this->getRoute() . $this->getUrlSuffix();
    }

    /**
     * get url key configuration
     *
     * @return mixed
     */
    public function getRoute()
    {
        return ($this->getConfigGeneral('url_key')) ?: 'find-a-store';
    }

    /**
     * @param null $urlKey
     * @param null $type
     *
     * @return string
     */
    public function getStoreLocatorUrl($urlKey = null, $type = null)
    {
        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url = $this->getUrl($this->getRoute() . '/' . $urlKey);
        $url = explode('?', $url);
        $url = $url[0];

        return rtrim($url, '/');
    }

    /**
     * @param $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl($route, $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * Check position can show link
     *
     * @param $position
     *
     * @return bool
     */
    public function canShowLink($position)
    {
        $positionConfig = explode(',', $this->getConfigGeneral('show_on'));

        return in_array($position, $positionConfig);
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getUrlSuffix($storeId = null)
    {
        return $this->scopeConfig->getValue(
            CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Generate url_key for post, tag, topic, category, author
     *
     * @param $resource
     * @param $object
     * @param $name
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateUrlKey($resource, $object, $name)
    {
        $attempt = -1;
        do {
            if ($attempt++ >= 10) {
                throw new LocalizedException(__('Unable to generate url key. Please check the setting and try again.'));
            }

            $urlKey = $this->_translitUrl->filter($name);
            if ($urlKey) {
                $urlKey = $urlKey . ($attempt ?: '');
            }
        } while ($this->checkUrlKey($resource, $object, $urlKey));

        return $urlKey;
    }

    /**
     * @param $resource
     * @param $object
     * @param $urlKey
     *
     * @return bool
     */
    public function checkUrlKey($resource, $object, $urlKey)
    {
        if (empty($urlKey)) {
            return true;
        }

        $adapter = $resource->getConnection();
        $select = $adapter->select()
            ->from($resource->getMainTable(), '*')
            ->where('url_key = :url_key');

        $binds = ['url_key' => (string)$urlKey];

        if ($id = $object->getId()) {
            $select->where($resource->getIdFieldName() . ' != :object_id');
            $binds['object_id'] = (int)$id;
        }

        $result = $adapter->fetchOne($select, $binds);

        return $result;
    }

    /**
     * @param $code
     *
     * @return mixed
     * @throws \Zend_Serializer_Exception
     */
    public function getConfigOpenTime($code)
    {
        $unserializeData = $this->unserialize($this->getStoreTimeSetting($code));
        $unserializeData[$code]['use_system_config'] = 1;
        $unserializeData = $unserializeData[$code];

        return $this->serialize($unserializeData);
    }

    /**
     * @param $templateId
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getDefaultTemplateHtml($templateId)
    {
        return $this->readFile($this->getTemplatePath($templateId));
    }

    /**
     * @param $relativePath
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function readFile($relativePath)
    {
        $rootDirectory = $this->_fileSystem->getDirectoryRead(DirectoryList::ROOT);

        return $rootDirectory->readFile($relativePath);
    }

    /**
     * @param $templateId
     * @param string $type
     * @param string $subPath
     *
     * @return string
     */
    public function getTemplatePath($templateId, $type = '.html', $subPath = 'view/base/templates/default/static-block/')
    {
        /** Get directory of Data.php */
        $currentDir = __DIR__;

        /** Get root directory(path of magento's project folder) */
        $rootPath = $this->_directoryList->getRoot();

        $currentDirArr = explode('\\', $currentDir);
        if (count($currentDirArr) == 1) {
            $currentDirArr = explode('/', $currentDir);
        }

        $rootPathArr = explode('/', $rootPath);
        if (count($rootPathArr) == 1) {
            $rootPathArr = explode('\\', $rootPath);
        }

        $basePath = '';
        for ($i = count($rootPathArr); $i < count($currentDirArr) - 1; $i++) {
            $basePath .= $currentDirArr[$i] . '/';
        }

        $templatePath = $basePath . $subPath;

        return $templatePath . $templateId . $type;
    }

    /**
     * Get locationId from Router
     *
     * @return bool|mixed
     */
    public function getLocationIdFromRouter()
    {
        $identifier = trim($this->_request->getPathInfo(), '/');
        $pos = strpos($identifier, 'mpstorelocator/storelocator/view');

        if ($pos !== false) {
            $element = explode('/', $identifier);

            return array_pop($element);
        }

        return false;
    }

    /**
     * Get location by Url key
     *
     * @param $url
     *
     * @return \Mageplaza\StoreLocator\Model\Location
     */
    public function getLocationByUrl($url)
    {
        $location = $this->locationFactory->create()->load($url, 'url_key');

        return $location;
    }

    /**
     * Get all store locator url
     *
     * @return array
     */
    public function getAllLocationUrl()
    {
        $urls = [0 => $this->getRoute()];
        $locations = $this->locationFactory->create()->getCollection();

        foreach ($locations as $location) {
            $urls[] = $location->getUrlKey();
        }

        return $urls;
    }

    /**
     * @param $fileName
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMapTheme($fileName)
    {
        return $this->readFile($this->getTemplatePath($fileName, '.json', 'view/base/web/map-style/'));
    }

    /**
     * @param $priority
     * @param $message
     *
     * @return string
     */
    public function getMessagesHtml($priority, $message)
    {
        /** @var $messagesBlock \Magento\Framework\View\Element\Messages */
        $messagesBlock = $this->_layout->createBlock(\Magento\Framework\View\Element\Messages::class);
        $messagesBlock->{$priority}(__($message));

        return $messagesBlock->toHtml();
    }

    /**
     * Get location default store
     *
     * @return null
     */
    public function getDefaultStoreLocation()
    {
        $locations = $this->locationFactory->create()->getCollection()->setOrder('sort_order', 'ASC');

        foreach ($locations as $location) {
            if ($location->getIsDefaultStore() == 1) {
                return $location;
            }
        }

        return null;
    }
}