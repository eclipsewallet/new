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

namespace Mageplaza\CustomerAttributes\Model\Config\Source;

/**
 * Class FrontendClass
 * @package Mageplaza\CustomerAttributes\Model\Config\Source
 */
class FrontendClass implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => 'validate-number', 'label' => __('Decimal Number')],
            ['value' => 'validate-digits', 'label' => __('Integer Number')],
            ['value' => 'validate-email', 'label' => __('Email')],
            ['value' => 'validate-url', 'label' => __('URL')],
            ['value' => 'validate-alpha', 'label' => __('Letters')],
            ['value' => 'validate-alphanum', 'label' => __('Letters (a-z, A-Z) or Numbers (0-9)')]
        ];
    }
}
