<?php
if(!class_exists('ExtensionPaypalCommerce')){
    require_once(__DIR__ . DIRECTORY_SEPARATOR .'vendor'.DIRECTORY_SEPARATOR.'autoload.php');
    require_once(__DIR__.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'paypal_commerce_hooks.php');
    require_once(__DIR__.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'paypal_commerce_modules.php');
}

if(!defined('PAYPAL_SUPPORTED_CURRENCIES')){
    define(
        'PAYPAL_SUPPORTED_CURRENCIES',
        [
            'AUD','BRL','CAD','CNY','CZK','DKK','EUR',
            'HKD','HUF','ILS','JPY','MYR','MXN','TWD',
            'NZD','NOK','PHP','PLN','GBP','RUB','SGD',
            'SEK','CHF','THB','USD'
        ]
    );
}

$controllers = [
    'storefront' => [
        'responses/extension/paypal_commerce'
    ],
    'admin'      => [
        'responses/extension/paypal_commerce'
    ],
];

$models = [
    'storefront' => [
        'extension/paypal_commerce',
    ],
    'admin'      => [
        'extension/paypal_commerce'
    ],
];

$languages = [
    'storefront' => [
        'paypal_commerce/paypal_commerce',
    ],
    'admin'      => [
        'paypal_commerce/paypal_commerce',
    ],
];

$templates = [
    'storefront' => [
        'responses/paypal_commerce_confirm.tpl'
    ],
    'admin'      => [
        'pages/sale/paypal_commerce_payment_details.tpl',
        'responses/extension/paypal_commerce_connect.tpl',
        'responses/extension/paypal_commerce_test.tpl',
        'responses/extension/paypal_commerce_manual_connect.tpl',
        'responses/extension/paypal_commerce_note_wrapper.tpl'
    ]
];
