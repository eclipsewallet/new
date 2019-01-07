define(
    [
        'Marvelic_Job/js/form/dep-file',
        'uiRegistry'
    ],
    function (Element, reg) {
        'use strict';

        return Element.extend(
            {
                defaults: {
                    listens: {
                        "${$.ns}.${$.ns}.behavior.behavior_field_file_format:value": "onChangeValue"
                    }
                },
                onChangeValue: function (value) {
                    if (this.isShown) {
                       this.value('');
                    }
                }
            }
        );
    }
);