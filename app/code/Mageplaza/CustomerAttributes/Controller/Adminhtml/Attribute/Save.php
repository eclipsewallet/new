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

use Magento\Backend\Model\View\Result\Redirect;
use Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Save
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute
 */
class Save extends Attribute
{
    /**
     * @var string
     */
    protected $type = 'customer';

    /**
     * @return Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Serializer_Exception
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $attributeObject = $this->_initAttribute();

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);

                $data['frontend_input'] = $attributeObject->getFrontendInput();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
            } else {
                $attributeCode = $data['attribute_code'] ?: $this->dataHelper->generateCode($this->getRequest()->getParam('frontend_label')[0]);

                $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );

                    return $this->returnResult('customer/*/', ['_current' => true]);
                }

                $data['attribute_code'] = $attributeCode;
                $data['source_model'] = $this->dataHelper->getSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = $this->dataHelper->getBackendModelByInputType($data['frontend_input']);
                $data['backend_type'] = $this->dataHelper->getBackendTypeByInputType($data['frontend_input']);
                $data['attribute_set_id'] = $this->_getEntityType()->getDefaultAttributeSetId();
                $data['attribute_group_id'] = $this->attrSetFactory->create()->getDefaultGroupId($data['attribute_set_id']);
                $data['is_user_defined'] = 1;
                $data['multiline_count'] = 0;
            }

            $data['validate_rules'] = $this->dataHelper->getValidateRules($data, $attributeObject->getValidateRules());

            if ($data['is_user_defined']) {
                $data['used_in_forms'][] = $this->type == 'customer' ? 'adminhtml_' . $this->type : '';
            }

            foreach (['is_filterable_in_grid', 'is_searchable_in_grid'] as $item) {
                $data[$item] = $data['is_used_in_grid'];

                if (!empty($data['is_used_in_sales_order_grid'])) {
                    $data[$item] = $data['is_used_in_sales_order_grid'];
                }
            }

            foreach (['mp_store_id', 'mp_customer_group', 'value_depend'] as $item) {
                if (isset($data[$item])) {
                    $data[$item] = implode(',', $data[$item]);
                }
            }

            if ($defaultValueField = $this->dataHelper->getDefaultValueByInput($data['frontend_input'])) {
                $defaultValue = $this->getRequest()->getParam($defaultValueField);
                $data['default_value'] = $defaultValue;

                if ($defaultValueField == 'default_value_date' && $defaultValue) {
                    $data['default_value'] = date("Y-m-d H:i:s", strtotime($defaultValue));
                }
            }

            $attributeObject->addData($data);

            try {
                $attributeObject->save();

                $this->_eventManager->dispatch(
                    'mageplaza_' . $this->type . '_attribute_save',
                    ['attribute' => $attributeObject]
                );

                $this->messageManager->addSuccessMessage(__('You saved the attribute.'));
                $this->_session->setAttributeData(false);

                if ($this->getRequest()->getParam('back', false)) {
                    return $this->returnResult('customer/*/edit', ['id' => $attributeObject->getId(), '_current' => true]);
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setAttributeData($data);

                if ($attributeId) {
                    return $this->returnResult('customer/*/edit', ['id' => $attributeId, '_current' => true]);
                }
            }
        }

        return $this->returnResult('customer/*/', []);
    }
}
