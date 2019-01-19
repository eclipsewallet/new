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

namespace Mageplaza\ProductLabels\Block\Listing;

use Mageplaza\ProductLabels\Block\Label as AbstractLabel;
use Mageplaza\ProductLabels\Helper\Data as HelperData;

/**
 * Class Label
 * @package Mageplaza\ProductLabels\Block\Listing
 */
class Label extends AbstractLabel
{
    /**
     * get Style text label
     *
     * @param $rule
     *
     * @return string
     */
    public function getTextStyle($rule)
    {
        if ($rule->getSame()) {
            $font  = $rule->getLabelFont();
            $color = $rule->getLabelColor();
        } else {
            $font  = $rule->getListFont();
            $color = $rule->getListColor();
        }

        return 'font-family:' . $font . '; color:' . $color;
    }

    /**
     * Get position, size of label on listing page
     *
     * @param $rule
     *
     * @return string
     */
    public function getLabelStyle($rule)
    {
        if ($rule->getSame()) {
            $fontSize = $rule->getLabelFontSize();
            $posData  = HelperData::jsonDecode($rule->getLabelPosition());
        } else {
            $fontSize = $rule->getListFontSize();
            $posData  = HelperData::jsonDecode($rule->getListPosition());
        }
        $width  = $posData['label']['width'] * 100 / $this->getProductImgWidth();
        $height = $posData['label']['height'] * 100 / $this->getProductImgHeight();
        $top    = (($this->getProductImgHeight() - $posData['label']['height']) * $posData['label']['percentTop'] / 100) / $this->getProductImgHeight() * 100;
        $left   = (($this->getProductImgWidth() - $posData['label']['width']) * $posData['label']['percentLeft'] / 100) / $this->getProductImgWidth() * 100;

        return 'width:' . $width . '%; height:' . $height . '%; top:' . $top . '%; left:' . $left . '%; font-size:' . $fontSize . 'px';
    }

    /**
     * Get Src image label
     *
     * @param $rule
     *
     * @return string
     */
    public function getSrcImg($rule)
    {
        if ($rule->getSame()) {
            if ($labelTemplate = $rule->getLabelTemplate()) {
                return $labelTemplate;
            } else if ($labelImage = $rule->getLabelImage()) {
                return $this->_helperData->getImageUrl($labelImage, 'product');
            }
        } else {
            if ($listTemplate = $rule->getListTemplate()) {
                return $listTemplate;
            } else if ($listImage = $rule->getListImage()) {
                return $this->_helperData->getImageUrl($listImage, 'listing');
            }
        }

        return null;
    }

    /**
     * get Label on listing page
     *
     * @param $rule
     *
     * @return mixed
     */
    public function getLabel($rule)
    {
        return $rule->getSame() ? $rule->getLabel() : $rule->getListLabel();
    }

    /**
     * Replace id custom css
     *
     * @param $rule
     * @param $id
     *
     * @return mixed
     */
    public function replaceCustomCss($rule, $id)
    {
        if ($rule->getSame()) {
            $customCss = $rule->getLabelCss();
            $search    = [
                'design-labels',
                'design-label-image',
                'design-label-text',
            ];
            $replace   = [
                'design-labels-' . $rule->getId() . '-' . $id,
                'design-label-image-' . $rule->getId() . '-' . $id,
                'design-label-text-' . $rule->getId() . '-' . $id,
            ];

            return str_replace($search, $replace, $customCss);
        }

        $customCss = $rule->getListCss();
        $search    = [
            'design-labels-list',
            'design-label-image-list',
            'design-label-text-list',
        ];
        $replace   = [
            'design-labels-' . $rule->getId() . '-' . $id,
            'design-label-image-' . $rule->getId() . '-' . $id,
            'design-label-text-' . $rule->getId() . '-' . $id,
        ];

        return str_replace($search, $replace, $customCss);
    }

    /**
     * get width image product on listing page
     *
     * @return string
     */
    public function getProductImgWidth()
    {
        return $this->_gallery->getImageAttribute('category_page_list', 'width');
    }

    /**
     * get height image product on listing page
     *
     * @return string
     */
    public function getProductImgHeight()
    {
        return $this->_gallery->getImageAttribute('category_page_list', 'height');
    }
}