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
    'Magento_Ui/js/grid/columns/select',
    'jquery/ui',
], function ($, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html'
        },
        getLabel: function (record) {
            var rowPrefix = "tr[data-repeat-index='" + record._rowIndex + "'] ",
                labelImgSrc = record.label_template || record.label_image,
                labelText = record.label || '',
                font = record.label_font,
                fontSize = record.label_font_size,
                color = record.label_color,
                label_position = record.label_position,
                customCss = record.label_css,
                labelData = JSON.parse(label_position),
                width = 0,
                height = 0,
                labelWrapperStyle = 'margin:auto; position: relative;';

            if (labelImgSrc) {
                width = labelData.label.width;
                height = labelData.label.height;
                labelWrapperStyle += 'width:{{width}}px; height:{{height}}'
                    .replace("{{width}}", width)
                    .replace("{{height}}", height)
                ;
            }

            var labelImgStyle = 'width:{{width}}px; height:{{height}}'
                .replace("{{width}}", width)
                .replace("{{height}}", height)
            ;
            var labelTextStyle = 'font-family:{{font}}; font-size:{{fontSize}}px; color:{{color}}'
                .replace("{{font}}", font)
                .replace("{{fontSize}}", fontSize)
                .replace("{{color}}", color)
            ;
            var rowStyle = rowPrefix + customCss;

            var labelTpl = '<div id="design-labels" style="{{labelWrapperStyle}}">\n' +
                '    <img src="{{labelImgSrc}}" id="design-label-image" style="{{labelImgStyle}}" />\n' +
                '    <span id="design-label-text" style="{{labelTextStyle}}">{{labelText}}</span>\n' +
                '</div>' +
                '<style>{{rowStyle}}</style>';

            return labelTpl
                .replace("{{labelWrapperStyle}}", labelWrapperStyle)
                .replace("{{labelImgSrc}}", labelImgSrc)
                .replace("{{labelImgStyle}}", labelImgStyle)
                .replace("{{labelTextStyle}}", labelTextStyle)
                .replace("{{labelText}}", labelText)
                .replace("{{rowStyle}}", rowStyle)
                ;
        }
    });
});