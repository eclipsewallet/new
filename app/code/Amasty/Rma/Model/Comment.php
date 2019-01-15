<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Comment extends AbstractModel
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;

    /**
     * @var DateTime
     */
    protected $coreDate;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlBuilder;

    /**
     * @var \Amasty\Rma\Model\ResourceModel\File\CollectionFactory
     */
    protected $filesCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context                                               $context
     * @param \Magento\Framework\Registry                                                    $registry
     * @param ScopeConfigInterface                                                           $scopeConfig
     * @param ObjectManagerInterface                                                         $objectManager
     * @param \Amasty\Rma\Helper\Data                                                        $helper
     * @param DateTime                                                                       $coreDate
     * @param \Magento\Framework\Url                                                         $urlBuilder
     * @param \Amasty\Rma\Model\ResourceModel\File\CollectionFactory                                $filesCollection
     * @param \Amasty\Rma\Model\ResourceModel\Comment                                        $resource
     * @param \Amasty\Rma\Model\ResourceModel\Comment\Collection                             $resourceCollection
     * @param array                                                                          $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager,
        \Amasty\Rma\Helper\Data $helper,
        DateTime $coreDate,
        \Magento\Framework\Url $urlBuilder,
        \Amasty\Rma\Model\ResourceModel\File\CollectionFactory $filesCollectionFactory,
        \Amasty\Rma\Model\ResourceModel\Comment $resource = null,
        \Amasty\Rma\Model\ResourceModel\Comment\Collection $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
        $this->helper = $helper;
        $this->coreDate = $coreDate;
        $this->urlBuilder = $urlBuilder;
        $this->filesCollectionFactory = $filesCollectionFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rma\Model\ResourceModel\Comment');
    }

    /**
     * @param \Amasty\Rma\Model\Request $request
     * @param $data
     *
     * @return Comment
     * @throws LocalizedException
     * @throws \Exception
     *
     */
    function submit($request, $data)
    {
        $commentText = trim(strip_tags($data['value']));

        if (empty($commentText)) {
            $commentText = __(
                "Status has been changed to %1",
                $request->getStatusLabel()
            );

            $this->setData('is_empty', true);
        }

        $this->setData([
            'request_id'    => $request->getId(),
            'value'         => $commentText,
            'is_admin'      => $data['is_admin'],
            'unique_key'    => uniqid($request->getId())
        ]);

        $this->save();

        foreach ($data['files'] as $file) {
            /** @var \Amasty\Rma\Model\File $file */
            $fileModel = $this->objectManager->create('\Amasty\Rma\Model\File');
            /** @var \Magento\MediaStorage\Model\File\Uploader $upload */
            $upload = $this->helper->getUpload($file);

            if ($upload) {
                $maxSize = $this->scopeConfig->getValue(
                    'amrma/general/max_attachment_size',
                    ScopeInterface::SCOPE_STORE
                );

                if ($maxSize && $maxSize < $upload->getFileSize() / 1024 / 1024) {
                    throw new LocalizedException(
                        __('Attachment size exceeds %1 Mb', $maxSize)
                    );
                }

                $uploadResult = $upload->save($fileModel->getUploadDir());

                if (!$uploadResult) {
                    throw new LocalizedException(__('Unable upload file'));
                }
            }

            if (isset($uploadResult) && isset($fileModel)) {
                $fileModel->setData(
                    [
                        'comment_id' => $this->getId(),
                        'file'       => $uploadResult['file'],
                        'name'       => $uploadResult['name']
                    ]
                );
                $fileModel->save();
            }
        }

        return $this;
    }

    public function getCommentText()
    {
        return nl2br($this->getData('value'));
    }

    /**
     * @return \Magento\Sales\Model\Order
     * @throws LocalizedException
     */
    public function authenticate()
    {
        $ret = null;

        if ($this->getId()) {
            /** @var \Amasty\Rma\Model\Request $request */
            $request = $this->objectManager->create('\Amasty\Rma\Model\Request');
            $request->load($this->getData('request_id'));

            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->objectManager->create('\Magento\Sales\Model\Order');
            $order->load($request->getData('order_id'));

            if ($order->getId()) {
                return $order;
            } else {
                throw new LocalizedException(__('Order expired'));
            }
        } else {
            throw new LocalizedException(__('Wrong key'));
        }
    }

    /**
     * @return string
     */
    public function getCommentUrl()
    {
        return $this->urlBuilder->getUrl(
            'amasty_rma/request/commentLookup',
            [
                'key'    => $this->getData('unique_key'),
                '_nosid' => true
            ]
        );
    }

    /**
     * @return \Amasty\Rma\Model\Request|null
     */
    public function getRequest()
    {
        if (!$this->getData('request_id')) {
            return null;
        }

        if (!$this->hasData('request')) {
            /** @var  \Amasty\Rma\Model\Request $request */
            $request = $this->objectManager->create('\Amasty\Rma\Model\Request');
            $request->load($this->getData('request_id'));

            $this->setData('request', $request);
        }

        return $this->getData('request');
    }

    /**
     * @return File|null
     */
    public function getFile()
    {
        /** @var \Amasty\Rma\Model\File $file */
        $file = $this->objectManager->create('\Amasty\Rma\Model\File');

        $file->load($this->getId(), 'comment_id');

        return $file->getId() ? $file : null;
    }

    public function getFileUrl($file = null)
    {
        if ($file) {
            $url = $this->urlBuilder->getUrl(
                'amasty_rma/request/download',
                [
                    'id' => $file->getId(),
                    'code' => $this->getRequest()->getData('code'),
                    '_nosid' => true
                ]
            );

            return $url;
        } else {
            return false;
        }
    }

    /**
     * @return \Amasty\Rma\Model\ResourceModel\File\Collection
     */
    public function getFilesForComment()
    {
        $filesCollection = $this->filesCollectionFactory->create();

        /** @var \Amasty\Rma\Model\ResourceModel\File\Collection $filesCollection */
        return $filesCollection->addFilter('comment_id', $this->getId());
    }

    public function getFileName()
    {
        $file = $this->getFile();

        if ($file) {
            return $file->getData('name');
        } else {
            return false;
        }
    }
}
