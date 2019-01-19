<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomerAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerAttributes\Model\Plugin\Customer\Address;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class ConvertQuoteAddressToOrderAddress
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Customer\Address
 */
class ConvertQuoteAddressToOrderAddress
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    )
    {
        $this->helperData = $helperData;
    }

    /**
     * @param ToOrderAddress $subject
     * @param \Closure $proceed
     * @param Address $quoteAddress
     * @param array $data
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundConvert(
        ToOrderAddress $subject,
        \Closure $proceed,
        Address $quoteAddress,
        $data = []
    )
    {
        $orderAddress = $proceed($quoteAddress, $data);
        $attributes = $this->helperData->getUserDefinedAttributeCodes('customer_address');
        foreach ($attributes as $attribute) {
            $orderAddress->setData($attribute, $quoteAddress->getData($attribute));
        }

        return $orderAddress;
    }
}
