/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomerAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'Mageplaza_CustomerAttributes/js/init-editor-content'
], function ($, initEditorContent) {
    'use strict';

    return function (Component) {
        return Component.extend({
            attributes: window.customerAttributes ? window.customerAttributes.contentType : [],

            initialize: function () {
                this._super();

                var self = this;

                $.each(this.attributes, function (index, attribute) {
                    if (attribute.attribute_code === self.index && attribute.additional_data) {
                        $.async({component: self, selector: '#' + self.uid}, function (element) {
                            initEditorContent($(element));
                        }.bind(self));
                    }
                });

                return this;
            }
        })
    }
});
