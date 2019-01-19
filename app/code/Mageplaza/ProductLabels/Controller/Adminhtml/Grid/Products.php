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

namespace Mageplaza\ProductLabels\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\ProductLabels\Controller\Adminhtml\Rule;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class Products
 * @package Mageplaza\ProductLabels\Controller\Adminhtml\Grid
 */
class Products extends Rule
{
    /**
     * JS helper
     *
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsonHelper;

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * ProductList constructor.
     *
     * @param RuleFactory $ruleFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        RuleFactory $ruleFactory,
        Registry $coreRegistry,
        Context $context,
        PageFactory $pageFactory,
        JsonHelper $jsonHelper
    )
    {
        $this->_jsonHelper  = $jsonHelper;
        $this->_pageFactory = $pageFactory;

        parent::__construct($ruleFactory, $coreRegistry, $context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $page = $this->_pageFactory->create();
        $html = $page->getLayout()
            ->createBlock('\Mageplaza\ProductLabels\Block\Adminhtml\Rule\Edit\Grid')->toHtml();
        if ($this->getRequest()->getParam('loadGrid')) {
            $html = $this->_jsonHelper->jsonEncode($html);
        }

        return $this->getResponse()->representJson($html);
    }
}
