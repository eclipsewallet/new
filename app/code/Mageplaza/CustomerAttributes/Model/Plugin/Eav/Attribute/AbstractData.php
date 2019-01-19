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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Eav\Attribute;

/**
 * Class AbstractData
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Eav\Attribute
 */
class AbstractData
{
    /**
     * @param \Magento\Eav\Model\Attribute\Data\AbstractData $subject
     * @param array|string $value
     *
     * @return array|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeValidateValue(\Magento\Eav\Model\Attribute\Data\AbstractData $subject, $value)
    {
        $attribute = $subject->getAttribute();

        foreach (['customer_register_address', 'customer_address_edit'] as $form) {
            if ($attribute->getIsUserDefined() && !in_array($form, $attribute->getUsedInForms())) {
                $attribute->setIsRequired(false);
                $attribute->setValidateRules(null);
            }
        }

        return [$value];
    }
}