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
        if (window.checkoutConfig.storeCode == "th") {
            Thailand_loader.done(function () {

                // BEGIN
                // check until all field ready

                var keepInterval = true;
                var intervalAutocomplete = setInterval(function () {
                    if (!keepInterval) {
                        clearInterval(intervalAutocomplete);
                        return;
                    }

                    if (uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.country_id")
                        && uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.city")
                        && uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.region_id_input")
                        && uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.postcode")) {

                        select = $('#' + uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.country_id").uid);
                        if (select.val()) {

                            keepInterval = false;
                            var dbUrl = require.toUrl('') + '/Marvelic_Postcode/js/database/db.json';

                            $.Thailand.setup({
                                database: dbUrl // path หรือ url ไปยัง database
                            });
                            if (typeof(uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.district")) !== 'undefined') {
                                var bilingDistrict = uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.district").uid,
                                    billingCity = uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.city").uid,
                                    billingPostcode = uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.postcode").uid,
                                    billingProvince = uiRegistry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.region_id_input").uid;
                            }
                            if (select.val() === 'TH') {
                                $.Thailand({
                                    $district: $('#' + bilingDistrict), // input ของตำบล
                                    $amphoe: $('#' + billingCity), // input ของอำเภอ
                                    $province: $('#' + billingProvince), // input ของจังหวัด
                                    $zipcode: $('#' + billingPostcode), // input ของรหัสไปรษณีย์
                                    onLoad: function () {
                                        console.info('Billing Autocomplete is ready!');
                                    }
                                });
                            }
                        }

                    } else {
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
