<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model\UrlBuilder;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;

class Adapter implements \Amasty\ShopbyBase\Api\UrlBuilder\AdapterInterface
{
    const SELF_ROUTE_PATH = 'amshopby/index/index';
    const SELF_MODULE_NAME = 'amshopby';

    /**
     * @var \Magento\Framework\Url
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Framework\Url $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopConfig,
        RequestInterface $request
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopConfig;
        $this->request = $request;
    }

    /**
     * @param null $routePath
     * @param null $routeParams
     * @return string|null
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        if ($routePath == self::SELF_ROUTE_PATH
            || ($this->request->getModuleName() == self::SELF_MODULE_NAME && !$this->hasQueryParam($routeParams))
        ) {
            $urlKey = $this->scopeConfig->getValue(
                \Amasty\Shopby\Helper\Data::AMSHOPBY_ROOT_GENERAL_URL_PATH,
                ScopeInterface::SCOPE_STORE
            );
            if ($urlKey) {
                $routeParams['_direct'] = $urlKey . $this->getSuffix();
                $routePath = '';
            }
            return $this->urlBuilder->getUrl($routePath, $routeParams);
        }
        return null;
    }

    /**
     * @param array|null $routeParams
     * @return bool
     */
    private function hasQueryParam($routeParams)
    {
        if (!is_array($routeParams) || !isset($routeParams['_query'])) {
            return false;
        }
        $userPamrams = $this->request->getUserParams();
        foreach ($routeParams['_query'] as $paramName => $paramValue) {
            if ($paramValue && !isset($userPamrams[$paramName])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return null
     */
    public function getSuffix()
    {
        return null;
    }
}
