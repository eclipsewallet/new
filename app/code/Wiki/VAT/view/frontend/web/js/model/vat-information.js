define(
    [
        'ko',
        'uiComponent',
        'Magento_Customer/js/model/customer'
    ],
    function (ko, Component,customer) {
        'use strict';

        return Component.extend({
            vatNumber: ko.observable(),
            vatCompany: ko.observable(),
            vatAddress: ko.observable(),
            vatSave: ko.observable()
        });
    }
);
