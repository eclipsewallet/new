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

namespace Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Validate
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute
 */
class Validate extends \Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute
{
    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);

        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $frontendLabel = $this->getRequest()->getParam('frontend_label');
        $attributeCode = $attributeCode ?: $this->dataHelper->generateCode($frontendLabel[0]);
        $attributeId = $this->getRequest()->getParam('attribute_id');

        if (!$attributeId) {
            $attributeObject = $this->_initAttribute()->loadByCode($this->_getEntityType()->getId(), $attributeCode);
            if ($attributeObject->getId()) {
                $this->messageManager->addErrorMessage(__('An attribute with this code already exists.'));

                $this->_view->getLayout()->initMessages();
                $response->setError(true);
                $response->setHtmlMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }
}
