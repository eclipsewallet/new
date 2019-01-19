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

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Holiday;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\StoreLocator\Model\HolidayFactory;

/**
 * Class InlineEdit
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Holiday
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
     * @var \Mageplaza\StoreLocator\Model\HolidayFactory
     */
    public $holidayFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param HolidayFactory $holidayFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        HolidayFactory $holidayFactory
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->holidayFactory = $holidayFactory;

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
        $holidayItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($holidayItems))) {
            return $resultJson->setData([
                                            'messages' => [__('Please correct the data sent.')],
                                            'error'    => true,
                                        ]);
        }

        $key = array_keys($holidayItems);
        $holidayId = !empty($key) ? (int)$key[0] : '';
        /** @var \Mageplaza\StoreLocator\Model\Holiday $holiday */
        $holiday = $this->holidayFactory->create()->load($holidayId);
        try {
            $holidayData = $holidayItems[$holidayId];
            $holiday->addData($holidayData)->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithHolidayId($holiday, $e->getMessage());
            $error = true;
        } catch (\RuntimeException $e) {
            $messages[] = $this->getErrorWithHolidayId($holiday, $e->getMessage());
            $error = true;
        } catch (\Exception $e) {
            $messages[] = $this->getErrorWithHolidayId(
                $holiday,
                __('Something went wrong while saving the Holiday.')
            );
            $error = true;
        }

        return $resultJson->setData([
                                        'messages' => $messages,
                                        'error'    => $error
                                    ]);
    }

    /**
     * Add Holiday id to error message
     *
     * @param \Mageplaza\StoreLocator\Model\Holiday $holiday
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithHolidayId(\Mageplaza\StoreLocator\Model\Holiday $holiday, $errorText)
    {
        return '[Holiday ID: ' . $holiday->getId() . '] ' . $errorText;
    }
}
