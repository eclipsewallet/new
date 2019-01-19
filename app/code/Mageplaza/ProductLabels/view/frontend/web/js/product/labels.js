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

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('mageplaza.productlabels', {

        /**
         * @inheritDoc
         */
        _create: function () {
            this.initLabel();
        },

        /**
         * Init Label
         */
        initLabel: function () {
            var i = 0,
                labelEl = $('#design-labels-' + this.options.ruleId + '-' + this.options.productId),
                imgLabelEl = $('#design-label-image-' + this.options.ruleId + '-' + this.options.productId),
                textLabelEl = $('#design-label-text-' + this.options.ruleId + '-' + this.options.productId),
                labelTopPercent = this.options.labelTopPercent,
                labelLeftPercent = this.options.labelLeftPercent,
                labelWidth = this.options.labelWidth,
                labelHeight = this.options.labelHeight,
                labelFontSize = parseInt(this.options.labelFontSize),
                labelFont = this.options.labelFont,
                labelColor = this.options.labelColor,
                galleryWidth = parseInt(this.options.galleryWidth),
                galleryHeight = parseInt(this.options.galleryHeight);

            var showLabel = setInterval(function () {
                var productImgEl = $('.fotorama__stage');
                i++;

                if (i > 60) {
                    clearInterval(showLabel);
                }

                if (productImgEl.length) {
                    clearInterval(showLabel);

                    if ($(imgLabelEl).attr('src')) {
                        imgLabelEl.css({
                            'width': '100%',
                            'height': '100%'
                        });
                    } else {
                        imgLabelEl.removeAttr('style');
                        /** fix src img label null **/
                        textLabelEl.css({
                            "-webkit-transform": "translate(0,0)"
                        })
                    }

                    textLabelEl.css({
                        'font-family': labelFont,
                        'color': labelColor
                    });

                    var top = ((galleryHeight - labelHeight) * labelTopPercent / 100) / galleryHeight * 100,
                        left = ((galleryWidth - labelWidth) * labelLeftPercent / 100) / galleryWidth * 100,
                        width = labelWidth * 100 / galleryWidth,
                        height = labelHeight * 100 / galleryHeight;
                    labelEl.css({
                        'width': width + '%',
                        'height': height + '%',
                        'font-size': labelFontSize,
                        'top': top + '%',
                        'left': left + '%'
                    });
                    productImgEl.prepend(labelEl);
                    labelEl.show();

                    labelEl.after('<div id="mpfotorama"></div>');
                    $('#mpfotorama').css({
                        'overflow': 'hidden',
                        'width': productImgEl.width(),
                        'height': productImgEl.height()
                    });
                    $(".fotorama__stage__shaft").appendTo("#mpfotorama");

                    /** spill template **/
                    $('.product.media').css('overflow', 'unset');
                    $('.fotorama-item').css('overflow', 'unset');
                    $('.fotorama__stage__shaft').css('overflow', 'hidden');
                    $('.fotorama__video-close').css('display', 'none');
                    productImgEl.css('overflow', 'unset');
                }
            }, 1000);
        }
    });

    return $.mageplaza.productlabels;
});

