<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Adminhtml\Request\Edit;

use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Comments extends \Magento\Backend\Block\Template
{
    /** @var  \Amasty\Rma\Model\Request */
    protected $request;

    /** @var  \Magento\Framework\Registry */
    protected $registry;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    
    /**
     * @var \Amasty\Rma\Model\Source\Status
     */
    protected $statusSource;

    /**
     * @var \Amasty\Rma\Model\Comment
     */
    protected $commentModel;

    /**
     * General constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param ObjectManagerInterface                  $objectManager
     * @param \Amasty\Rma\Model\Source\Status         $statusSource
     * @param \Amasty\Rma\Model\Comment               $commentModel
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        ObjectManagerInterface $objectManager,
        \Amasty\Rma\Model\Source\Status $statusSource,
        \Amasty\Rma\Model\Comment $commentModel,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->objectManager = $objectManager;

        parent::__construct($context, $data);
        $this->statusSource = $statusSource;
        $this->commentModel = $commentModel;
    }

    public function _construct()
    {
        $this->setTemplate('Amasty_Rma::request/comment.phtml');

        parent::_construct();
    }

    public function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->registry->registry('amrma_request');
        }

        return $this->request;
    }

    public function getComments()
    {
        $request = $this->getRequest();

        /** @var \Amasty\Rma\Model\ResourceModel\Comment\Collection $commentCollection */
        $commentCollection = $this->objectManager
            ->create('\Amasty\Rma\Model\ResourceModel\Comment\Collection');

        $commentCollection
            ->addFilter('request_id', $request->getId())
            ->setOrder('created_at', 'DESC');

        return $commentCollection;
    }

    /**
     * @param int $commentId
     * @return \Amasty\Rma\Model\ResourceModel\File\Collection
     */
    public function getFiles($commentId)
    {
        $this->commentModel->setId($commentId);

        return $this->commentModel->getFilesForComment();
    }

    public function getStatuses()
    {
        return $this->statusSource;
    }

    public function getIsNotifyCustomer()
    {
        return $this->_scopeConfig->isSetFlag(
            'amrma/email/notify_customer',
            ScopeInterface::SCOPE_STORE
        );
    }
}
