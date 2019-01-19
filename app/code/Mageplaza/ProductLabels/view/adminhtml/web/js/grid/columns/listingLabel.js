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
                same = record.same,
                labelImgSrc = record.label_template || record.label_image,
                labelText = record.label || '',
                font = record.label_font,
                fontSize = record.label_font_size,
                color = record.label_color,
                label_position = record.label_position,
                labelData = JSON.parse(label_position),
                customCss = record.label_css,
                width = 0,
                height = 0,
                labelWrapperStyle = 'margin:auto; position: relative;';

            var tmpWrapperId = 'design-labels';
            var tmpImgId = 'design-label-image';
            var tmpTextId = 'design-label-text';

            if (same === '0') {
                labelImgSrc = record.list_template || record.list_image;
                labelText = record.list_label || '';
                font = record.list_font;
                fontSize = record.list_font_size;
                color = record.list_color;
                label_position = record.list_position;
                labelData = JSON.parse(label_position);
                customCss = record.list_css;

                tmpWrapperId += '-list';
                tmpImgId += '-list';
                tmpTextId += '-list';
            }

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

            var labelTpl = '<div id="{{tmpWrapperId}}" style="{{labelWrapperStyle}}">\n' +
                '    <img src="{{labelImgSrc}}" id="{{tmpImgId}}" style="{{labelImgStyle}}" />\n' +
                '    <span id="{{tmpTextId}}" style="{{labelTextStyle}}">{{labelText}}</span>\n' +
                '</div>' +
                '<style>{{rowStyle}}</style>';

            return labelTpl
                .replace("{{tmpWrapperId}}", tmpWrapperId)
                .replace("{{tmpImgId}}", tmpImgId)
                .replace("{{tmpTextId}}", tmpTextId)
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