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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Sales;

use Mageplaza\CustomerAttributes\Model\Address\OrderFactory as OrderAddressFactory;
use Mageplaza\CustomerAttributes\Model\OrderFactory;

/**
 * Class DataProvider
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Sales
 */
class DataProvider
{
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var OrderAddressFactory
     */
    protected $orderAddressFactory;

    /**
     * @param OrderFactory $orderFactory
     * @param OrderAddressFactory $orderAddressFactory
     */
    public function __construct(
        OrderFactory $orderFactory,
        OrderAddressFactory $orderAddressFactory
    )
    {
        $this->orderFactory = $orderFactory;
        $this->orderAddressFactory = $orderAddressFactory;
    }

    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetData(\Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject, $result)
    {
        if ($subject->getName() != 'sales_order_grid_data_source') {
            return $result;
        }

        if (isset($result['items'])) {
            $order = $this->orderFactory->create();
            $data = $order->attachDataToSalesOrder($result['items']);

            foreach ($result['items'] as &$item) {
                if (isset($data[$item['entity_id']])) {
                    foreach ((array)$data[$item['entity_id']] as $datum) {
                        foreach ($datum as $key => $value) {
                            $k = str_replace('customer_', '', $key);
                            $item[$k] = $value;
                        }
                    }
                }
            }
        }

        return $result;
    }
}