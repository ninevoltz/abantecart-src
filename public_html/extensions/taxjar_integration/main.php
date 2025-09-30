<?php
/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
if (!class_exists('ExtensionTaxjarIntegration')) {
    include('core/taxjar_integration.php');
}

$controllers = array(
    'storefront' => array(),
    'admin'      => array(
        'responses/extension/taxjar_integration',
        'pages/catalog/taxjar_integration',
        'pages/taxjar_integration/nexus_address',
        'pages/taxjar_integration/sync_order',
        'pages/total/taxjar_integration_total',
        'pages/sale/taxjar_customer_data',
    ),
);

$models = array(
    'storefront' => array(
        'extension/taxjar_discount',
        'extension/taxjar_integration',
        'total/taxjar_integration_total',
        'extension/nexus_address'
    ),
    'admin'      => array(
        'extension/taxjar_discount',
        'extension/taxjar_integration',
        'extension/nexus_address'),
);

$templates = array(
    'storefront' => array('pages/account/tax_exempt_edit.tpl'),
    'admin'      => array(
        'pages/extension/taxjar_integration_settings.tpl',
        'pages/taxjar_integration/tabs.tpl',
        'pages/taxjar_integration/taxjar_integration_form.tpl',
        'pages/taxjar_integration/fallback_rates.tpl',
        'pages/taxjar_integration/nexus_address.tpl',
        'pages/taxjar_integration/sync_order.tpl',
        'responses/extension/nexus_address.tpl',
        'pages/sale/taxjar_customer_form.tpl',
    ),
);

$languages = array(
    'storefront' => array('english/taxjar_integration/taxjar_integration'),
    'admin'      => array('english/taxjar_integration/taxjar_integration'),
);

