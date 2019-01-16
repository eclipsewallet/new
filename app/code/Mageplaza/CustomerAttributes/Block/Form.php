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

namespace Mageplaza\CustomerAttributes\Block;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type as EntityType;
use Magento\Eav\Model\Form\Factory;
use Magento\Framework\Data\Collection\ModelFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Swatches\Model\Swatch;
use Mageplaza\CustomerAttributes\Helper\Data as DataHelper;

/**
 * Class Form
 * @package Mageplaza\CustomerAttributes\Block
 */
class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * EAV Form Type code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Entity model class type for new entity object
     *
     * @var string
     */
    protected $_entityModelClass;

    /**
     * Entity type instance
     *
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityType;

    /**
     * EAV form instance
     *
     * @var \Magento\Eav\Model\Form
     */
    protected $_form;

    /**
     * EAV Entity Model
     *
     * @var \Magento\Framework\Model\AbstractModel
     */
    protected $_entity;

    /**
     * Format for HTML elements id attribute
     *
     * @var string
     */
    protected $_fieldIdFormat = '%1$s';

    /**
     * Format for HTML elements name attribute
     *
     * @var string
     */
    protected $_fieldNameFormat = '%1$s';

    /**
     * @var ModelFactory
     */
    protected $_modelFactory;

    /**
     * @var Factory
     */
    protected $_formFactory;

    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Customer\Model\Metadata\Form
     */
    protected $_metadataForm;

    /**
     * @var FormFactory
     */
    protected $_metadataFormFactory;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param ModelFactory $modelFactory
     * @param Factory $formFactory
     * @param Config $eavConfig
     * @param FormFactory $metadataFormFactory
     * @param DataHelper $dataHelper
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModelFactory $modelFactory,
        Factory $formFactory,
        Config $eavConfig,
        FormFactory $metadataFormFactory,
        DataHelper $dataHelper,
        Session $customerSession,
        array $data = []
    )
    {
        $this->_modelFactory = $modelFactory;
        $this->_formFactory = $formFactory;
        $this->_eavConfig = $eavConfig;
        $this->_metadataFormFactory = $metadataFormFactory;
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;

        parent::__construct($context, $data);
    }

    /**
     * Return attribute renderer by frontend input type
     *
     * @param string $type
     *
     * @return \Mageplaza\CustomerAttributes\Block\Form\Renderer\AbstractRenderer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRenderer($type)
    {
        return $this->getLayout()->getBlock('customer_form_template')->getChildBlock($type);
    }

    /**
     * Render attribute row and return HTML
     *
     * @param \Magento\Eav\Model\Attribute $attribute
     *
     * @return string|false
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Serializer_Exception
     */
    public function getAttributeHtml(\Magento\Eav\Model\Attribute $attribute)
    {
        $type = $attribute->getFrontendInput();

        if (!empty($attribute->getData('additional_data'))) {
            $additionalData = $this->dataHelper->unserialize($attribute->getData('additional_data'));
            if (isset($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                $type .= '_' . $additionalData[Swatch::SWATCH_INPUT_TYPE_KEY];
            }
        }

        $block = $this->getRenderer($type);
        if ($block) {
            $block->setAttributeObject(
                $attribute
            )->setEntity(
                $this->getEntity()
            )->setFieldIdFormat(
                $this->_fieldIdFormat
            )->setFieldNameFormat(
                $this->_fieldNameFormat
            )->setEntityType(
                $this->_entityType
            );

            return $block->toHtml();
        }

        return false;
    }

    /**
     * Set entity model class for new object
     *
     * @param string $model
     *
     * @return $this
     */
    public function setEntityModelClass($model)
    {
        $this->_entityModelClass = $model;

        return $this;
    }

    /**
     * Set Entity type if entity model entity type is not defined or is different
     *
     * @param int|string|\Magento\Eav\Model\Entity\Type $entityType
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $this->_eavConfig->getEntityType($entityType);

        return $this;
    }

    /**
     * Set EAV entity Form code
     *
     * @param string $code
     *
     * @return $this
     */
    public function setFormCode($code)
    {
        $this->_formCode = $code;

        return $this;
    }

    /**
     * Return Entity object
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getEntity()
    {
        if ($this->_entity === null && $this->_entityModelClass) {
            $this->_entity = $this->_modelFactory->create($this->_entityModelClass);
            $entityId = $this->getCurrentEntityId($this->_entity->getEntityType());
            if ($entityId) {
                $this->_entity->load($entityId);
            }
        }

        return $this->_entity;
    }

    /**
     * Retrieve current entity type
     *
     * @param EntityType $entityType
     *
     * @return int|null
     */
    protected function getCurrentEntityId(EntityType $entityType)
    {
        switch ($entityType->getEntityTypeCode()) {
            case CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER:
                return $this->customerSession->getCustomerId();
                break;
            case AddressMetadataInterface::ENTITY_TYPE_ADDRESS:
                return (int)$this->getRequest()->getParam('id');
                break;
            default:
                return null;
        }
    }

    /**
     * Set Entity object
     *
     * @param \Magento\Framework\Model\AbstractModel $entity
     *
     * @return $this
     */
    public function setEntity(\Magento\Framework\Model\AbstractModel $entity)
    {
        $this->_entity = $entity;

        return $this;
    }

    /**
     * Return EAV entity Form instance
     *
     * @return \Magento\Eav\Model\Form
     */
    public function getForm()
    {
        if ($this->_form === null) {
            $this->_form = $this->_formFactory->create(
                \Magento\Customer\Model\Form::class
            )->setFormCode(
                $this->_formCode
            )->setEntity(
                $this->getEntity()
            );
            if ($this->_entityType) {
                $this->_form->setEntityType($this->_entityType);
            }
            $this->_form->initDefaultValues();
        }

        return $this->_form;
    }

    /**
     * Set EAV entity form instance
     *
     * @param \Magento\Eav\Model\Form $form
     *
     * @return $this
     */
    public function setForm(\Magento\Eav\Model\Form $form)
    {
        $this->_form = $form;

        return $this;
    }

    /**
     * @return array
     */
    public function getUserDefinedAttributes()
    {
        $attributes = [];
        /** @var \Magento\Eav\Model\Attribute $attribute */
        foreach ($this->getForm()->getUserAttributes() as $attribute) {
            if ($attribute->getIsVisible() && $this->dataHelper->filterAttribute($attribute)) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * @return string
     */
    public function getAttributeDataDependency()
    {
        $data = [];
        foreach ($this->getUserDefinedAttributes() as $attribute) {
            if ($attribute->getFieldDepend() || $attribute->getFrontendInput() == 'select') {
                $data[] = $attribute->getData();
            }
        }

        return DataHelper::jsonEncode($data);
    }

    /**
     * Set format for HTML elements id attribute
     *
     * @param string $format
     *
     * @return $this
     */
    public function setFieldIdFormat($format)
    {
        $this->_fieldIdFormat = $format;

        return $this;
    }

    /**
     * Set format for HTML elements name attribute
     *
     * @param string $format
     *
     * @return $this
     */
    public function setFieldNameFormat($format)
    {
        $this->_fieldNameFormat = $format;

        return $this;
    }

    /**
     * @return \Magento\Customer\Model\Metadata\Form
     */
    public function getMetadataForm()
    {
        if ($this->_metadataForm === null) {
            $this->_metadataForm = $this->_metadataFormFactory->create(
                $this->_entityType->getEntityTypeCode(),
                $this->_formCode
            );
        }

        return $this->_metadataForm;
    }
}
