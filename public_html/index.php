<?php
/*
 * ------------------------------------------------------------------------------
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 * ------------------------------------------------------------------------------
 */

if (!function_exists('simplexml_load_file')) {
    exit("simpleXML functions are not available. Please contact your system administrator or host service provider.");
}

// Load Configuration
// Real path (operating system web root) to the directory where abantecart is installed
$root_path = dirname(__FILE__);

// Windows IIS Compatibility  
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('IS_WINDOWS', true);
    $root_path = str_replace('\\', '/', $root_path);
} else {
    define('IS_WINDOWS', false);
}

define('DIR_ROOT', $root_path);
const DIR_CORE = DIR_ROOT . '/core/';

if(is_file(DIR_ROOT.'/system/config.php')) {
    require_once(DIR_ROOT.'/system/config.php');
}
// New Installation
if (!defined('DB_DATABASE')) {
    if(is_dir(DIR_ROOT.'/install')) {
        header('Location: install/index.php');
    }
    exit('Fatal Error: configuration not found!');
}

// Load all initial set up
require_once(DIR_ROOT.'/core/init.php');
/** @var Registry $registry */
/** @var Aconfig $config */

ADebug::checkpoint('init end');

if (!defined('IS_ADMIN') || !IS_ADMIN) { // storefront load

    // Relative paths and directories
    define('RDIR_TEMPLATE', 'storefront/view/'.$config->get('config_storefront_template').'/');

    // Customer
    $registry->set('customer', new ACustomer($registry));

    // Tax
    $registry->set('tax', new ATax($registry));

    // Weight
    $registry->set('weight', new AWeight($registry));

    // Length
    $registry->set('length', new ALength($registry));

    // Cart
    $registry->set('cart', new ACart($registry));

} else {
    // Admin template load
    // Relative paths and directories
    define('RDIR_TEMPLATE', 'admin/view/default/');

    // User
    $registry->set('user', new AUser($registry));
}// end admin load

// Currency
$registry->set('currency', new ACurrency($registry));

//Route to request process
$router = new ARouter($registry);
$registry->set('router', $router);
$router->processRoute(ROUTE);

// Output
$registry->get('response')->output();

if (IS_ADMIN === true && $registry->get('config')->get('config_maintenance') && $registry->get('user')->isLogged()) {
    $user_id = $registry->get('user')->getId();
    startStorefrontSession(
        $user_id,
        ['merchant_username' => $registry->get('user')->getUserName()],
    );
}

//Show cache stats if debugging
if ($registry->get('config')->get('config_debug')) {
    ADebug::variable('Cache statistics: ', $registry->get('cache')->stats()."\n");
}

ADebug::checkpoint('app end');

//display debug info
if ($router->getRequestType() == 'page') {
    ADebug::display();
}
// add ability to call hook at the end
/** @var AHook $hook */
$hook->hk_IndexEnd();
