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

namespace Mageplaza\CustomerAttributes\Controller\Viewfile;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\MediaStorage\Model\File\Uploader;
use Mageplaza\CustomerAttributes\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class Upload
 * @package Mageplaza\CustomerAttributes\Controller\Viewfile
 */
class Upload extends Action
{
    /**
     * @var Data
     */
    private $data;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Zend_Validate_File_Upload
     */
    private $fileUpload;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param Data $data
     * @param Config $config
     * @param \Zend_Validate_File_Upload $fileUpload
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Data $data,
        Config $config,
        \Zend_Validate_File_Upload $fileUpload,
        LoggerInterface $logger
    )
    {
        $this->data = $data;
        $this->config = $config;
        $this->fileUpload = $fileUpload;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            if (empty($this->fileUpload->getFiles())) {
                throw new \Exception('$_FILES array is empty.');
            }

            $files = $this->convertFilesArray();

            /** @var Uploader $uploader */
            $uploader = $this->_objectManager->create(Uploader::class, ['fileId' => current($files)]);

            $attribute = $this->config->getAttribute('customer_address', key($files));
            if ($attribute->getFrontendInput() == 'image') {
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            }

            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);

            $result = $uploader->save($mediaDirectory->getAbsolutePath($this->data->getBaseTmpMediaPath()));

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $this->data->getTmpMediaUrl($result['file']);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $result = [
                'error'     => __($e->getMessage()),
                'errorcode' => $e->getCode(),
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }

    /**
     * @return array
     * @throws \Zend_Validate_Exception
     */
    private function convertFilesArray()
    {
        $files = [];

        foreach ($this->fileUpload->getFiles()['custom_attributes'] as $itemKey => $item) {
            if (is_array($item)) {
                $files[key($item)][$itemKey] = current($item);
            }
        }

        return $files;
    }
}
