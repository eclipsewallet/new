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

namespace Mageplaza\CustomerAttributes\Helper;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Model\Swatch;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\CustomerAttributes\Helper
 */
class Data extends AbstractData
{
    const TEMPLATE_MEDIA_PATH = 'customer_address';

    /**
     * @var array
     */
    protected $userDefinedAttributeCodes = [];

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Config $eavConfig
     * @param Session $customerSession
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Config $eavConfig,
        Session $customerSession,
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    )
    {
        $this->eavConfig = $eavConfig;
        $this->customerSession = $customerSession;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return array
     */
    public function getCustomerFormOptions()
    {
        return [
            ['label' => __('Customer Account Create'), 'value' => 'customer_account_create'],
            ['label' => __('Customer Account Edit'), 'value' => 'customer_account_edit'],
            ['label' => __('Admin Checkout'), 'value' => 'adminhtml_checkout']
        ];
    }

    /**
     * @return array
     */
    public function getAddressFormOptions()
    {
        return [
            ['label' => __('Customer Address Registration'), 'value' => 'customer_register_address'],
            ['label' => __('Customer Address Edit'), 'value' => 'customer_address_edit'],
            ['label' => __('Admin Checkout'), 'value' => 'adminhtml_customer_address'],
            ['label' => __('Frontend Checkout'), 'value' => 'checkout_index_index']
        ];
    }

    /**
     * @return array
     */
    public function getInputType()
    {
        $inputTypes = [
            'text'               => [
                'label'            => __('Text Field'),
                'validate_filters' => ['alphanumeric', 'numeric', 'alpha', 'url', 'email'],
                'backend_type'     => 'varchar',
                'default_value'    => 'text',
            ],
            'textarea'           => [
                'label'            => __('Text Area'),
                'validate_filters' => [],
                'backend_type'     => 'text',
                'default_value'    => 'textarea',
            ],
            'date'               => [
                'label'            => __('Date'),
                'validate_filters' => ['date'],
                'backend_model'    => \Magento\Eav\Model\Entity\Attribute\Backend\Datetime::class,
                'backend_type'     => 'datetime',
                'default_value'    => 'date',
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/date',
            ],
            'boolean'            => [
                'label'            => __('Yes/No'),
                'validate_filters' => [],
                'source_model'     => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'backend_type'     => 'int',
                'default_value'    => 'yesno',
                'elementTmpl'      => 'ui/form/element/select',
            ],
            'select'             => [
                'label'            => __('Dropdown'),
                'validate_filters' => [],
                'source_model'     => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'backend_type'     => 'int',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/select',
            ],
            'multiselect'        => [
                'label'            => __('Multiple-select'),
                'validate_filters' => [],
                'backend_model'    => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'source_model'     => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'backend_type'     => 'varchar',
                'default_value'    => false,
            ],
            'select_visual'      => [
                'label'            => __('Single-select With Image'),
                'validate_filters' => [],
                'source_model'     => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'backend_type'     => 'int',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/select',
                'elementTmpl'      => 'Mageplaza_CustomerAttributes/form/element/radio-visual',
            ],
            'multiselect_visual' => [
                'label'            => __('Multiple Select With Image'),
                'validate_filters' => [],
                'backend_model'    => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'source_model'     => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'backend_type'     => 'varchar',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/checkboxes',
                'elementTmpl'      => 'Mageplaza_CustomerAttributes/form/element/checkbox-visual',
            ],
            'image'              => [
                'label'            => __('Media Image'),
                'validate_filters' => [],
                'backend_type'     => 'text',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/file-uploader',
                'elementTmpl'      => 'ui/form/element/uploader/uploader',
            ],
            'file'               => [
                'label'            => __('Single File Attachment'),
                'validate_filters' => [],
                'backend_type'     => 'text',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/file-uploader',
                'elementTmpl'      => 'ui/form/element/uploader/uploader',
            ],
            'textarea_visual'    => [
                'label'            => __('Content'),
                'validate_filters' => [],
                'backend_type'     => 'text',
                'default_value'    => 'textarea',
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/textarea',
            ],
            'multiline'          => [
                'label'            => __('Multiple Line'),
                'validate_filters' => ['alphanumeric', 'numeric', 'alpha', 'url', 'email'],
                'backend_type'     => 'text',
                'default_value'    => 'text',
            ]
        ];

        return $inputTypes;
    }

    /**
     * @return array
     */
    public function getValidateFilters()
    {
        return [
            ''             => __('None'),
            'alphanumeric' => __('Alphanumeric'),
            'numeric'      => __('Numeric Only'),
            'alpha'        => __('Alpha Only'),
            'url'          => __('URL'),
            'email'        => __('Email'),
            'date'         => __('Date')
        ];
    }

    /**
     * @param string $inputType
     *
     * @return string|false
     */
    public function getDefaultValueByInput($inputType)
    {
        $inputTypes = $this->getInputType();
        if (isset($inputTypes[$inputType])) {
            $value = $inputTypes[$inputType]['default_value'];
            if ($value) {
                return 'default_value_' . $value;
            }
        }

        return false;
    }

    /**
     * @param string $inputType
     *
     * @return string|null
     */
    public function getBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|null
     */
    public function getSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|null
     */
    public function getBackendTypeByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['backend_type'])) {
            return $inputTypes[$inputType]['backend_type'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|false
     */
    public function getComponentByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['component'])) {
            return $inputTypes[$inputType]['component'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|false
     */
    public function getElementTmplByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['elementTmpl'])) {
            return $inputTypes[$inputType]['elementTmpl'];
        }

        return null;
    }

    /**
     * @param $data
     * @param $validateRules
     *
     * @return string
     * @throws \Zend_Serializer_Exception
     */
    public function getValidateRules($data, $validateRules = [])
    {
        $inputType = $data['frontend_input'];
        $inputTypes = $this->getInputType();
        $rules = [];

        if (isset($inputTypes[$inputType])) {
            if ($inputType == 'date' && !empty($data['is_user_defined'])) {
                $rules['input_validation'] = 'date';
            } else if (!empty($inputTypes[$inputType]['validate_filters']) && !empty($data['input_validation'])) {
                if (in_array($data['input_validation'], $inputTypes[$inputType]['validate_filters'])) {
                    $rules['input_validation'] = $data['input_validation'];
                }
            }
        }

        if (isset($validateRules['input_validation'])) {
            unset($validateRules['input_validation']);
        }

        $result = $this->serialize(array_merge($validateRules, $rules));

        return empty($this->unserialize($result)) ? null : $result;
    }

    /**
     * Generate code from label
     *
     * @param string $label
     *
     * @return string
     * @throws \Zend_Validate_Exception
     */
    public function generateCode($label)
    {
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $this->getObject('\Magento\Catalog\Model\Product\Url')->formatUrlKey($label)
            ),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }

        return $code;
    }

    /**
     * @param Attribute $attribute
     *
     * @return bool
     * @throws \Zend_Serializer_Exception
     */
    public function isVisualAttribute($attribute)
    {
        if (!$attribute->hasData(Swatch::SWATCH_INPUT_TYPE_KEY)) {
            $serializedAdditionalData = $attribute->getData('additional_data');
            if ($serializedAdditionalData) {
                $additionalData = $this->unserialize($serializedAdditionalData);
                if (isset($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                    $attribute->setData(Swatch::SWATCH_INPUT_TYPE_KEY, $additionalData[Swatch::SWATCH_INPUT_TYPE_KEY]);
                }
            }
        }

        $isSelectType = in_array($attribute->getFrontendInput(), ['select', 'multiselect']);
        $isVisual = $attribute->getData(Swatch::SWATCH_INPUT_TYPE_KEY) === Swatch::SWATCH_INPUT_TYPE_VISUAL;

        return $isSelectType && $isVisual;
    }

    /**
     * @param Attribute $attribute
     *
     * @return void
     * @throws \Zend_Serializer_Exception
     */
    public function assembleAdditionalData($attribute)
    {
        $initialAdditionalData = $this->getAdditionalData($attribute);

        $dataToAdd[Swatch::SWATCH_INPUT_TYPE_KEY] = $attribute->getData(Swatch::SWATCH_INPUT_TYPE_KEY);

        $additionalData = array_merge($initialAdditionalData, $dataToAdd);
        $attribute->setData('additional_data', $this->serialize($additionalData));
    }

    /**
     * @param Attribute $attribute
     *
     * @return array
     * @throws \Zend_Serializer_Exception
     */
    public function getAdditionalData($attribute)
    {
        $additionalData = (string)$attribute->getData('additional_data');
        if (!empty($additionalData)) {
            return $this->unserialize($additionalData);
        }

        return [];
    }

    /**
     * Returns array of user defined attribute codes
     *
     * @param string $entityTypeCode
     *
     * @return array
     * @throws LocalizedException
     */
    public function getUserDefinedAttributeCodes($entityTypeCode)
    {
        if (empty($this->userDefinedAttributeCodes[$entityTypeCode])) {
            $this->userDefinedAttributeCodes[$entityTypeCode] = [];
            foreach ($this->eavConfig->getEntityAttributeCodes($entityTypeCode) as $attrCode) {
                $attribute = $this->eavConfig->getAttribute($entityTypeCode, $attrCode);
                if ($attribute->getIsUserDefined()) {
                    $this->userDefinedAttributeCodes[$entityTypeCode][] = $attribute->getAttributeCode();
                }
            }
        }

        return $this->userDefinedAttributeCodes[$entityTypeCode];
    }

    /**
     * @param $entityType
     * @param $formCode
     * @param bool $bypassFilter
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAttributeWithFilters($entityType, $formCode, $bypassFilter = false)
    {
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection($entityType, $formCode);
        $result = [];

        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisible() && ($bypassFilter || $this->filterAttribute($attribute))) {
                $result[] = $attribute;
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Eav\Model\Attribute $attribute
     *
     * @return bool
     * @throws LocalizedException
     */
    public function filterAttribute($attribute)
    {
        $storeId = $this->getScopeId();
        $groupId = $this->getGroupId();
        $stores = $attribute->getMpStoreId() ?: 0;
        $stores = explode(',', $stores);
        $groups = $attribute->getMpCustomerGroup() ?: 0;
        $groups = explode(',', $groups);

        $isVisibleStore = in_array(0, $stores) || in_array($storeId, $stores);

        return $isVisibleStore && in_array($groupId, $groups);
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getScopeId()
    {
        $scope = $this->_request->getParam(ScopeInterface::SCOPE_STORE) ?: $this->storeManager->getStore()->getId();

        if ($website = $this->_request->getParam(ScopeInterface::SCOPE_WEBSITE)) {
            $scope = $this->storeManager->getWebsite($website)->getDefaultStore()->getId();
        }

        return $scope;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getGroupId();
        }

        return 0;
    }

    /**
     * Check the current page is OSC
     *
     * @return bool
     */
    public function isOscPage()
    {
        $moduleEnable = $this->isModuleOutputEnabled('Mageplaza_Osc');
        $isOscModule = ($this->_request->getRouteName() == 'onestepcheckout');

        return $moduleEnable && $isOscModule;
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return self::TEMPLATE_MEDIA_PATH . '/tmp';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseTmpMediaPath();
    }

    /**
     * @param $file
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * Move file from temporary directory into base directory
     *
     * @param $file
     *
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function moveTemporaryFile($file)
    {
        /** @var \Magento\Framework\Filesystem $fileSystem */
        $fileSystem = $this->getObject('Magento\Framework\Filesystem');
        $directoryRead = $fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $directoryWrite = $fileSystem->getDirectoryWrite(DirectoryList::MEDIA);

        $path = $this->getBaseTmpMediaPath() . '/' . $file['file'];
        $newName = Uploader::getNewFileName($directoryRead->getAbsolutePath($path));
        $newPath = self::TEMPLATE_MEDIA_PATH . Uploader::getDispretionPath($newName);

        if (!$directoryWrite->create($newPath)) {
            throw new LocalizedException(
                __('Unable to create directory %1.', $newPath)
            );
        }

        if (!$directoryWrite->isWritable($newPath)) {
            throw new LocalizedException(
                __('Destination folder is not writable or does not exists.')
            );
        }

        $directoryWrite->renameFile($path, $newPath . '/' . $newName);

        return Uploader::getDispretionPath($newName) . '/' . $newName;
    }
}
