define(
    [
        'jquery',
        'ko',
        'underscore',
        'uiComponent',
        'uiRegistry',
        'Wiki_VAT/js/model/vat-data',
        'Wiki_VAT/js/model/vat-information',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Customer/js/model/customer',
        'mage/validation'
    ],
    function ($, ko, _, Component, registry, vatData, vatInformation, additionalValidators,customer) {
        'use strict';

        var cacheKeyvatNumber = 'vatNumber',
            cacheKeyvatCompany = 'vatCompany',
            cacheKeyvatAddress = 'vatAddress',
            cacheKeyvatSave = 'vatSave',
            cacheKeyIsRequireVat = 'vatRequire';

        function prepareSubscribeValue(object, cacheKey) {
            object(vatData.getData(cacheKey));
            object.subscribe(function (newValue) {
                vatData.setData(cacheKey, newValue);
            });
        }

        return Component.extend({
            defaults: {
                template: 'Wiki_VAT/container/vat-information'
            },
            vatNumber: vatInformation().vatNumber,
            vatCompany: vatInformation().vatCompany,
            vatAddress: vatInformation().vatAddress,
            vatSave: vatInformation().vatSave,

            initialize: function () {
                this._super();

                var self = this;

                prepareSubscribeValue(this.vatSave, cacheKeyvatSave);

                additionalValidators.registerValidator(this);
                return this;
            },
            initObservable: function () {
                this._super()
                    .observe({
                        isRequireVatVisible: vatData.getData(cacheKeyIsRequireVat)
                    });
                this.isRequireVatVisible.subscribe(function (newValue) {
                    vatData.setData(cacheKeyIsRequireVat, newValue);
                });
                if(customer.isLoggedIn()){
                    var vatInformation = customer.customerData.custom_attributes;
                    if(typeof vatInformation !== 'undefined'){
                        this.vatNumber = ko.observable(vatInformation.vat_number.value);
                        this.vatCompany = ko.observable(vatInformation.vat_company.value);
                        this.vatAddress = ko.observable(vatInformation.vat_address.value);
                    }
                }
                return this;
            },
            validate: function () {
                if(vatData.getData(cacheKeyIsRequireVat)){
                    var numberSelector = $('#wk-vat-number');
                    numberSelector.parents('form').validation();

                    var number = !!numberSelector.valid();
                    var company = !!$('#wk-vat-company').valid();
                    var address = !!$('#wk-vat-address').valid();

                    var result = number && company && address;

                    return result;
                }
            },
        });
    }
);
