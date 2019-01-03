<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 07/12/2017
 * Time: 13:51
 */

namespace Marvelic\Postcode\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\ObjectManager;

class PostcodeConfigProvider implements ConfigProviderInterface
{
    public function getConfig()
    {
        $objectManager = ObjectManager::getInstance();
        $helper = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $enable = $helper->getValue('postcode/options/enable');
        $paymentConfig = $objectManager->get('Magento\Payment\Model\Config');
        $activePaymentMethods = $paymentConfig->getActiveMethods();
        $config = [
            'postcode' => [
                'options' => [
                    'enable' => $enable,
                ],
                'activePaymentMethods' => $activePaymentMethods
            ]
        ];

        return $config;
    }
}