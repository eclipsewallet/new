define([
    'jquery',
    'uiRegistry'
], function ($ ,registry) {
    'use strict';

    return function (validator) {
        $(document).on("change","[name='tax_id']",function () {
            var validate = registry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.vat_id").validation;
            
            if($(".checkout-billing-address").css('display') == 'none')
            {
                if(this.checked){
                    validate['required-entry'] = true;
                    //VAT
                    $("[name='shippingAddress.vat_id']").addClass("_required");
                    $("[name='shippingAddress.vat_id']").removeClass("col-mp");
                    $("[name='shippingAddress.vat_id']").removeClass("mp-6");
                    $("[name='shippingAddress.vat_id']").appendTo(".requestidt");
                    $("[name='shippingAddress.vat_id']").show();

                } else {
                    validate['required-entry'] = false;
                    $("[name='shippingAddress.vat_id']").removeClass("_required");
                    $("[name='shippingAddress.vat_id']").removeClass("_error");
                    $("[name='shippingAddress.vat_id']").hide();
                }
            }
            else{
                if(this.checked){
                    validate['required-entry'] = true;
                    //VAT
                    $("[name='shippingAddress.vat_id']").addClass("_required");
                    $("[name='shippingAddress.vat_id']").removeClass("col-mp");
                    $("[name='shippingAddress.vat_id']").removeClass("mp-6");
                    $("[name='shippingAddress.vat_id']").appendTo(".requestidt");
                    $("[name='shippingAddress.vat_id']").show();
                } else {
                    validate['required-entry'] = false;
                    $("[name='shippingAddress.vat_id']").removeClass("_required");
                    $("[name='shippingAddress.vat_id']").hide();
                }
            }
        });       
        
        $(document).on("keyup","[name='shippingAddress.vat_id'] [name='vat_id']",function(){
            if($(".checkout-billing-address").css('display') !== 'none'){
                registry.get("checkout.steps.shipping-step.billingAddress.billing-address-fieldset.vat_id").value($(this).val());
            }
        });

        return validator;
        
    };

});