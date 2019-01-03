define([
    'jquery',
    'thailand',
    'uiComponent',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'Marvelic_Postcode/js/Thailand_loader',
    'text!Marvelic_Postcode/js/database/db.json'
], function ($,
             Thailand,
             Component,
             checkoutData,
             uiRegistry,
             Thailand_loader) {
    var config = window.checkoutConfig.postcode.options.enable;
    var method = window.checkoutConfig.postcode.activePaymentMethods;

    delete method.paypal_billing_agreement;
    delete method.free;

    if (config === '1') {
        if(window.checkoutConfig.storeCode == "th"){
            Thailand_loader.done(function () {
                // BEGIN
                // check until all field ready
                var keepInterval = true;
                var intervalAutocomplete = setInterval(function(){
                    if (!keepInterval) {
                        clearInterval(intervalAutocomplete);
                        return;
                    }

                    if (uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.country_id")
                        && uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city")
                        && uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id_input")
                        && uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode")) {

                        select = $('#'+uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.country_id").uid);
                        if (select.val()) {

                            keepInterval = false;
                            var dbUrl = require.toUrl('') + '/Marvelic_Postcode/js/database/db.json';

                            $.Thailand.setup({
                                database: dbUrl // path หรือ url ไปยัง database
                            });
                            if (typeof(uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.district")) !== 'undefined') {
                                var districtAddress = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.district").uid,
                                    cityAddress = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city").uid,
                                    provinceAddress = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id_input").uid,
                                    postcodeAddress = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode").uid,
                                    addressSelect = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.country_id").uid;
                            }
                            if (select.val() === 'TH') {
                                $.Thailand({
                                    $district: $('#' + districtAddress), // input ของตำบล
                                    $amphoe: $('#' + cityAddress), // input ของอำเภอ
                                    $province: $('#' + provinceAddress), // input ของจังหวัด
                                    $zipcode: $('#' + postcodeAddress), // input ของรหัสไปรษณีย์
                                    onLoad: function () {
                                        console.info('Autocomplete is ready!');
                                    }
                                });
                            }
                        }
                    } else{
                        console.log('Not yet load field');
                    }

                }, 500);
            }).fail(function () {
                console.error("ERROR: library failed to load");
            });
        }
    }

    return Component;
});
