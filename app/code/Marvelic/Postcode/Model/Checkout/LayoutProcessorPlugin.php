<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21/12/2017
 * Time: 11:01
 */

namespace Marvelic\Postcode\Model\Checkout;


class LayoutProcessorPlugin
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street'] = [
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Street Address'),// I removed main label
            'required' => true, //turn false because I removed main label
            'dataScope' => 'shippingAddress.street',
            'provider' => 'checkoutProvider',
            'sortOrder' => 70,
            'type' => 'group',
            'additionalClasses' => 'street',
            'children' => [
                [
//                    'label' => __('Street Address '),
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Marvelic_Postcode/form/element/address'
                    ],
                    'dataScope' => '0',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255],
                ]

            ]
        ];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['vat_id']['validation'] = ['required-entry' => false, "min_text_len‌​gth" => 1, "max_text_length" => 255];
//        * config: checkout/options/display_billing_address_on = payment_method */
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']
        )) {

            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                     ['payment']['children']['payments-list']['children'] as $key => $payment) {

                $method = substr($key, 0, -5);

                /* district */
                if (isset($payment['children']['form-fields']['children']['district'])) {

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                    ['district']['sortOrder'] = 75;
                }

                /* street */
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                ['street'] = [
                    'component' => 'Magento_Ui/js/form/components/group',
                    'label' => __('Street Address'),// I removed main label
                    'required' => true, //turn false because I removed main label
                    'dataScope' => 'billingAddress'.$method.'.street',
                    'provider' => 'checkoutProvider',
                    'sortOrder' => 70,
                    'type' => 'group',
                    'additionalClasses' => 'street',
                    'children' => [
                        [
//                    'label' => __('Street Address '),
                            'component' => 'Magento_Ui/js/form/element/abstract',
                            'config' => [
                                'customScope' => 'billingAddress'.$method,
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'Marvelic_Postcode/form/element/address'
                            ],
                            'dataScope' => '0',
                            'provider' => 'checkoutProvider',
                            'validation' => ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255],
                        ]

                    ]
                ];
            }
        }

        /* config: checkout/options/display_billing_address_on = payment_page */
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['billing-address-form']
        )) {
            $method = 'shared';

            /* street */
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']
            ['children']['street'] = [
                'component' => 'Magento_Ui/js/form/components/group',
                'label' => __('Street Address'),// I removed main label
                'required' => true, //turn false because I removed main label
                'dataScope' => 'billingAddress'.$method.'.street',
                'provider' => 'checkoutProvider',
                'sortOrder' => 70,
                'type' => 'group',
                'additionalClasses' => 'street',
                'children' => [
                    [
//                    'label' => __('Street Address '),
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'config' => [
                            'customScope' => 'billingAddress'.$method,
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'Marvelic_Postcode/form/element/address'
                        ],
                        'dataScope' => '0',
                        'provider' => 'checkoutProvider',
                        'validation' => ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255],
                    ]
                ]
            ];
        }

        return $jsLayout;
    }

}