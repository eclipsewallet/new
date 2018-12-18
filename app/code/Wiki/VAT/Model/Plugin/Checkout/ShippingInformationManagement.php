<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 11/15/2018
 * Time: 2:46 PM
 */

namespace Wiki\VAT\Model\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Wiki\VAT\Helper\Data as VATHelper;

class ShippingInformationManagement
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var VATHelper
     */
    protected $VATHelper;

    /**
     * @param CheckoutSession $checkoutSession
     * @param VATHelper $VATHelper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        VATHelper $VATHelper
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->VATHelper      = $VATHelper;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    )
    {
        if ($this->VATHelper->isEnabled() && $extensionAttributes = $addressInformation->getExtensionAttributes()) {
            $vatInformation = [
                'vatNumber'      => $extensionAttributes->getWkVatNumber(),
                'vatCompany'      => $extensionAttributes->getWkVatCompany(),
                'vatAddress' => $extensionAttributes->getWkVatAddress(),
                'vatSave' => $extensionAttributes->getWkVatSave()
            ];
            $this->checkoutSession->setVatData($vatInformation);
        }

        return [$cartId, $addressInformation];
    }
}