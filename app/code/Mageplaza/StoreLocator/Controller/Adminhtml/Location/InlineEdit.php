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
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\StoreLocator\Model\LocationFactory;

/**
 * Class InlineEdit
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Location
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $jsonFactory;

    /**
     * File Factory
     *
     * @var \Mageplaza\StoreLocator\Model\LocationFactory
     */
    public $locationFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param LocationFactory $locationFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        LocationFactory $locationFactory
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->locationFactory = $locationFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $locationItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($locationItems))) {
            return $resultJson->setData([
                                            'messages' => [__('Please correct the data sent.')],
                                            'error'    => true,
                                        ]);
        }

        $key = array_keys($locationItems);
        $locationId = !empty($key) ? (int)$key[0] : '';
        /** @var \Mageplaza\StoreLocator\Model\Location $location */
        $location = $this->locationFactory->create()->load($locationId);
        try {
            $locationData = $locationItems[$locationId];
            $location->addData($locationData)->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithLocationId($location, $e->getMessage());
            $error = true;
        } catch (\RuntimeException $e) {
            $messages[] = $this->getErrorWithLocationId($location, $e->getMessage());
            $error = true;
        } catch (\Exception $e) {
            $messages[] = $this->getErrorWithLocationId(
                $location,
                __('Something went wrong while saving the Location.')
            );
            $error = true;
        }

        return $resultJson->setData([
                                        'messages' => $messages,
                                        'error'    => $error
                                    ]);
    }

    /**
     * Add Location id to error message
     *
     * @param \Mageplaza\StoreLocator\Model\Location $location
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithLocationId(\Mageplaza\StoreLocator\Model\Location $location, $errorText)
    {
        return '[Location ID: ' . $location->getId() . '] ' . $errorText;
    }
}
