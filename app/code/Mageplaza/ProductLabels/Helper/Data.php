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

namespace Mageplaza\ProductLabels\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;

/**
 * Class Data
 * @package Mageplaza\ProductLabels\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'productlabels';

    /**
     * @var RuleCollection
     */
    protected $_ruleCollection;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param RuleCollection $ruleCollection
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        RuleCollection $ruleCollection
    )
    {
        $this->_ruleCollection = $ruleCollection;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param $rule
     * @return \Magento\Framework\Phrase
     */
    public function getState($rule)
    {
        if ($rule->getId()) {
            $toDate      = strtotime($rule->getToDate());
            $fromDate    = strtotime($rule->getFromDate());
            $currentDate = strtotime(date('d-m-Y H:i:s'));

            if (($toDate >= $currentDate && $fromDate <= $currentDate) || (!$toDate && $fromDate <= $currentDate)) {
                return __('Running');
            } else if ($fromDate > $currentDate) {
                return __('Queue');
            } else if ($toDate < $currentDate) {
                return __('Done');
            }
        }

        return __('None');
    }

    /**
     * @param        $image
     * @param string $type
     *
     * @return string
     */
    public function getImageUrl($image, $type = Image::TEMPLATE_MEDIA_LABEL)
    {
        $imageHelper = $this->getImageHelper();
        $imageFile   = $imageHelper->getMediaPath($image, $type);

        return $imageHelper->getMediaUrl($imageFile);
    }

    /**
     * @return \Mageplaza\ProductLabels\Helper\Image
     */
    public function getImageHelper()
    {
        return $this->objectManager->get(Image::class);
    }
}