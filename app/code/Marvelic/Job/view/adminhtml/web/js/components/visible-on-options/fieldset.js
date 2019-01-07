define(
    [
        'Magento_Ui/js/form/components/fieldset'
    ],
    function (Fieldset) {
        'use strict';
        return Fieldset.extend(
            {
                defaults: {
                    valuesForOptions: [],
                    imports: {
                        toggleVisibility: '${$.parentName}.settings.entity:value'
                    },
                    openOnShow: true,
                    isShown: false,
                    inverseVisibility: false
                },

                /**
                 * Toggle visibility state.
                 *
                 * @param {Number} selected
                 */
                toggleVisibility: function (selected) {
                    this.isShown = (selected != undefined);
                    this.visible(this.inverseVisibility ? !this.isShown : this.isShown);
                    if (this.openOnShow) {
                        this.opened(this.inverseVisibility ? !this.isShown : this.isShown);
                    }
                },
                initConfig: function () {
                    this._super();
                    return this;
                },
            }
        );
    }
);