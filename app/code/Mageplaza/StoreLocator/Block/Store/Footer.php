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

namespace Mageplaza\StoreLocator\Block\Store;

use Mageplaza\StoreLocator\Block\Frontend;

/**
 * Class Frontend
 * @package Mageplaza\StoreLocator\Block
 */
class Footer extends Frontend
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_StoreLocator::storelocator/footer.phtml';

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStaticContent()
    {
        $block = $this->_helperData->getConfigGeneral('bottom_static_block');

        $cmsBlock = $this->_blockFactory->create();
        $cmsBlock->load($block, 'identifier');

        $html = '';
        if ($cmsBlock && $cmsBlock->getId()) {
            $html = $this->getLayout()->createBlock('Magento\Cms\Block\Block')
                ->setBlockId($cmsBlock->getId())
                ->toHtml();
        }

        return $html;
    }
}
