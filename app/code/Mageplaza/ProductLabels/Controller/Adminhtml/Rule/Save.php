<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\ProductLabels\Controller\Adminhtml\Rule;
use Mageplaza\ProductLabels\Helper\Image;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class Save
 * @package Mageplaza\ProductLabels\Controller\Adminhtml\Rule
 */
class Save extends Rule
{
    /**
     * JS helper
     *
     * @var \Magento\Backend\Helper\Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param RuleFactory $ruleFactory
     * @param Js $jsHelper
     * @param DateTime $date
     * @param Image $imageHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RuleFactory $ruleFactory,
        Js $jsHelper,
        DateTime $date,
        Image $imageHelper
    )
    {
        $this->jsHelper    = $jsHelper;
        $this->date        = $date;
        $this->imageHelper = $imageHelper;

        parent::__construct($ruleFactory, $registry, $context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPost('rule')) {
            /** @var \Mageplaza\ProductLabels\Model\Rule $rule */

            $rule = $this->initRule();

            /** get rule conditions */
            $rule->loadPost($data);
            $this->prepareData($rule, $data);
            $this->_eventManager->dispatch('mageplaza_productlabels_rule_prepare_save', ['post' => $rule, 'request' => $this->getRequest()]);

            try {
                $rule->save();

                $this->messageManager->addSuccessMessage(__('The rule has been saved.'));
                $this->_getSession()->setData('mageplaza_productlabels_rule_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mpproductlabels/*/edit', ['id' => $rule->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('mpproductlabels/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Rule.'));
            }

            $resultRedirect->setPath('mpproductlabels/*/edit', ['id' => $rule->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('mpproductlabels/*/');

        return $resultRedirect;
    }

    /**
     * @param       $rule
     * @param array $data
     *
     * @return $this
     */
    protected function prepareData($rule, $data = [])
    {
        $this->imageHelper->uploadImage($data, 'label_image', Image::TEMPLATE_MEDIA_PRODUCT_LABEL, $rule->getLabelImage());
        $this->imageHelper->uploadImage($data, 'list_image', Image::TEMPLATE_MEDIA_LISTING_LABEL, $rule->getListImage());

        if (is_null($rule->getCreatedAt())) {
            $data['created_at'] = $this->date->date();
        }

        if (is_null($rule->getFromDate())) {
            $data['from_date'] = $this->date->date();
        }

        $data['updated_at'] = $this->date->date();
        $rule->addData($data);

        return $this;
    }
}
