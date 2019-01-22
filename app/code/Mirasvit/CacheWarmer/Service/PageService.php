<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.12
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Service;

use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\PageCache\Cache;
use Magento\Framework\App\PageCache\Identifier as CacheIdentifier;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Registry;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Data\PageTypeInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageTypeRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Service\FilterServiceInterface;
use Mirasvit\CacheWarmer\Api\Service\PageServiceInterface;
use Mirasvit\CacheWarmer\Api\Service\WarmerServiceInterface;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\Config\ExtendedConfig;
use \Magento\Store\Model\StoreManagerInterface;

class PageService implements PageServiceInterface
{
    /**
     * @var CurlService
     */
    protected $curlService;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var CacheIdentifier
     */
    private $cacheIdentifier;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ExtendedConfig
     */
    private $extendedConfig;

    /**
     * @var PageTypeRepositoryInterface
     */
    private $pageTypeRepository;

    /**
     * @var FilterServiceInterface
     */
    private $filterService;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        CacheIdentifier $cacheIdentifier,
        CurlService $curlService,
        Registry $registry,
        HttpContext $httpContext,
        Cache $cache,
        Config $config,
        ExtendedConfig $extendedConfig,
        PageTypeRepositoryInterface $pageTypeRepository,
        FilterServiceInterface $filterService,
        ModuleListInterface $moduleList,
        StoreManagerInterface $storeManager
    ) {
        $this->pageRepository     = $pageRepository;
        $this->cacheIdentifier    = $cacheIdentifier;
        $this->curlService        = $curlService;
        $this->registry           = $registry;
        $this->httpContext        = $httpContext;
        $this->cache              = $cache;
        $this->config             = $config;
        $this->extendedConfig     = $extendedConfig;
        $this->pageTypeRepository = $pageTypeRepository;
        $this->filterService      = $filterService;
        $this->moduleList         = $moduleList;
        $this->storeManager         = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isCached(PageInterface $page)
    {
        if ($this->moduleList->has('FishPig_Bolt')) {
            if ($this->cache->load($page->getCacheId())) {
                return true;
            }

            $channel = $this->curlService->initChannel();

            $channel->setUrl($page->getUri());

            //we add this cookie to have correct cache hit stats
            $channel->addCookie('mst-cache-warmer-track', 1);

            if ($page->getVaryString()) {
                $channel->addCookie('X-Magento-Vary', $page->getVaryString());
            }

            $response = $this->curlService->request($channel);
            $headers  = $response->getHeaders();

            if (isset($headers['X-Cached-By']) && $headers['X-Cached-By'] == 'Bolt') {
                return true;
            }

            return false;
        } elseif ($this->config->getCacheType() == PageCacheConfig::BUILT_IN) {
            return ($this->cache->load($page->getCacheId())) ? true : false;
        } else {
            $channel = $this->curlService->initChannel();

            $channel->setUrl($page->getUri());
            $channel->setUserAgent(WarmerServiceInterface::STATUS_USER_AGENT);

            if ($page->getVaryString()) {
                $channel->addCookie('X-Magento-Vary', $page->getVaryString());
            }

            $response = $this->curlService->request($channel);

            if ($response->getBody() === '*') {
                return false;
            }

            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collect(RequestInterface $request, ResponseInterface $response)
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        /** @var \Magento\Framework\App\Request\Http $request */

        $cacheId = $this->cacheIdentifier->getValue();

        $page = $this->pageRepository->getByCacheId($cacheId);

        $pageType = $request->getFullActionName();

        if (strpos($request->getUriString(), '?') !== false) {
            $pageType .= '_*';
        } elseif ($this->filterService->isSeoFilterPage($pageType, $request->getParams())) {
            $pageType .= '_SeoFilter';
        }

        if (!$this->isValidUrl($request->getUriString())) {
            return false;
        }
        $storeId = $this->storeManager->getStore()->getId();

        if (!$page && $request->getFullActionName() !== '__'
            && strpos($request->getUriString(), '_=') === false) {
            $product  = $this->registry->registry('current_product');
            $category = $this->registry->registry('current_category');

            $productId = $product ? $product->getId() : 0;
            if (!$productId && $this->registry->registry(PageServiceInterface::PRODUCT_REG)) {
                $productId = $this->registry->registry(PageServiceInterface::PRODUCT_REG);
            }

            $categoryId = $category ? $category->getId() : 0;
            if (!$categoryId && $this->registry->registry(PageServiceInterface::CATEGORY_REG)) {
                $categoryId = $this->registry->registry(PageServiceInterface::CATEGORY_REG);
            }

            $varyData = $this->prepareVaryData($this->httpContext->getData());

            /** @var PageInterface $page */
            $page = $this->pageRepository->getCollection()
                ->addFieldToFilter(PageInterface::URI, $request->getUriString())
                ->addFieldToFilter(PageInterface::PRODUCT_ID, $productId)
                ->addFieldToFilter(PageInterface::CATEGORY_ID, $categoryId)
                ->addFieldToFilter(PageInterface::STORE_ID, $storeId)
                ->addFieldToFilter(PageInterface::VARY_DATA, $varyData)
                ->getFirstItem();

            $page->setUri($request->getUriString())
                ->setCacheId($cacheId)
                ->setPageType($pageType)
                ->setProductId($productId)
                ->setCategoryId($categoryId)
                ->setStoreId($storeId)
                ->setVaryData($varyData);

            $pageTypeCollection = $this->pageTypeRepository->getCollection()
                ->addFieldToFilter(PageTypeInterface::PAGE_TYPE, $pageType);
            if ($pageTypeCollection->getSize() == 0) {
                $this->pageTypeRepository->save(
                    $this->pageTypeRepository->create()->setPageType($pageType)
                );
            }

            if (!$this->config->isIgnoredPage($page)) {
                $this->pageRepository->save($page);
            }
        } elseif (is_object($page) && $page->getId() && $pageType != $page->getPageType()) {
            $page->setUri($request->getUriString())
                ->setCacheId($cacheId)
                ->setPageType($pageType)
                ->setStoreId($storeId)
                ->setVaryData($this->prepareVaryData($this->httpContext->getData()));

            $this->pageRepository->save($page);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidUrl($url)
    {
        if (strpos($url, 'https://') === false
            && strpos($url, 'http://') === false) {
            return false;
        }
        //Assume that URL like https://123.123.123.123 is not valid. no cetificate
        if (isset($parsedUrl['host'])
            && $parsedUrl['host']
            && strpos($url, 'http://') === false
            && filter_var($parsedUrl['host'], FILTER_VALIDATE_IP)
        ) {
            return false;
        }

        return true;
    }

    /**

    /**
     * {@inheritdoc}
     */
    public function prepareVaryData($varyData)
    {
        if (is_array($varyData)) {
            ksort($varyData);
        }

        return \Zend_Json::encode($varyData);
    }
}
