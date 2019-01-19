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

namespace Mageplaza\StoreLocator\Controller\StoreLocator;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Helper\Data as HelperData;

/**
 * Class LocationsData
 * @package Mageplaza\StoreLocator\Controller\StoreLocator
 */
class LocationsData extends Action
{
    /**
     * @var Frontend
     */
    protected $_frontEnd;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * LocationsData constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mageplaza\StoreLocator\Helper\Data $helperData
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Mageplaza\StoreLocator\Block\Frontend $frontend
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        JsonFactory $jsonResultFactory,
        Frontend $frontend
    )
    {
        $this->_helperData = $helperData;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->_frontEnd = $frontend;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->jsonResultFactory->create();
        $locationData = $this->_frontEnd->getDataLocations();
        if (count($locationData)) {
            $result->setData(json_decode($this->_frontEnd->getDataLocations()));
        }

        return $result;
    }
}
