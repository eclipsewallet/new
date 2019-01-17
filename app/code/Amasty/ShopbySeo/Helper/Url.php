<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Helper;

use Amasty\Shopby\Helper\Category;
use Amasty\Shopby\Model\ResourceModel\Catalog\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\ScopeInterface;
use Amasty\ShopbyBase\Helper\Data as BaseData;

/**
 * Class Url
 * @package Amasty\ShopbySeo\Helper
 */
class Url extends AbstractHelper
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var bool
     */
    private $isBrandFilterActive;

    /**
     * @var array
     */
    private $originalParts;

    /**
     * @var array
     */
    private $query;

    /**
     * @var string
     */
    private $paramsDelimiterCurrent;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var string[]
     */
    private $categoryUrls;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Shopby\Model\Layer\Cms\Manager
     */
    private $cmsManager;

    /**
     * @var  \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    private $settingHelper;

    /**
     * @var string
     */
    private $aliasDelimiter;

    /**
     * @var string
     */
    private $rootRoute;

    /**
     * @var null
     */
    private $brandAttributeCode;

    /**
     * @var bool|null
     */
    private $isAddSuffixToShopby;

    /**
     * @var string
     */
    private $brandUrlKey;

    /**
     * @var int[]
     */
    private $filterPositions;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $shopbyRequest;

    /**
     * @var string[]
     */
    private $intoCategoryModules;

    /**
     * @var BaseData
     */
    private $baseHelper;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    private $urlFinder;

    public function __construct(
        Context $context,
        Data $helper,
        CategoryCollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\Shopby\Model\Layer\Cms\Manager $cmsManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Amasty\ShopbyBase\Helper\Data $baseHelper,
        \Amasty\ShopbyBase\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        array $intoCategoryModules = ['catalog', 'amshopby', 'cms'] //skip ambrand
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->shopbyRequest = $shopbyRequest;
        $this->moduleManager = $context->getModuleManager();
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->cmsManager = $cmsManager;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        $this->baseHelper = $baseHelper;
        $this->settingHelper = $settingHelper;
        $this->intoCategoryModules = $intoCategoryModules;
        $this->urlFinder = $urlFinder;
        $this->aliasDelimiter =
            $this->scopeConfig->getValue('amasty_shopby_seo/url/option_separator', ScopeInterface::SCOPE_STORE);
        $this->rootRoute = trim($this->scopeConfig->getValue('amshopby_root/general/url', ScopeInterface::SCOPE_STORE));
        $this->brandAttributeCode = $this->moduleManager->isEnabled('Amasty_ShopbyBrand')
        && $this->scopeConfig->getValue('amshopby_brand/general/attribute_code', ScopeInterface::SCOPE_STORE)
            ? $this->scopeConfig->getValue('amshopby_brand/general/attribute_code', ScopeInterface::SCOPE_STORE) : null;
        $this->brandUrlKey =
            trim($this->scopeConfig->getValue('amshopby_brand/general/url_key', ScopeInterface::SCOPE_STORE));
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_getRequest();
    }

    /**
     * @param string $url
     * @return string
     */
    public function seofyUrl($url)
    {
        if (!$this->isSeoUrlEnabled()
            || !$this->initialize($url)
            || $this->cmsManager->isCmsPageNavigation()
        ) {
            return $url;
        }
        $this->query = $this->parseQuery();


        $routeUrl = $this->originalParts['route'];
        if ($this->isIntoCategory()) {
            $routeUrl = $this->getCategoryRouteUrl() ?: $routeUrl;
        }

        $routeUrlTrimmed = $this->removeCategorySuffix($routeUrl);
        $endsWithLine = strlen($routeUrlTrimmed)
            && $routeUrlTrimmed[strlen($routeUrlTrimmed) - 1] == DIRECTORY_SEPARATOR;
        if ($endsWithLine) {
            //if routeUrl is valid Magento route
            return $url;
        }

        $moveSuffix = $routeUrlTrimmed != $routeUrl;
        $resultPath = $routeUrlTrimmed;

        $seoAliases = $this->cutAliases();
        $seoAliasesInUrl = $this->getSeoAliasesInUrl($resultPath);
        foreach ($seoAliases as $attributeCode => $aliases) {
            foreach ($aliases as $key => $alias) {
                if (isset($seoAliasesInUrl[$alias])) {
                    unset($seoAliases[$attributeCode]);
                }
            }
        }

        if ($seoAliases) {
            $resultPath = $this->injectAliases($resultPath, $seoAliases);
        }

        $resultPath = $this->cutReplaceExtraShopby($resultPath);
        $resultPath = ltrim($resultPath, DIRECTORY_SEPARATOR);

        if ($moveSuffix || $this->isAddSuffixToShopby()) {
            $resultPath = $this->addCategorySuffix($resultPath);
        }

        $result = $this->query ? ($resultPath . '?' . $this->query2Params($this->query)) : $resultPath;

        if ($this->originalParts['hash']) {
            $result .= '#' . $this->originalParts['hash'];
        }

        return $this->originalParts['domain'] . $result;
    }

    /**
     * @param $path
     * @return array
     */
    private function getSeoAliasesInUrl($path)
    {
        $path = $this->helper->getFilterWord() ? substr($path, strripos($path, '/') + 1) : $path;

        return array_flip(explode($this->helper->getOptionSeparator(), $path));
    }

    /**
     * @return bool
     */
    private function isIntoCategory()
    {
        $moduleName = $this->_getRequest()->getModuleName();
        $settingCategory = $this->settingHelper->getSettingByAttributeCode(Category::ATTRIBUTE_CODE);
        return
            isset($this->query['cat'])
            && in_array($moduleName, $this->intoCategoryModules)
            && !$settingCategory->isMultiselect();
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function initialize($url)
    {
        $this->originalParts = [];
        if ($this->_request->getModuleName() == 'catalogsearch') {
            return false;
        }
        /**
         * TODO: this code do not execute now. Maybe it is not necessary
         */
        $url = str_replace('amshopby/index/index/', $this->rootRoute, $url);

        if (!preg_match('@^([^/]*//[^/]*/)(.*)$@', $url)) {
            return false;
        }

        $parsedUrl = parse_url($url);
        $this->originalParts['domain'] = $this->storeManager->getStore()->getBaseUrl();
        $route = isset($parsedUrl['path']) ? trim($parsedUrl['path'], '/') : null;
        if ($this->scopeConfig->isSetFlag('web/url/use_store', ScopeInterface::SCOPE_STORE)) {
            $storeCode = $this->storeManager->getStore()->getCode();
            if(strpos($route, $storeCode) === 0) {
                $route = substr($route, strlen($storeCode) + 1);
            }
        }
        $this->originalParts['route'] = $this->normalizeRoute($route);

        if (strpos($this->originalParts['route'], 'media/') !== false) {
            return false;
        }

        if ($this->originalParts['route'] !== null) {
            $routeBaseUrl = parse_url($this->originalParts['domain'], PHP_URL_PATH);
            if (strpos($this->originalParts['route'], $routeBaseUrl) === 0) {
                $this->originalParts['route'] = substr($this->originalParts['route'], strlen($routeBaseUrl));
                if (empty($this->originalParts['route'])) {
                    $this->originalParts['route'] = null;
                }
            }
        }

        $this->originalParts['params'] = isset($parsedUrl['query']) ? $parsedUrl['query'] : null;
        $this->originalParts['hash'] = isset($parsedUrl['fragment']) ? $parsedUrl['fragment'] : null;

        $delimiterEscaped = isset($parsedUrl['query']) && strpos($parsedUrl['query'], '&amp;') !== false;
        $this->paramsDelimiterCurrent = $delimiterEscaped ? '&amp;' : '&';

        return true;
    }

    /**
     * @param string $route
     * @return string
     */
    private function normalizeRoute($route)
    {
        if ($subPath = $this->getBaseUrlSubPath()) {
            if (strpos($route, $subPath) !== false) {
                return substr($route, strlen($subPath) + 1);
            }
        }

        return $route;
    }

    /**
     * @return string
     */
    private function getBaseUrlSubPath()
    {
        $parsedBaseUrl = parse_url(trim($this->storeManager->getStore()->getBaseUrl(), '/'));
        return isset($parsedBaseUrl['path']) ? trim($parsedBaseUrl['path'], '/') : '';
    }

    /**
     * @return array
     */
    protected function parseQuery()
    {
        $query = [];
        $this->isBrandFilterActive = false;
        if (!isset($this->originalParts['params'])) {
            return $query;
        }

        $parts = explode($this->paramsDelimiterCurrent, $this->originalParts['params']);
        if ($parts) {
            foreach ($parts as $part) {
                $param = explode('=', $part, 2);
                if (count($param) != 2) {
                    continue;
                }

                $paramName = $param[0];
                $value = $param[1];
                $query[$paramName] = $value;
                if ($this->brandAttributeCode === $paramName) {
                    $this->isBrandFilterActive = true;
                }
            }
        } else {
            foreach ($this->shopbyRequest->getRequestParams() as $name => $values) {
                $query[$name] = $values;
                if ($this->brandAttributeCode === $name) {
                    $this->isBrandFilterActive = true;
                }
            }
        }

        return $query;
    }

    /**
     * @return string
     */
    public function getCategoryRouteUrl()
    {
        $categoryId = isset($this->query['cat']) ?$this->query['cat']: null;
        return $this->getCategoryUrlById($categoryId);
    }

    /**
     * @param string|null $categoryId
     * @return string
     */
    private function getCategoryUrlById($categoryId)
    {
        if (!$categoryId) {
            return '';
        }

        if ($this->categoryUrls === null) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addUrlRewriteToResult();
            $select = $collection->getSelect();
            $select->reset('columns');
            $select->columns('entity_id');
            $urlRewriteTable = $this->resource->getTableName('url_rewrite');
            $select->columns($urlRewriteTable . '.request_path');
            $this->categoryUrls = $select->getAdapter()->fetchPairs($select);
        }

        return isset($this->categoryUrls[$categoryId]) ? $this->categoryUrls[$categoryId] : '';
    }

    /**
     * @return array|mixed|null
     */
    private function getOptionsSeoData()
    {
        $attributeOptionsData = $this->helper->getOptionsSeoData();
        return $attributeOptionsData;
    }

    /**
     * @return array
     */
    protected function cutAliases()
    {
        $attributeOptionsData = $this->getOptionsSeoData();

        $aliasesByCode = [];
        $brandAliases = [];
        foreach ($this->query as $paramName => $rawValues) {
            if ($this->isParamSeoSignificant($paramName) && isset($attributeOptionsData[$paramName])) {
                $optionsData = $attributeOptionsData[$paramName];
                $rawValues = explode(',', str_replace('%2C', ',', $rawValues));
                if (is_array($rawValues)) {
                    foreach ($rawValues as $value) {
                        if (!array_key_exists($value, $optionsData)) {
                            continue;
                        }
                        $alias = $optionsData[$value];
                        if ($paramName == $this->brandAttributeCode) {
                            $brandAliases[$paramName][] = $alias;
                        } else {
                            if (array_key_exists($paramName, $aliasesByCode)) {
                                $aliasesByCode[$paramName][] = $alias;
                            } else {
                                $aliasesByCode[$paramName] = [$alias];
                            }
                        }
                    }
                } elseif (array_key_exists($rawValues, $optionsData)) {
                    $alias = $optionsData[$rawValues];
                    if ($paramName == $this->brandAttributeCode) {
                        $brandAliases[$paramName][] = $alias;
                    } else {
                        if (array_key_exists($paramName, $aliasesByCode)) {
                            $aliasesByCode[$paramName][] = $alias;
                        } else {
                            $aliasesByCode[$paramName] = [$alias];
                        }
                    }
                }

                foreach ($attributeOptionsData as $key => $optionValue) {
                    if (isset($this->query[$key])) {
                        unset($this->query[$key]);
                    }
                }
            }
        }

        $this->sortAliases($aliasesByCode);

        $aliases = $this->mergeAliases($brandAliases, $aliasesByCode);

        return $aliases;
    }

    /**
     * @param array $seoAliases
     */
    private function sortAliases(&$seoAliases)
    {
        $filterPositions = $this->getFilterPositions();
        uksort($seoAliases, function ($first, $second) use ($filterPositions) {
            if ($first == $second) {
                return 0;
            }

            if (!isset($filterPositions[$first])) {
                return 1;
            }

            if (!isset($filterPositions[$second])) {
                return -1;
            }

            return $filterPositions[$first] - $filterPositions[$second];
        });
    }

    /**
     * @return int[]|null
     */
    private function getFilterPositions()
    {
        if ($this->filterPositions === null) {
            $allFilters = $this->coreRegistry->registry(\Amasty\Shopby\Model\Layer\FilterList::ALL_FILTERS_KEY);

            if (!$allFilters) {
                return null;
            }

            $this->filterPositions = [];
            $position = 0;

            foreach ($allFilters as $filter) {
                $code = $filter->getRequestVar();
                $this->filterPositions[$code] = $position;
                $position++;
            }
        }

        return $this->filterPositions;
    }

    /**
     * @param string[] $brandAliases
     * @param string[][] $aliasesByCode
     * @return array
     */
    private function mergeAliases($brandAliases, $aliasesByCode)
    {
        $result = array_merge($brandAliases, $aliasesByCode);

        return $result;
    }

    /**
     * @param string $paramName
     * @return bool
     */
    protected function isParamSeoSignificant($paramName)
    {
        $seoList = $this->helper->getSeoSignificantAttributeCodes();

        return in_array($paramName, $seoList);
    }

    /**
     * @param $routeUrl
     * @param array $aliases
     * @return string
     */
    protected function injectAliases($routeUrl, array $aliases)
    {
        $result = $routeUrl;
        if ($aliases) {
            $filterWord = $this->helper->getFilterWord() ? $this->helper->getFilterWord() . DIRECTORY_SEPARATOR : '';

            if (isset($aliases[$this->brandAttributeCode])
                && $this->coreRegistry->registry(BaseData::SHOPBY_CATEGORY_INDEX)
            ) {
                $result .= DIRECTORY_SEPARATOR . implode($this->aliasDelimiter, $aliases[$this->brandAttributeCode]);
                unset($aliases[$this->brandAttributeCode]);
            }

            if (count($aliases) > 0) {
                $result .= DIRECTORY_SEPARATOR . $filterWord;
            }

            $isFirstAlias = true;
            foreach ($aliases as $code => $alias) {
                $delimiter = $isFirstAlias ? '' : $this->aliasDelimiter;
                if (!$this->helper->isIncludeAttributeName()) {
                    $result .= $delimiter . implode($this->aliasDelimiter, $alias);
                } else {
                    $result .= $delimiter . $code . $this->aliasDelimiter . implode($this->aliasDelimiter, $alias);
                }

                $isFirstAlias = false;
            }
        }

        return $result;
    }

    /**
     * @param string $url
     * @return bool|string
     */
    protected function cutReplaceExtraShopby($url)
    {
        $cut = false;
        $allProductsEnabled =
            $this->scopeConfig->isSetFlag('amshopby_root/general/enabled', ScopeInterface::SCOPE_STORE);
        if ($allProductsEnabled || $this->moduleManager->isEnabled('Amasty_ShopbyBrand')) {
            $l = strlen($this->rootRoute);
            if (substr($url, 0, $l) == $this->rootRoute
                && strlen($url) > $l
                && $url[$l] == DIRECTORY_SEPARATOR
            ) {
                $url = substr($url, strlen($this->rootRoute));
                $cut = true;
            }
        }

        if ($cut) {
            if ($this->isBrandFilterActive) {
                $url = $this->brandUrlKey . $url;
            }
        }
        return $url;
    }

    /**
     * @param array $query
     * @return string
     */
    protected function query2Params($query)
    {
        $result = [];
        foreach ($query as $code => $value) {
            $result[] = $code . '=' . $value;
        }
        return implode($this->paramsDelimiterCurrent, $result);
    }

    /**
     * @param string $url
     * @return string
     */
    public function addCategorySuffix($url)
    {
        $suffix = $this->getSeoSuffix();
        if (strlen($suffix)) {
            $url .= $suffix;
        }
        return $url;
    }

    /**
     * @param string $url
     * @return bool|string
     */
    public function removeCategorySuffix($url)
    {
        $suffix = $this->getSeoSuffix();
        if ($this->coreRegistry->registry(BaseData::SHOPBY_CATEGORY_INDEX) && $this->query) {
            if (strlen($suffix)) {
                $p = strrpos($this->rootRoute, $suffix);
                if ($p !== false && $p == strlen($this->rootRoute) - strlen($suffix)) {
                    return $url;
                }
            }
        }
        if (strlen($suffix)) {
            $p = strrpos($url, $suffix);
            if ($p !== false && $p == strlen($url) - strlen($suffix)) {
                $url = substr($url, 0, $p);
            }
        }
        return $url;
    }

    /**
     * @return bool
     */
    public function isSeoUrlEnabled()
    {
        return !!$this->scopeConfig->getValue('amasty_shopby_seo/url/mode', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isAddSuffixToShopby()
    {
        if ($this->isAddSuffixToShopby === null) {
            $moduleName = $this->_getRequest()->getModuleName();
            $isOnProperPage = in_array($moduleName, ['ambrand', 'amshopby', null], true);
            $addSuffixFlag = $this->scopeConfig->isSetFlag(
                'amasty_shopby_seo/url/add_suffix_shopby',
                ScopeInterface::SCOPE_STORE
            );

            $result = $addSuffixFlag && $isOnProperPage && strlen($this->getSeoSuffix());

            if ($moduleName === null) { //early call - don't save the field
                return $result;
            }

            $this->isAddSuffixToShopby = $result;
        }

        return $this->isAddSuffixToShopby;
    }

    /**
     * @return string
     */
    public function getSeoSuffix()
    {
        return (string)$this->scopeConfig
            ->getValue('catalog/seo/category_url_suffix', ScopeInterface::SCOPE_STORE);
    }
}
