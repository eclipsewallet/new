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
    'tinymce4',
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
], function ($, tinyMCE) {
    'use strict';

    return function (elem, repeat, breakdown) {
        var config = {
            width: $(elem).parent().hasClass('_with-tooltip') ? 'calc(100% - 36px)' : '100%',
            settings: {
                theme_advanced_buttons1: 'bold,italic,|,justifyleft,justifycenter,justifyright,|,fontsizeselect'
                + ',|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
                theme_advanced_buttons2: null,
                theme_advanced_buttons3: null,
                theme_advanced_buttons4: null
            },
            tinymce4:{
                'content_css': null,
            }
        };

        if (breakdown) {
            $.extend(config.settings, {
                theme_advanced_buttons1: 'bold,italic,|,justifyleft,justifycenter,justifyright,|,fontsizeselect',
                theme_advanced_buttons2: 'forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code'
            });
        }

        var editor = new wysiwygSetup(elem.attr('id'), config);
        if ($.isReady) {
            tinyMCE.dom.Event.domLoaded = true;
        }
        if (repeat) {
            editor.turnOff();
        }
        editor.wysiwygInstance.turnOn();
        elem.addClass('wysiwyg-editor').data('wysiwygEditor', editor);

        return elem;
    };
});

