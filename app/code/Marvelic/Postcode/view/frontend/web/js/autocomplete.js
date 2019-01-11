define([
    'jquery',
    'thailand',
    'uiComponent',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'Marvelic_Postcode/js/Thailand_loader',
    'text!Marvelic_Postcode/js/database/raw_database/raw_database1.json'
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

                    var automation = function(country_code) {
                        if(country_code) {
                            keepInterval = false;
                            if (typeof(uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.district")) !== 'undefined') {
                                var districtAddress = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.district").uid;
                                var cityAddress = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city").uid;
                                var provinceAddress = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id_input").uid;
                                var postcodeAddress = uiRegistry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode").uid;
                            }
                            
                            //var dbUrl = require.toUrl('') + '/Marvelic_Postcode/js/database/'+country_code+'.json';
                            var dbUrl = require.toUrl('') + '/Marvelic_Postcode/js/database/raw_database/raw_database1.json';
                            $.ajax({
                                url: dbUrl,
                                error: function()
                                {
                                   console.log('File does not exists');
                                },
                                success: function()
                                {
                                    $.Thailand.setup({
                                        database: dbUrl // path หรือ url ไปยัง database
                                    });
                                    $.Thailand({
                                        $district_en: $('#' + districtAddress), // input ของตำบล
                                        $amphoe_en: $('#' + cityAddress), // input ของอำเภอ
                                        $province_en: $('#' + provinceAddress), // input ของจังหวัด
                                        $zipcode: $('#' + postcodeAddress), // input ของรหัสไปรษณีย์
                                        onLoad: function () {
                                            console.info('Autocomplete is ready!');
                                        }
                                    });
                                }
                            });
                                                   
                        }
                    }

                    automation(select.val());

                    select.on('change', function() {
                        var country_code = $(this).val();          
                        automation(country_code); 
                    });

                } else{
                    console.log('Not yet load field');
                }

            }, 500);
        }).fail(function () {
            console.error("ERROR: library failed to load");
        });
    }

    return Component;
});
