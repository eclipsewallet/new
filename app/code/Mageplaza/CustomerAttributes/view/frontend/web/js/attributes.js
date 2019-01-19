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
    'underscore'
], function($, _) {
    'use strict';

    return function(config) {
        var confgAttr = config.attributes;
        $.each(confgAttr, function(index, attribute) {
            var elem = $('[name="' + attribute.attribute_code + '"]');

            elem.prop('disabled', !!!attribute.customer_can_edit);

            checkDependency(elem);

            if (elem.length > 1){
                elem.each(function(index, elemChild) {
                    if (elemChild.type === 'radio' && elemChild.checked){
                        $(elemChild).trigger('change');
                    }
                });
            } else {
                elem.trigger('change');
            }
        });

        function checkDependency(elem) {
            elem.on('change', function() {
                var el      = this;
                var attrObj = _.findWhere(confgAttr, {attribute_code: el.name});
                var attrId  = attrObj["attribute_id"];

                var dependConfigs = _.filter(confgAttr, function(el) {
                    return el.value_depend
                });
                var dependAttr    = _.findWhere(dependConfigs, {field_depend: attrId});

                if (dependAttr){
                    var attrCode = dependAttr.attribute_code;
                    if (dependAttr.frontend_input === 'multiselect'){
                        attrCode += '[]';
                    }
                    var dependElem = $('[name="' + attrCode + '"]');
                    if (dependElem.length){
                        var valueDepend = dependAttr.value_depend.split(',');
                        if ($.inArray(attrId + '_' + el.value, valueDepend) !== -1){
                            dependElem.prop('disabled', false);
                            dependElem.parents('.field').show();
                        } else {
                            dependElem.prop('disabled', true);
                            dependElem.parents('.field').hide();
                        }
                    }
                }
            });
        }
    };
});
