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

namespace Mageplaza\StoreLocator\Controller\StoreLocator;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Store
 * @package Mageplaza\StoreLocator\Controller\StoreLocator
 */
class Store extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Store constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        PageFactory $resultPageFactory,
        LayoutFactory $resultLayoutFactory,
        JsonFactory $resultJsonFactory,
        AccountManagementInterface $accountManagement,
        CustomerUrl $customerUrl,
        Session $customerSession
    )
    {
        $this->storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->accountManagement = $accountManagement;
        $this->customerUrl = $customerUrl;
        $this->session = $customerSession;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $result = $this->_resultJsonFactory->create();
            $resultLayout = $this->_resultLayoutFactory->create();
            $listLocHtml = $resultLayout->getLayout()
                ->createBlock('Mageplaza\StoreLocator\Block\Frontend')
                ->setTemplate('Mageplaza_StoreLocator::storelocator/index.phtml')
                ->toHtml();
            $result->setData([
                                 'success' => $listLocHtml
                             ]);

            return $result;
        }

        return $this->resultPageFactory->create();
    }
}
