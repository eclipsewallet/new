<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 11/15/2018
 * Time: 4:58 PM
 */

namespace Wiki\VAT\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Wiki\VAT\Helper\Data as VATHelper;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;

class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * @var VATHelper
     */
    protected $VATHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    protected $customer;

    protected $customerFactory;

    /**
     * @param VATHelper $VATHelper
     * @param Session $checkoutSession
     * @codeCoverageIgnore
     */
    public function __construct(
        VATHelper $VATHelper,
        Session $checkoutSession,
        Customer $customer,
        CustomerFactory $customerFactory
    )
    {
        $this->VATHelper      = $VATHelper;
        $this->checkoutSession = $checkoutSession;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();

        $VatData = $this->checkoutSession->getVatData();

        if ($VatData) {
            $order->setData('wk_vat_information', json_encode($VatData));
            if($customerId && $VatData['vatSave']){
                $customer = $this->customer->load($customerId);
                $customerData = $customer->getDataModel();
                $customerData->setCustomAttribute('vat_number',$VatData['vatNumber']);
                $customerData->setCustomAttribute('vat_company',$VatData['vatCompany']);
                $customerData->setCustomAttribute('vat_address',$VatData['vatAddress']);
                $customer->updateData($customerData);
                $customer->save();
            }
            $this->checkoutSession->unsVatData();
        }

    }
}