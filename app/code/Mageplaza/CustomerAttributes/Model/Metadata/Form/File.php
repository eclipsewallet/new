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

namespace Mageplaza\CustomerAttributes\Model\Metadata\Form;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Model\FileProcessorFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;
use Mageplaza\CustomerAttributes\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class File
 * @package Mageplaza\CustomerAttributes\Model\Metadata\Form
 */
class File extends \Magento\Customer\Model\Metadata\Form\File
{
    /**
     * File constructor.
     *
     * @param TimezoneInterface $localeDate
     * @param LoggerInterface $logger
     * @param AttributeMetadataInterface $attribute
     * @param ResolverInterface $localeResolver
     * @param $value
     * @param string $entityTypeCode
     * @param bool $isAjax
     * @param EncoderInterface $urlEncoder
     * @param NotProtectedExtension $fileValidator
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     * @param FileProcessorFactory|null $fileProcessorFactory
     */
    public function __construct(
        TimezoneInterface $localeDate,
        LoggerInterface $logger,
        AttributeMetadataInterface $attribute,
        ResolverInterface $localeResolver,
        $value,
        string $entityTypeCode,
        bool $isAjax,
        EncoderInterface $urlEncoder,
        NotProtectedExtension $fileValidator,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        FileProcessorFactory $fileProcessorFactory = null)
    {
        if (is_array($value) && isset($value['value'])) {
            $value = $value['value'];
        }

        /** @var Data $helper */
        $helper = ObjectManager::getInstance()->get(Data::class);

        if ($helper->versionCompare('2.2.0')) {
            parent::__construct($localeDate, $logger, $attribute, $localeResolver, $value, $entityTypeCode, $isAjax, $urlEncoder, $fileValidator, $fileSystem, $uploaderFactory, $fileProcessorFactory);
        } else {
            parent::__construct($localeDate, $logger, $attribute, $localeResolver, $value, $entityTypeCode, $isAjax, $urlEncoder, $fileValidator, $fileSystem, $uploaderFactory);
        }
    }
}
