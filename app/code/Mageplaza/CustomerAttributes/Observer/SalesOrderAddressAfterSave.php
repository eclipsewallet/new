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

namespace Mageplaza\CustomerAttributes\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\CustomerAttributes\Helper\Data;
use Mageplaza\CustomerAttributes\Model\Address\OrderFactory;
use Psr\Log\LoggerInterface;

/**
 * Class SalesOrderAddressAfterSave
 * @package Mageplaza\CustomerAttributes\Observer
 */
class SalesOrderAddressAfterSave implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var OrderFactory
     */
    protected $orderAddressFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Data $dataHelper
     * @param OrderFactory $orderAddressFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $dataHelper,
        OrderFactory $orderAddressFactory,
        LoggerInterface $logger
    )
    {
        $this->dataHelper = $dataHelper;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->logger = $logger;
    }

    /**
     * After save observer for order address
     *
     * @param Observer $observer
     *
     * @return $this|string|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $orderAddress = $observer->getEvent()->getAddress();
        if ($orderAddress instanceof \Magento\Framework\Model\AbstractModel) {
            $attributes = $this->dataHelper->getAttributeWithFilters('customer_address', 'checkout_index_index');
            /** @var \Magento\Customer\Model\Attribute $attribute */
            foreach ($attributes as $attribute) {
                $attrCode = $attribute->getAttributeCode();
                if (!empty($orderAddress[$attrCode])) {
                    $file = Data::jsonDecode($orderAddress[$attrCode]);

                    if (!empty($file['file'])) {
                        try {
                            $orderAddress[$attrCode] = $this->dataHelper->moveTemporaryFile($file);
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $this->logger->critical($e);

                            return $e->getMessage();
                        }
                    } else if (isset($orderAddress[$attrCode]['value'])) {
                        $orderAddress[$attrCode] = $orderAddress[$attrCode]['value'];
                    }
                }
            }

            $orderAddressModel = $this->orderAddressFactory->create();
            $orderAddressModel->saveAttributeData($orderAddress);
        }

        return $this;
    }
}
