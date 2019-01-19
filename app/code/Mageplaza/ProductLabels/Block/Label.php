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
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Block;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Model\ResourceModel\RuleFactory as ResourceRuleFactory;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class Label
 * @package Mageplaza\ProductLabels\Block
 */
class Label extends Template
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var ResourceRuleFactory
     */
    protected $_resourceRuleFactory;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductFactory
     */
    protected $_productLoader;

    /**
     * @var Gallery
     */
    protected $_gallery;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var ThemeProviderInterface
     */
    protected $_themeProvider;

    /**
     * Label constructor.
     *
     * @param Template\Context $context
     * @param RuleFactory $ruleFactory
     * @param ResourceRuleFactory $resourceRuleFactory
     * @param HelperData $helperData
     * @param CollectionFactory $productCollectionFactory
     * @param ProductFactory $productLoader
     * @param Gallery $gallery
     * @param Registry $registry
     * @param SessionFactory $customerSession
     * @param ThemeProviderInterface $themeProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        RuleFactory $ruleFactory,
        ResourceRuleFactory $resourceRuleFactory,
        HelperData $helperData,
        CollectionFactory $productCollectionFactory,
        ProductFactory $productLoader,
        Gallery $gallery,
        Registry $registry,
        SessionFactory $customerSession,
        ThemeProviderInterface $themeProvider,
        array $data = []
    )
    {
        $this->_ruleFactory             = $ruleFactory;
        $this->_resourceRuleFactory     = $resourceRuleFactory;
        $this->_helperData              = $helperData;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_productLoader           = $productLoader;
        $this->_gallery                 = $gallery;
        $this->_registry                = $registry;
        $this->customerSession          = $customerSession;
        $this->_themeProvider           = $themeProvider;

        parent::__construct($context, $data);
    }

    /**
     * Get All Rule Collection apply on product
     *
     * @param $product
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRulesApplyProduct($product)
    {
        /** @var \Mageplaza\ProductLabels\Model\ResourceModel\Rule $resourceModel */
        $resourceModel  = $this->_resourceRuleFactory->create();
        $ruleIds        = $resourceModel->getMatchingRuleIds();
        $ruleCollection = [];
        $customerGroup  = 0;
        if ($this->customerSession->create()->isLoggedIn()) {
            $customerGroup = $this->customerSession->create()->getCustomer()->getGroupId();
        }

        foreach ($ruleIds as $ruleId) {
            $rule    = $this->_ruleFactory->create()->load($ruleId);
            $isValid = $rule->getConditions()->validate($product);
            if (!$isValid) {
                continue;
            }
            $customerGroupRule = explode(',', $rule['customer_group_ids']);

            if ($rule['customer_group_ids'] == null || in_array($customerGroup, $customerGroupRule)) {
                $ruleCollection[] = $rule;
            }

            if ($rule->getStopProcess()) {
                return $ruleCollection;
            }
        }

        return $ruleCollection;
    }

    /**
     * Get Product Ids by rule conditions
     *
     * @param $rule
     *
     * @return array
     */
    public function getProductIds($rule)
    {
        $productIds = $rule->getMatchingProductIds();

        if ($rule->getBestseller() != 1) {
            return $productIds;
        }

        $bestSellerProductIds = [];
        $collection           = $this->productCollectionFactory->create()
            ->addIdFilter($productIds)
            ->setPageSize($rule->getLimit());

        $collection->getSelect()->joinLeft(
            ['soi' => $collection->getTable('sales_order_item')],
            'e.entity_id = soi.product_id',
            ['qty_ordered' => 'SUM(soi.qty_ordered)']
        )->group('e.entity_id')->where('soi.qty_ordered', ['gt' => 0])->order('qty_ordered DESC');

        $collection->addStoreFilter();

        foreach ($collection->getData() as $item) {
            $bestSellerProductIds[] = $item['entity_id'];
        }

        return $bestSellerProductIds;
    }

    /**
     * check validate product in rule
     *
     * @param $rule
     * @param $id
     *
     * @return bool
     */
    public function validateProductInRule($rule, $id)
    {
        return in_array($id, $this->getProductIds($rule));
    }

    /**
     * Replace variables label
     *
     * @param $label
     * @param $productId
     *
     * @return mixed
     */
    public function replaceLabel($label, $productId)
    {
        $attrCode = $this->getAttrInLabel($label);
        $search   = [
            '{{discount}}',
            '{{discount_percent}}',
            '{{current_price}}',
        ];
        $replace  = [
            $this->getDiscount($productId),
            $this->getPercentDiscount($productId),
            $this->getCurrentPrice($productId),
        ];
        $label    = str_replace($search, $replace, $label);

        if ($attrCode && !in_array('{{' . $attrCode . '}}', $search)) {
            $label = str_replace('{{' . $attrCode . '}}', $this->getAttributeProduct($attrCode, $productId), $label);
        }

        return $label;
    }

    /**
     * get attribute code in variables label
     *
     * @param $label
     *
     * @return bool|string
     */
    public function getAttrInLabel($label)
    {
        $start     = strpos($label, '{{');
        $end       = strpos($label, '}}');
        $attribute = substr($label, $start + 2, $end - ($start + 2));

        return $attribute;
    }

    /**
     * get discount product by product id
     *
     * @param $productId
     *
     * @return string
     */
    public function getDiscount($productId)
    {
        $product        = $this->getProductById($productId);
        $originalPrice  = $product->getPrice();
        $finalPrice     = $product->getFinalPrice();
        $currencySymbol = $this->getCurrentCySymbol();
        $discount       = 0;

        if ($originalPrice > $finalPrice) {
            $discount = $originalPrice - $finalPrice;
        }

        return $currencySymbol . $discount;
    }

    /**
     * get discount percent by product id
     *
     * @param $productId
     *
     * @return int|string
     */
    public function getPercentDiscount($productId)
    {
        $product       = $this->getProductById($productId);
        $originalPrice = $product->getPrice();
        $finalPrice    = $product->getFinalPrice();

        $percentage = 0;
        if ($originalPrice > $finalPrice) {
            $percentage = number_format(($originalPrice - $finalPrice) * 100 / $originalPrice, 0);
        }

        return $percentage;
    }

    /**
     * get Current price by product id
     *
     * @param $productId
     *
     * @return float
     */
    public function getCurrentPrice($productId)
    {
        return number_format($this->getProductById($productId)->getFinalPrice(), 2);
    }

    public function getProductById($productId)
    {
        return $this->_productLoader->create()->load($productId);
    }

    /**
     * get product attribute name
     *
     * @param $attributeCode
     * @param $productId
     *
     * @return string
     */
    public function getAttributeProduct($attributeCode, $productId)
    {
        $product = $this->getProductById($productId);

        if (is_object($product->getCustomAttribute($attributeCode))) {
            $optionId = $product->getCustomAttribute($attributeCode)->getValue();

            $_attributeId = $product->getResource()->getAttribute($attributeCode);
            if ($_attributeId->usesSource()) {
                $label = $_attributeId->getSource()->getOptionText($optionId);
                if (is_array($label)) {
                    return implode(', ', $label);
                }

                return $label;
            }
        }

        return null;
    }

    /**
     * get Current Product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Get Url image template
     *
     * @param $path
     *
     * @return string
     */
    public function getTemplateUrl($path)
    {
        $url   = $this->getViewFileUrl('Mageplaza_ProductLabels::images/template/' . $path);
        $start = strpos($url, '/version');
        $end   = strpos($url, 'adminhtml');

        return substr($url, 0, $start) . '/' . substr($url, $end, 1000);
    }

    /**
     * Check Smartwave/porto theme
     *
     * @return bool
     */
    public function isPortoTheme()
    {
        $themeId = $this->_scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $this->_themeProvider->getThemeById($themeId);

        return $theme->getCode() === 'Smartwave/porto';
    }
}