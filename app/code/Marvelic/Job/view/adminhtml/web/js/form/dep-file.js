define(
    [
    'Magento_Ui/js/form/element/abstract'
    ],
    function (Element) {
        'use strict';

        return Element.extend(
            {
                defaults: {
                    valuesForOptions : [],
                    imports          : {
                        toggleVisibility: '${$.parentName}.export_source_job:value'
                    },
                    isShown          : false,
                    inverseVisibility: false,
                    visible:false
                },

                toggleVisibility: function (selected) {
                    this.isShown = selected in this.valuesForOptions;
                    this.visible(this.inverseVisibility ? !this.isShown : this.isShown);
                }
            }
        );
    }
);