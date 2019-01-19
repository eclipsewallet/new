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

var config = {
    paths: {
        mpStoreLocator: 'Mageplaza_StoreLocator/js/mp.storelocator',
        owlSlider: 'Mageplaza_Core/js/owl.carousel.min'
    },
    shim: {
        'Mageplaza_StoreLocator/js/jquery.storelocator.js': ['jquery', 'jquery/ui'],
        owlSlider: ['jquery', 'jquery/ui']
    }
};