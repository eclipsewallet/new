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
class Head extends Frontend
{
    /**
     * Get head background image
     *
     * @return string
     */
    public function getBackgroundImage()
    {
        $background = $this->getViewFileUrl('Mageplaza_StoreLocator::media/default-background.png');
        $backgroundConfig = $this->_helperData->getConfigGeneral('upload_head_image');
        if ($backgroundConfig) {
            $background = $this->_helperImage->getBaseMediaUrl() . '/' . $this->_helperImage->getMediaPath($backgroundConfig, 'image');
        }

        return $background;
    }

    /**
     * Get head logo image
     *
     * @return string
     */
    public function getLogoImage()
    {
        $logo = $this->getViewFileUrl('Mageplaza_StoreLocator::media/default-logo.png');
        $logoConfig = $this->_helperData->getConfigGeneral('upload_head_icon');
        if ($logoConfig) {
            $logo = $this->_helperImage->getBaseMediaUrl() . '/' . $this->_helperImage->getMediaPath($logoConfig, 'image');
        }

        return $logo;
    }

    /**
     * Get head store title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getStoreTitle()
    {
        return ($this->_helperData->getConfigGeneral('title')) ?: __('Find a stores');
    }

    /**
     * Get head store description
     *
     * @return \Magento\Framework\Phrase
     */
    public function getStoreDescription()
    {
        return ($this->_helperData->getConfigGeneral('description')) ?: __('Do more of what you love. Discover inspiring programs happening every day at Apple.');
    }
}
