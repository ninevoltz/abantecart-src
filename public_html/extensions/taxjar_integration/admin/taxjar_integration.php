<?php
/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

require 'vendor/autoload.php';

class ExtensionTaxjarIntegration extends Extension {

    public $errors = array();
    public $data = array();
    public $totals = array();
    public $postcode = 0;
    protected $controller;
    protected $registry;

    protected $exemptGroups = array();

    public function __construct() {
        $this->registry = Registry::getInstance();
        $this->controller = $this->baseObject;
        $this->exemptGroups = array(
            'none'  => '-----Please Select-----',
            'wholesale' => 'A. Wholesale',
            'government' => 'B. Government',
            'other' => 'C. Other',
            'non_exempt' => 'D. Non Exempt'
        );
    }

    protected function _is_enabled() {
        return $this->registry->get('config')->get('taxjar_integration_status');
    }

    public function onControllerCommonListingGrid_InitData() {
        $that =& $this->baseObject;
        if (!$this->_is_enabled()) {
            return null;
        }
        if ($that->data['table_id'] === 'customer_grid') {
            $that->loadLanguage('taxjar_integration/taxjar_integration');
            $that->data['actions']['edit']['children']['taxjar_integration'] = array(
                'text' => $that->language->get('taxjar_integration_name'),
                'href' => $that->html->getSecureURL('sale/taxjar_customer_data', '&customer_id=%ID%'),
            );
        } elseif($that->data['table_id'] === 'product_grid') {
            $that->loadLanguage('taxjar_integration/taxjar_integration');
            $that->data['actions']['edit']['children']['taxjar_integration'] = array(
                'text' => $that->language->get('taxjar_integration_name'),
                'href' => $that->html->getSecureURL('catalog/taxjar_integration', '&product_id=%ID%'),
            );
        }
    }

    public function onControllerPagesCatalogProductTabs_InitData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that =& $this->baseObject;
        $that->loadLanguage('taxjar_integration/taxjar_integration');

        $this->data = array();
        $this->data['tabs'][] = array(
            'href'   => $that->html->getSecureURL(
                'catalog/taxjar_integration',
                '&product_id='.$that->request->get['product_id']
            ),
            'text'   => $that->language->get('taxjar_integration_name'),
            'active' => ($that->data['active'] == 'taxjar_integration'),
        );

        $view = new AView(Registry::getInstance(), 0);
        $view->batchAssign($this->data);
        $that->view->addHookVar('extension_tabs', $view->fetch('pages/taxjar_integration/tabs.tpl'));
    }

    public function onControllerPagesSaleCustomer_InitData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that =& $this->baseObject;
        $that->loadLanguage('taxjar_integration/taxjar_integration');
        $customer_id = $that->request->get['customer_id'];
        $taxjar_tab[] = array(
            'href' => $that->html->getSecureURL('sale/taxjar_customer_data', '&customer_id='.$customer_id),
            'text' => $that->language->get('taxjar_integration_name'),
        );
        $tab_code = "";
        foreach ($taxjar_tab as $tab) {
            if ($tab['active']) {
                $classname = 'active';
            } else {
                $classname = '';
            }

            $tab_code = '<li class="'.$classname.'">';
            $tab_code .= '	<a href="'.$tab['href'].'"><strong>'.$tab['text'].'</strong></a>';
            $tab_code .= '</li>';
        }
        $that->view->addHookVar('extension_tabs', $tab_code);
    }

    public function onControllerPagesSaleCustomerTransaction_InitData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that =& $this->baseObject;
        $that->loadLanguage('taxjar_integration/taxjar_integration');
        $customer_id = $that->request->get['customer_id'];
        $taxjar_tab[] = array(
            'href' => $that->html->getSecureURL('sale/taxjar_customer_data', '&customer_id='.$customer_id),
            'text' => $that->language->get('taxjar_integration_name'),
        );

        $tab_code = "";
        foreach ($taxjar_tab as $tab) {
            if ($tab['active']) {
                $classname = 'active';
            } else {
                $classname = '';
            }

            $tab_code = '<li class="'.$classname.'">';
            $tab_code .= '<a href="'.$tab['href'].'"><strong>'.$tab['text'].'</strong></a>';
            $tab_code .= '</li>';
        }
        $that->view->addHookVar('extension_tabs', $tab_code);
    }

    public function onControllerPagesSaleCustomerTabs_InitData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = &$this->baseObject;
        $that->loadLanguage('taxjar_integration/taxjar_integration');

        $this->data = array();
        $this->data['tabs'][] = array(
            'href'   => $that->html->getSecureURL(
                'catalog/taxjar_integration',
                '&product_id='.$that->request->get['product_id']),
            'text'   => $that->language->get('taxjar_integration_name'),
            'active' => ($that->data['active'] == 'taxjar_integration'),
        );

        $view = new AView(Registry::getInstance(), 0);
        $view->batchAssign($this->data);
        $that->view->addHookVar('extension_tabs', $view->fetch('pages/taxjar_integration/tabs.tpl'));
    }

    //public function onControllerPagesSaleOrder_UpdateData() {
    //    if (!$this->_is_enabled()) {
    //        return null;
    //    }
    //    $that = $this->baseObject;
    //    $order_id = $that->request->get['order_id'];
    //    $that->load->model('sale/order');
    //    $order = $that->model_sale_order->getOrder($order_id);
    //    if ($this->baseObject_method === 'details') {
    //        if ($order['order_status_id'] === $that->config->get('taxjar_integration_status_success_settled')
    //            || $order['order_status_id'] === $that->config->get('taxjar_integration_status_delete_settled')
    //            || $order['order_status_id'] === $that->config->get('taxjar_integration_status_refund_settled')) {
    //            $that->view->addHookVar('order_details',
    //                '<div class="alert alert-danger" role="alert">'
    //                .'This order is already calculated and committed to TaxJar. Edits to this order will not be reflected on TaxJar transactions!'
    //                .'</div>'
    //            );
    //        }
    //    }
    //}

    public function onControllerPagesSaleOrder_InitData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = $this->baseObject;
        $that->load->model('extension/taxjar_discount');
        $that->load->model('sale/order');
        if ($this->baseObject_method==='recalc') {
            $order_id = $that->request->get['order_id'];
            $skip_recalc = array();
            $new_totals = array();
            //$log_msg = '';

            if (!$that->user->canModify('sale/order')) {
                $that->session->data['error'] = $that->language->get('error_permission');
                return 0;
            } else {
                if (!has_value($order_id)) {
                    $that->session->data['error'] = "Missing required details";
                    return 0;
                }
            }

            //do we have to skip recalc for some totals?
            if ($that->request->get['skip_recalc']) {
                $enc = new AEncryption($that->config->get('encryption_key'));
                $skip_recalc = unserialize($enc->decrypt($that->request->get['skip_recalc']));
            }
            //do we have total values passed?
            if ($that->request->post['totals']) {
                $new_totals = $that->request->post['totals'];
            }

            //do we need to add new total record?
            /**
             * @var $adm_order_mdl ModelSaleOrder
             */
            $adm_order_mdl = $that->load->model('sale/order');
            if ($that->request->post['key'] && $order_id) {
                $new_total = $that->request->post;
                $order_total_id = $adm_order_mdl->addOrderTotal($order_id, $new_total);
                $skip_recalc[] = $order_total_id;
            }

            if ($that->config->get('advanced_reports_status')==='1') {
                include_once DIR_EXT . 'advanced_reports' . DIR_EXT_CORE . 'lib/order_manager.php';
                $order = new AExtOrderManager($order_id);
            } elseif ($that->config->get('ups_integration_status')==='1') {
                include_once DIR_EXT . 'ups_integration' . DIR_EXT_CORE . 'lib/order_manager.php';
                $order = new AUPSOrderManager($order_id);
            } elseif ($that->config->get('ups_integration_plus_status')==='1') {
                include_once DIR_EXT . 'ups_integration_plus' . DIR_EXT_CORE . 'lib/order_manager.php';
                $order = new AUPSPlusOrderManager($order_id);
            } elseif ($that->config->get('usps_integration_status')==='1') {
                include_once DIR_EXT . 'usps_integration' . DIR_EXT_CORE . 'lib/order_manager.php';
                $order = new AUSPSOrderManager($order_id);
            } elseif ($that->config->get('fedex_integration_status')==='1') {
                include_once DIR_EXT . 'fedex_integration' . DIR_EXT_CORE . 'lib/order_manager.php';
                $order = new AFedexOrderManager($order_id);
            } elseif ($that->config->get('canpost_integration_status')==='1') {
                include_once DIR_EXT . 'canpost_integration' . DIR_EXT_CORE . 'lib/order_manager.php';
                $order = new ACanpostOrderManager($order_id);
            } elseif ($that->config->get('purolator_integration_status')==='1') {
                include_once DIR_EXT . 'purolator_integration' . DIR_EXT_CORE . 'lib/order_manager.php';
                $order = new APurolatorOrderManager($order_id);
            } else {
                $order = new AOrderManager($order_id);
            }

            //Recalc. If total has changed from original, update and create a log to order history
            $t_ret = $order->recalcTotals($skip_recalc, $new_totals);
            if (!$t_ret || $t_ret['error']) {
                $that->session->data['error'] = "Error recalculating totals! ".$t_ret['error'];
            } else {
                $that->session->data['success'] = $that->language->get('text_success');
            }

            $that->session->data['is_admin']='1';

            $that->load->model('extension/taxjar_discount');
            $that->load->model('extension/taxjar_integration');
            $that->load->model('sale/order');
            $order = $that->model_sale_order->getOrder($order_id);
            $coupon_id = $order['coupon_id'];
            $order_totals = $that->model_sale_order->getOrderTotals($order_id);
            $order_products = $that->model_sale_order->getOrderProducts($order_id);
            $final_discount = $subtotal = 0.00;
            $customer_id = $order['customer_id'];
            foreach ($order_totals as $total) {
                if ($total['type'] === 'subtotal') {
                    $subtotal = round($total['value'], 2);
                } elseif ($total['type'] === 'discount') {
                    $final_discount = round($total['value'], 2);
                }
            }
            foreach ($order_products as $product) {
                $discount = $that->model_extension_taxjar_discount->getDiscount($product['product_id'], $product['total'], $customer_id, $order_products, $subtotal, $final_discount, $coupon_id);
                $that->model_extension_taxjar_integration->addProductDiscount($order_id, $product['product_id'], $this->rounding($discount));
            }

            redirect($that->html->getSecureURL('sale/order/details', '&order_id='.$order_id));

        } elseif ($this->baseObject_method === 'history') {
	        if ( $that->request->is_POST() ) {
		            $order_id  = $that->request->get['order_id'];
			        $status_id = $that->request->post['order_status_id'];

		        $enable_calculation = $that->config->get( 'taxjar_integration_sales_tax_calculation' );
		        $enable_export      = $that->config->get( 'taxjar_integration_sales_tax_report' );
		        $order              = $that->model_sale_order->getOrder( $order_id );
		        $products           = $that->model_sale_order->getOrderProducts( $order_id );
		        $order_totals       = $that->model_sale_order->getOrderTotals( $order_id );
		        if ( isset( $order_id )
		             && isset( $status_id )
		             && $status_id == $that->config->get( 'taxjar_integration_status_success_settled' ) ) {

			        $cust_data                = array();
			        $cust_data['customer_id'] = $order['customer_id'];
			        $cust_data['order_id']    = $order_id;
			        $cust_data['email']       = $order['email'];
			        if ($enable_calculation ) {
				        $this->calculateTax($this->baseObject, $cust_data, true, $order_totals );
			        }
			        if ( $enable_export ) {
				        $this->createOrder($this->baseObject,$order_id, $products, $order_totals );
			        }
		        }

		        if ( isset( $order_id )
		             && isset( $status_id )
		             && $status_id == $that->config->get( 'taxjar_integration_status_delete_settled' ) ) {
			        $that->load->model( 'sale/order' );
			        $customer_id              = $order['customer_id'];
			        $cust_data                = array();
			        $cust_data['customer_id'] = $customer_id;
			        $cust_data['order_id']    = $order_id;
			        //Cancel Tax
			        if ( $enable_export ) {
				        $this->cancelOrder($that,$cust_data );
			        }
		        }

		        if (isset( $order_id )
		             && isset( $status_id )
		             && $status_id === $that->config->get( 'taxjar_integration_status_refund_settled' ) ) {
			        $that->load->model( 'sale/order' );
			        $customer_id              = $order['customer_id'];
			        $cust_data                = array();
			        $cust_data['customer_id'] = $customer_id;
			        $cust_data['order_id']    = $order_id;
			        //Cancel Tax
			        if ( $enable_export ) {
				        $this->refundOrder($that,$order, $order_totals );
			        }
		        }
	        }
        }
    }

    public function onControllerResponsesListingGridOrder_UpdateData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = $this->baseObject;
        if ($this->baseObject_method === 'update_field') {
            $enable_export = $that->config->get('taxjar_integration_sales_tax_report');
            $that->load->model('sale/order');

            if ($enable_export==='1') {
                if (isset($that->request->post['order_status_id'])) {
                    foreach ($that->request->post['order_status_id'] as $key => $value) {
                        $order_id = $key;
                        $status_id = $value;
                    }
                }
	            $order = $that->model_sale_order->getOrder($order_id);
	            $products = $that->model_sale_order->getOrderProducts($order_id);
	            $order_totals = $that->model_sale_order->getOrderTotals($order_id);
                if (isset($order_id)
                    && isset($status_id)
                    && $status_id == $that->config->get('taxjar_integration_status_success_settled')) {
                    $customer_id = $order['customer_id'];
                    $cust_data = array();
                    $cust_data['customer_id'] = $customer_id;
                    $cust_data['order_id'] = $order_id;
                    $this->createOrder($that,$order_id, $products, $order_totals);
                }
                if (isset($order_id)
                    && isset($status_id)
                    && $status_id == $that->config->get('taxjar_integration_status_refund_settled')) {
                    $customer_id = $order['customer_id'];
                    $cust_data = array();
                    $cust_data['customer_id'] = $customer_id;
                    $cust_data['order_id'] = $order_id;
                    $this->refundOrder($that,$order, $order_totals);
                }
                if (isset($order_id)
                    && isset($status_id)
                    && $status_id == $that->config->get('taxjar_integration_status_delete_settled')) {
                    $customer_id = $order['customer_id'];
                    $cust_data = array();
                    $cust_data['customer_id'] = $customer_id;
                    $cust_data['order_id'] = $order_id;
                    //Cancel Tax
                    $this->cancelOrder($that,$cust_data);
                }
            }
        } elseif ($this->baseObject_method==='update') {
            $enable_export = $that->config->get('taxjar_integration_sales_tax_report');
            if ($enable_export==='1') {
                $that->load->model('sale/order');
                switch ($that->request->post['oper']) {
                    case 'save' :
                        $ids = explode(',', $that->request->post['id']);
                        $ids = array_unique($ids);
                        if (!empty($ids)) {
                            foreach ($ids as $id) {
                                $status_id=$that->request->post['order_status_id'][$id];
	                            $order = $that->model_sale_order->getOrder($id);
	                            $products = $that->model_sale_order->getOrderProducts($id);
	                            $order_totals = $that->model_sale_order->getOrderTotals($id);
                                if ($status_id === $that->config->get('taxjar_integration_status_success_settled')) {
                                    $customer_id = $order['customer_id'];
                                    $cust_data = array();
                                    $cust_data['customer_id'] = $customer_id;
                                    $cust_data['order_id'] = $id;
                                    $this->createOrder($that,$id, $products, $order_totals);
                                }
                                if ($status_id === $that->config->get('taxjar_integration_status_refund_settled')) {
                                    $customer_id = $order['customer_id'];
                                    $cust_data = array();
                                    $cust_data['customer_id'] = $customer_id;
                                    $cust_data['order_id'] = $id;
                                    $this->refundOrder($that,$order, $order_totals);
                                }
                                if ($status_id == $that->config->get('taxjar_integration_status_delete_settled')) {
                                    $customer_id = $order['customer_id'];
                                    $cust_data = array();
                                    $cust_data['customer_id'] = $customer_id;
                                    $cust_data['order_id'] = $id;
                                    //Cancel Tax
                                    $this->cancelOrder($that,$cust_data);
                                }
                            }
                        }
                        break;
                }
            }
        }
    }

    public function onControllerPagesCheckoutGuestStep3_UpdateData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = $this->baseObject;
        $display_totals = $that->cart->buildTotalDisplay();
	    $order = new AOrder($this->registry);
	    $order->buildOrderData($that->session->data);
	    $order->saveOrder();
		$that->view->assign('totals',$display_totals['total_data']);
	    //$that->data['totals'] = $display_totals['total_data'];
        if (isset($that->session->data['order_id'])) {
            $this->setOrderProductTaxCodes($that->session->data['order_id']);
        }
    }

	public function onControllerPagesCheckoutConfirm_UpdateData() {
		if (!$this->_is_enabled()) {
			return null;
		}
		$that = $this->baseObject;
		$display_totals = $that->cart->buildTotalDisplay();
		$order = new AOrder($this->registry);
		$order->buildOrderData($that->session->data);
		$order->saveOrder();
		//$that->data['totals'] = $display_totals['total_data'];
		$that->view->assign('totals',$display_totals['total_data']);
		if (isset($that->session->data['order_id'])) {
			$this->setOrderProductTaxCodes($that->session->data['order_id']);
		}
	}

    /**
     * @param int $order_id
     *
     * @throws AException
     */
    public function setOrderProductTaxCodes($order_id) {
        $that = $this->baseObject;
        $that->load->model('account/order');
        $product_data = $that->model_account_order->getOrderProducts($order_id);
        /**
         * @var ModelExtensiontaxjarIntegration $mdl
         */
        $mdl = $that->load->model('extension/taxjar_integration', 'storefront');
        foreach ($product_data as $key => $values) {
            $taxCodeValue = $mdl->getProductTaxCode($values['product_id']);
            $mdl->setOrderProductTaxCode($values['order_product_id'], $taxCodeValue);
        }
    }

    /**
     * @param $total_data
     *
     * @return float|int
     */
    public function calcTotalDiscount($total_data) {
        $total_discount = 0;
        foreach ($total_data as $key => $value) {
            if ($value['total_type'] == 'discount' || $value['type'] == 'discount') {
                $total_discount += -1 * $value['value'];
            }
        }
        return $total_discount;
    }

    /**
     * @param      $that
     * @param      $cust_data
     * @param bool $commit
     * @param int  $total_data
     * @param bool $return
     *
     * @return int
     * @throws AException
     */
    public function calculateTax($that, $cust_data, $commit = false, $total_data = 0, $return = false) {
        $load = $this->registry->get('load');
        $config = $this->registry->get('config');
        $session = $this->registry->get('session');
        $route = '';
        if (isset($that->request->get['rt'])) {
            $route = $that->request->get['rt'];
        } elseif (isset($that->request->get['route'])) {
            $route = $that->request->get['route'];
        }
        if (strpos($route, 'r/') === 0) {
            $route = substr($route, 2);
        }
        $api_routes = array(
            'checkout/confirm',
            'checkout/guest_step_3',
            'checkout/fast_checkout',
            'checkout/cart',
            'checkout/cart/recalc_totals'
        );
        $order_routes = array('checkout/confirm', 'checkout/guest_step_3', 'checkout/fast_checkout');
        if (IS_ADMIN === true) {
            $order_id = $cust_data['order_id'];
        } elseif (isset($session->data['order_id'])) {
            $order_id = $session->data['order_id'];
            $session->data['taxjar_order_id']=$order_id;
        }

        if (IS_ADMIN === true) {
            $load->model('sale/customer');
            $customer = $that->model_sale_customer->getCustomer($cust_data['customer_id']);
        } else {
            $customer = new ACustomer($this->registry);
        }
        $load->model('extension/taxjar_integration');
        if (IS_ADMIN !== true) {
            $load->model('account/address');

            $customerAddress = $that->model_account_address->getAddress($session->data['shipping_address_id']);

	        if(!$this->registry->get('customer')->isLogged() && isset($cust_data['guest']['shipping']) && isset($cust_data['guest']) && !$customerAddress) {
		        $customerAddress = $cust_data['guest']['shipping'];
	        } elseif (!$this->registry->get('customer')->isLogged() && isset($cust_data['guest']) && !$customerAddress) {
                $customerAddress = $cust_data['guest'];
            }

            if ($customer && !$customerAddress) {
                $customerAddress = $that->model_account_address->getAddress($customer->getAddressId());
            }
        }
        
        //Order data
        $order = new AOrder($this->registry);

        if (IS_ADMIN) {
            $load->model('sale/order');
            $order_data = $that->model_sale_order->getOrder($order_id);
        } else {
            $order_data = $order->loadOrderData($order_id, 'any');
        }

        $api_key=$config->get('taxjar_integration_api_key');
        $sandbox_mode = $that->config->get('taxjar_integration_sandbox_status');
        $sandbox_api_key = $that->config->get('taxjar_integration_sandbox_api_key');

        $from_country=$config->get('taxjar_integration_country');
        $from_zipcode= $config->get('taxjar_integration_postal_code');
        $from_state = $config->get('taxjar_integration_state');
        $from_city = $config->get('taxjar_integration_city');
        $from_street = $config->get('taxjar_integration_street');
	    $debug = $config->get('taxjar_integration_debug');
        if ($api_key || $sandbox_api_key) {

            if ($sandbox_mode==='1' && $sandbox_api_key) {
                $client = TaxJar\Client::withApiKey($sandbox_api_key);
                $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
            } else {
                $client = TaxJar\Client::withApiKey($api_key);
            }

            if (isset($order_data['shipping_postcode']) && !empty($order_data['shipping_postcode'])) {
                $to_street = $order_data['shipping_address_1'];
                $to_city = $order_data['shipping_city'];
                $to_state = $order_data['shipping_zone_code'];
                $to_country = $order_data['shipping_iso_code_2'];
                $to_zipcode = $order_data['shipping_postcode'];
            } else {
                $to_street = $customerAddress['address_1'];
                $to_city = $customerAddress['city'];
                $to_state = $customerAddress['zone_code'];
                $to_country = $customerAddress['iso_code_2'];
                $to_zipcode = $customerAddress['postcode'];
            }

            // Required Request Parameters
            if (!$cust_data['customer_id']) {
                $cust_data['customer_id'] = 'guest';
            }

            // Line Data
            // Required Parameters
            $lines = array();
            //Product model
            $total=$final_discount = 0.00;
            if ($order_id) {
                if (IS_ADMIN === false) {
                    if (in_array($route, $order_routes)) {
                        $load->model('account/order');
                        $product_data = $that->model_account_order->getOrderProducts($order_id);
                        $totals = $that->model_account_order->getOrderTotals($order_id);
                        foreach ($totals as $total) {
                            if ($total['key'] === 'coupon') {
                                $final_discount = round($total['value'], 2);
                            } elseif ($total['key'] === 'total') {
                                $total =  round($total['value'], 2);
                            }
                        }
                        $promotion = new APromotion($this->registry);
                        if (isset($session->data['coupon'])) {
                            $coupon = $promotion->getCouponData($session->data['coupon']);
                            $coupon_id = $coupon['coupon_id'];
                            $that->session->data['shipping_discount'] = $coupon['shipping'];
                            if ($coupon_id !== 0 || !empty($coupon_id)) {
                                if ($coupon['shipping'] !=='1') {
                                    $that->load->model('extension/taxjar_discount');
                                    $that->load->model('extension/taxjar_integration');
                                    $that->load->model('catalog/product');
                                    foreach ($product_data as $key => $product) {
                                        $discount = $that->model_extension_taxjar_discount->getDiscount($product['product_id'], $product['total'], $final_discount, $coupon_id);
                                        $that->model_extension_taxjar_integration->addProductDiscount($order_id, $product['product_id'], $this->rounding($discount));
                                    }
                                } else {
                                    $exist = $that->model_extension_taxjar_integration->getProductDiscountByOrderId($order_id);
                                    if ($exist > 0) {
                                        $that->model_extension_taxjar_integration->deleteProductDiscountByOrderId($order_id);
                                    }
                                }
                            }
                        } else {
                            $exist = $that->model_extension_taxjar_integration->getProductDiscountByOrderId($order_id);
                            if ($exist > 0) {
                                $that->model_extension_taxjar_integration->deleteProductDiscountByOrderId($order_id);
                            }
                        }
                    } else {
                        $product_data=$that->cart->getProducts();
	                    $total=$that->cart->getTotal();
                    }
                } else {
                    $load->model('sale/order');
                    $product_data = $that->model_sale_order->getOrderProducts($order_id);
                }
                $line = array();
                $load->model('extension/taxjar_integration');
                $load->model('catalog/product');
                $quantity = $exempt_qty =0;
                foreach ($product_data as $key => $values) {
                    //getting sku
                    $tmp = $that->model_catalog_product->getProduct($values['product_id']);
                    $tax_code = $that->model_extension_taxjar_integration->getProductTaxCode($values['product_id']);
                    $discount = $that->model_extension_taxjar_integration->getProductDiscount($order_id,$values['product_id']);
                    if ($tmp['sku']) {
                        $line['id'] = $tmp['sku'];
                    } else {
                        $line['id'] = $values['product_id'];
                    }

                    $quantity += $values['quantity'];
                    $line['quantity'] = (int)$values['quantity'];
	                $line['product_tax_code'] = isset($tax_code) ? $tax_code : '11111';
                    $line['unit_price'] = round($values['price'],2);
                    if (!empty($discount) && (float)$discount!==0.00) {
                        $line['discount'] = -$discount;
                    }
                    $lines[] = $line;
                }
            }

            //get shipping cost
            if (IS_ADMIN === true) {
                $all_totals = $that->model_sale_order->getOrderTotals($order_id);
                $shp_cost = 0.00;
                foreach ($all_totals as $t) {
                    if ($t['key'] == 'shipping') {
                        $shp_cost = round($t['value'],2);
                        break;
                    }
                }
            } else {
                $shipping_discount = 0.00;
                foreach ($total_data as $data) {
                    if ($data['id']==='coupon') {
                        $shipping_discount = round(-$data['value'],2);
                    }
                }
                $shp_cost = $that->session->data['shipping_method']['cost'];

                if ($that->session->data['shipping_discount']==='1') {
                    $shp_cost = $shp_cost-$shipping_discount;
                }
            }

            $sales_tax=$subtotal=0.00;

            $that->load->model('extension/taxjar_integration');
            $customer_settings=$that->model_extension_taxjar_integration->getCustomerSettings($order_data['customer_id']);
            $info = $that->model_extension_taxjar_integration->getTaxJarCustomer($order_data['email']);
            if ($info['synced']==='1' && $customer_settings['status']==='1') {
                $id=$info['taxjar_customer_id'];
            } else {
                $id='';
            }
			$tax_rate_cache='TaxJar.order_id.'.$order_id.'.tax_rate';
	        $taxjar_tax_rate_cache='TaxJar.order_id.'.$order_id.'.taxjar_tax_rate';
            $cache_zipcode='TaxJar.order_id.'.$order_id.'.zipcode';
            $cache_total='TaxJar.order_id.'.$order_id.'.total';
            $that->load->model('extension/nexus_address');
            $is_nexus = $that->model_extension_nexus_address->getNexusByState($to_state);
	        if (($to_country==='US' && $is_nexus > 0) || $to_country!=='US') {
                if (in_array($route, $api_routes) || $that->session->data['is_admin'] === '1') {
                    unset($that->session->data['is_admin']);
                    $zipcode_exist = $that->cache->get($cache_zipcode);
                    $total_exist = $that->cache->get($cache_total);
                    if (!$zipcode_exist || $zipcode_exist !== $to_zipcode) {
                        $that->cache->set($cache_zipcode, $to_zipcode);
                        $new_zipcode = true;
                    } else {
                        $new_zipcode = false;
                    }

                    if (!$total_exist || $total_exist !== $total) {
                        $that->cache->set($cache_total, $total);
                        $new_total = true;
                    } else {
                        $new_total = false;
                    }
	                if (($new_zipcode === true && $new_total === true) || $new_zipcode === true || $new_total === true) {
                        if ($lines) {
                            try {
                                if ($id!=='') {
                                	$getTaxRequest = $client->taxForOrder([
		                                'from_country' => $from_country,
		                                'from_zip' => $from_zipcode,
		                                'from_state' => $from_state,
		                                'from_city' => $from_city,
		                                'from_street' => $from_street,
		                                'to_country' => $to_country,
		                                'to_zip' => $to_zipcode,
		                                'to_state' => $to_state,
		                                'to_city' => $to_city,
		                                'to_street' => $to_street,
		                                'shipping' => (float)$shp_cost,
		                                'customer_id' => 'customer_' . $id,
		                                'line_items' => $lines,
		                                'plugin' => 'abantecart'
	                                ]);
                                } else {
                                    $getTaxRequest = $client->taxForOrder([
                                        'from_country' => $from_country,
                                        'from_zip' => $from_zipcode,
                                        'from_state' => $from_state,
                                        'from_city' => $from_city,
                                        'from_street' => $from_street,
                                        'to_country' => $to_country,
                                        'to_zip' => $to_zipcode,
                                        'to_state' => $to_state,
                                        'to_city' => $to_city,
                                        'to_street' => $to_street,
                                        'shipping' => (float)$shp_cost,
                                        'line_items' => $lines,
                                        'plugin' => 'abantecart'
                                    ]);
                                }
                                $that->cache->set($taxjar_tax_rate_cache, $getTaxRequest->amount_to_collect);
                                $tax_rate = $getTaxRequest->amount_to_collect;
                                if ($debug==='1') {
                                	$that->log->write('Tax rate calculation API response: '.var_export($getTaxRequest,true));
                                }
                            } catch (TaxJar\Exception $e) {
	                            $discounts = 0.00;
	                            foreach ( $total_data as $data ) {
		                            if ( $data['id'] === 'coupon' ) {
			                            $discounts = $data['value'];
		                            } elseif ( $data['id'] === 'subtotal' ) {
			                            $subtotal = $data['value'];
		                            }
	                            }
	                            $rate = $that->config->get( 'taxjar_integration_fallback_' . $to_state );
	                            if (($customer_settings['status']==='1' && $customer_settings['exempt_group']==='non_exempt')
	                                || $customer_settings['status']==='0' || empty($customer_settings)) {
		                            $tax_rate = ($subtotal + $discounts) * ($rate / 100);
		                        }
	                            if ( $debug === '1' ) {
		                            $that->log->write( 'Unable to calculate tax, the error message is: ' . $e->getMessage() );
	                            }
                            }
                        }
                    } else {
                        if ($to_state) {
                            $discounts=0.00;
                            foreach ($total_data as $data) {
                                if ($data['id']==='coupon') {
                                    $discounts=$data['value'];
                                } elseif($data['id']==='subtotal') {
                                    $subtotal=$data['value'];
                                }
                            }
                            $rate= $that->config->get('taxjar_integration_fallback_'.$to_state) ? $that->config->get('taxjar_integration_fallback_'.$to_state) : 0;
                            if (($customer_settings['status']==='1' && $customer_settings['exempt_group']==='non_exempt')
                                || $customer_settings['status']==='0' || empty($customer_settings)) {
	                            $tax_rate = ($subtotal + $discounts) * ($rate / 100);
	                        }
                           }
                        }
                } else {
                    if ($to_state) {
                        $discounts=0.00;
                        foreach ($total_data as $data) {
                            if ($data['id']==='coupon') {
                                $discounts=$data['value'];
                            } elseif($data['id']==='subtotal') {
                                $subtotal=$data['value'];
                            }
                        }

                        $rate=$that->config->get('taxjar_integration_fallback_'.$to_state) ? $that->config->get('taxjar_integration_fallback_'.$to_state) : 0;
                        if (($customer_settings['status']==='1' && $customer_settings['exempt_group']==='non_exempt')
                            || $customer_settings['status']==='0' || empty($customer_settings)) {
                            $tax_rate = ($subtotal + $discounts) * ($rate / 100);
	                    }
                       }
                    }
            }
            if (in_array($route, $order_routes)) {
                return $that->cache->get($taxjar_tax_rate_cache);
            } else {
                return $tax_rate;
            }
        }
    }

	/**
	 * @param $that
	 * @param $order_id
	 * @param $products
	 * @param $order_totals
	 *
	 * @throws AException
	 */
    public function createOrder($that,$order_id,$products,$order_totals) {
        $that =& $this->baseObject;
        if (IS_ADMIN===true) {
            $that->load->model('sale/order');
            $order_info = $that->model_sale_order->getOrder($order_id);
        } else {
            $that->load->model('checkout/order');
            $order_info = $that->model_checkout_order->getOrder($order_id);
        }
        $that->load->model('extension/taxjar_discount');
        $promotion = new APromotion($this->registry);
	    $debug = $that->config->get('taxjar_integration_debug');
        $that->load->model('extension/taxjar_discount');
        $that->load->model('extension/taxjar_integration');
        $that->load->model('catalog/product');
        $enable_export = $that->config->get('taxjar_integration_save_transaction');
        $id=0;
        if ($enable_export==='1') {
            $subtotal = $sales_tax = $shp_cost = $coupon = 0.00;
            foreach ($order_totals as $total) {
                if ($total['key'] === 'subtotal') {
                    $subtotal = round($total['value'], 2);
                } elseif ($total['key'] === 'taxjar_integration_total') {
                    $sales_tax = round($total['value'], 2);
                } elseif ($total['key'] === 'shipping') {
                    $shp_cost = round($total['value'], 2);
                } elseif ($total['key'] === 'coupon') {
                    $coupon = round($total['value'], 2);
                }
            }

            $to_street = $order_info['shipping_address_1'];
            $to_city = $order_info['shipping_city'];
            $to_state = $order_info['shipping_zone_code'];
            $to_country = $order_info['shipping_iso_code_2'];
            $to_zipcode = $order_info['shipping_postcode'];
            $date = new DateTime($order_info['date_added']);
            $date = $date->format('Y-m-d');

            $api_key = $that->config->get('taxjar_integration_api_key');
            $sandbox_mode = $that->config->get('taxjar_integration_sandbox_status');
            $sandbox_api_key = $that->config->get('taxjar_integration_sandbox_api_key');

            if (($api_key || $sandbox_api_key) && $enable_export === '1') {
                if ($sandbox_mode === '1' && $sandbox_api_key) {
                    $client = TaxJar\Client::withApiKey($sandbox_api_key);
                    $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
                } else {
                    $client = TaxJar\Client::withApiKey($api_key);
                }
            }

            $customer_id = $order_info['customer_id'];
            $customer_settings = $that->model_extension_taxjar_integration->getCustomerSettings($customer_id);
            $email = $order_info['email'];
            $info = $that->model_extension_taxjar_integration->getTaxJarCustomer($email);
            if ($info && $info['synced']==='1' && $customer_settings['status']==='1') {
                $id=$info['taxjar_customer_id'];
            }
            $taxjar_success_id = $that->config->get('taxjar_integration_status_success_settled');
            $order_status_id = $order_info['order_status_id'];

            $lines = array();
            foreach ($products as $key => $product) {
                $tmp = $that->model_catalog_product->getProduct($product['product_id']);
                $tax_code = $that->model_extension_taxjar_integration->getProductTaxCode($product['product_id']);
                $discount = $that->model_extension_taxjar_integration->getProductDiscount($order_id, $product['product_id']);
                if ($tmp['sku']) {
                    $line['id'] = $tmp['sku'];
                } else {
                    $line['id'] = $product['product_id'];
                }
                $line['description'] = $tmp['name'];
                $line['quantity'] = (int)$product['quantity'];
	            $line['product_tax_code'] = isset($tax_code) ? $tax_code : '11111';
                $line['unit_price'] = round($product['price'], 2);
                if (!empty($discount) && (float)$discount !== 0.00) {
                    $line['discount'] = -$discount;
                }
                $lines[] = $line;
            }
            $from_country = $that->config->get('taxjar_integration_country');
            $from_zipcode = $that->config->get('taxjar_integration_postal_code');
            $from_state = $that->config->get('taxjar_integration_state');
            $from_city = $that->config->get('taxjar_integration_city');
            $from_street = $that->config->get('taxjar_integration_street');

            try {
                $result = $client->showOrder($order_id);
                $exist = 1;
                if ($debug==='1') {
                	$that->log->write('TaxJar Order information API response: '.var_export($result,true));
                }
            } catch (TaxJar\Exception $e) {
                $exist = 0;
	            if ($debug==='1') {
					$that->log->write('Unable to get TaxJar order information, the error message is: '.$e->getMessage());
	            }
            }

            $amount = (float)$subtotal + (float)$shp_cost;
            if (isset($order_info['coupon_id'])) {
                $coupon_id = $order_info['coupon_id'];
                $coupon_code = $that->model_extension_taxjar_discount->getCouponData($coupon_id,$order_info['customer_id'],$products,$subtotal);
                if ($coupon_id !== 0 || !empty($coupon_id)) {
                    if ($coupon_code['shipping'] === '1') {
                        $shp_cost = $shp_cost + $coupon;
                    }
                }
                $amount = (float)$subtotal + (float)$shp_cost + $coupon;
            }

            if ($customer_id === 0 || $id===0) {
                if ($exist === 0 && isset($order_id) && $order_status_id === $taxjar_success_id) {
                    try {
                        $create = $client->createOrder([
                            'transaction_id' => $order_id,
                            'transaction_date' => $date,
                            'from_country' => $from_country,
                            'from_zip' => $from_zipcode,
                            'from_state' => $from_state,
                            'from_city' => $from_city,
                            'from_street' => $from_street,
                            'to_country' => $to_country,
                            'to_zip' => $to_zipcode,
                            'to_state' => $to_state,
                            'to_city' => $to_city,
                            'to_street' => $to_street,
                            'amount' => $amount,
                            'shipping' => (float)$shp_cost,
                            'sales_tax' => (float)$sales_tax,
                            'line_items' => $lines,
	                        'plugin' => 'abantecart'
                        ]);
                        if ($debug==='1') {
                        	$that->log->write('Transmit order to TaxJar API response: '.var_export($create,true));
                        }
                    } catch (TaxJar\Exception $e) {
	                    if ( $debug === '1' ) {
		                    $that->log->write( 'Unable to transmit the order to TaxJar, the error message is: ' . $e->getMessage() );
	                    }
                    }
                } elseif ($exist === 1 && isset($order_id) && $order_status_id === $taxjar_success_id) {
                    try {
                        $update = $client->updateOrder([
                            'transaction_id' => $order_id,
                            'transaction_date' => $date,
                            'from_country' => $from_country,
                            'from_zip' => $from_zipcode,
                            'from_state' => $from_state,
                            'from_city' => $from_city,
                            'from_street' => $from_street,
                            'to_country' => $to_country,
                            'to_zip' => $to_zipcode,
                            'to_state' => $to_state,
                            'to_city' => $to_city,
                            'to_street' => $to_street,
                            'amount' => $amount,
                            'shipping' => (float)$shp_cost,
                            'sales_tax' => (float)$sales_tax,
                            'line_items' => $lines,
                            'plugin' => 'abantecart'
                        ]);
	                    if ($debug==='1') {
	                    	$that->log->write('Update TaxJar order API response: '.var_export($update,true));
	                    }
                    } catch (TaxJar\Exception $e) {
	                    if ( $debug === '1' ) {
		                    $that->log->write( 'Unable to update the TaxJar order, the error message is: ' . $e->getMessage() );
	                    }
                    }
                }
            } else {
            	if ($exist === 0 && isset($order_id) && $order_status_id === $taxjar_success_id) {
                    try {
                        $create = $client->createOrder([
                            'transaction_id' => $order_id,
                            'transaction_date' => $date,
                            'from_country' => $from_country,
                            'from_zip' => $from_zipcode,
                            'from_state' => $from_state,
                            'from_city' => $from_city,
                            'from_street' => $from_street,
                            'to_country' => $to_country,
                            'to_zip' => $to_zipcode,
                            'to_state' => $to_state,
                            'to_city' => $to_city,
                            'to_street' => $to_street,
                            'amount' => (float)$amount,
                            'shipping' => (float)$shp_cost,
                            'sales_tax' => (float)$sales_tax,
                            'customer_id' => 'customer_'.$id,
                            'line_items' => $lines,
                            'plugin' => 'abantecart'
                        ]);
	                    if ($debug==='1') {
		                    $that->log->write('Transmit order to TaxJar API response: '.var_export($create,true));
	                    }
                    } catch (TaxJar\Exception $e) {
	                    if ( $debug === '1' ) {
		                    $that->log->write( 'Unable to transmit the order to TaxJar, the error message is: ' . $e->getMessage() );
	                    }
                    }
                } elseif ($exist === 1 && isset($order_id) && $order_status_id === $taxjar_success_id) {
                    try {
                        $update = $client->updateOrder([
                            'transaction_id' => $order_id,
                            'transaction_date' => $date,
                            'from_country' => $from_country,
                            'from_zip' => $from_zipcode,
                            'from_state' => $from_state,
                            'from_city' => $from_city,
                            'from_street' => $from_street,
                            'to_country' => $to_country,
                            'to_zip' => $to_zipcode,
                            'to_state' => $to_state,
                            'to_city' => $to_city,
                            'to_street' => $to_street,
                            'amount' => $amount,
                            'shipping' => (float)$shp_cost,
                            'sales_tax' => (float)$sales_tax,
                            'customer_id' => 'customer_'.$id,
                            'line_items' => $lines,
                            'plugin' => 'abantecart'
                        ]);
	                    if ($debug==='1') {
		                    $that->log->write('Update TaxJar order API response: '.var_export($update,true));
	                    }
                    } catch (TaxJar\Exception $e) {
	                    if ($debug === '1' ) {
		                    $that->log->write( 'Unable to update TaxJar transaction, the error message is: ' . $e->getMessage() );
	                    }
                    }
                }
            }
        }
    }

	/**
	 * @param $that
	 * @param $cust_data
	 */
    public function cancelOrder($that,$cust_data) {
        //$that = $this->baseObject;
        $api_key=$that->config->get('taxjar_integration_api_key');
        $sandbox_mode = $that->config->get('taxjar_integration_sandbox_status');
        $sandbox_api_key = $that->config->get('taxjar_integration_sandbox_api_key');
        $enable_export = $that->config->get('taxjar_integration_save_transaction');
	    $debug = $that->config->get('taxjar_integration_debug');
        if ($enable_export==='1') {
            if (($api_key || $sandbox_api_key)) {
                if ($sandbox_mode === '1' && $sandbox_api_key) {
                    $client = TaxJar\Client::withApiKey($sandbox_api_key);
                    $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
                } else {
                    $client = TaxJar\Client::withApiKey($api_key);
                }

	            try {
		            $refund = $client->deleteRefund('refund_'.$cust_data['order_id']);
		            if ($debug==='1') {
			            $that->log->write('Delete TaxJar refund order API response: '.var_export($refund,true));
		            }
	            } catch (TaxJar\Exception $e) {
	            }

                try {
                    $result = $client->deleteOrder($cust_data['order_id']);
	                if ($debug==='1') {
		                $that->log->write('Delete TaxJar order API response: '.var_export($result,true));
	                }
                } catch (TaxJar\Exception $e) {
                	if ($debug==='1') {
		                $that->log->write( 'Unable to delete TaxJar order, the error message is: ' . $e->getMessage() );
	                }
                }
            }
        }
    }

	/**
	 * @param $that
	 * @param $order
	 * @param $order_totals
	 *
	 * @throws Exception
	 */
    public function refundOrder($that,$order,$order_totals) {
    	//$that = $this->baseObject;
    	$api_key=$that->config->get('taxjar_integration_api_key');
        $subtotal=$tax=$shipping=0.00;
	    $debug = $that->config->get('taxjar_integration_debug');
        foreach ($order_totals as $totals) {
            if ($totals['key']==='subtotal') {
                $subtotal=round($totals['value'],2);
            } elseif($totals['key']==='taxjar_integration_total') {
                $tax=round($totals['value'],2);
            } elseif($totals['key']==='shipping') {
                $shipping=round($totals['value'],2);
            } elseif ($totals['key'] === 'coupon') {
	            $coupon = round($totals['value'], 2);
            }
        }

        $sandbox_mode = $that->config->get('taxjar_integration_sandbox_status');
        $sandbox_api_key = $that->config->get('taxjar_integration_sandbox_api_key');
        $enable_export = $that->config->get('taxjar_integration_save_transaction');
        $from_country = $that->config->get('taxjar_integration_country');
        $from_zipcode = $that->config->get('taxjar_integration_postal_code');
        $from_state = $that->config->get('taxjar_integration_state');
        $from_city = $that->config->get('taxjar_integration_city');
        $from_street = $that->config->get('taxjar_integration_street');
        $that->load->model('catalog/product');
		$that->load->model('extension/taxjar_integration');
		$that->load->model('extension/taxjar_discount');
		$lines=array();
        $coupon=0.00;
	    if (IS_ADMIN===true) {
		    $that->load->model('sale/order');
		    $order_info = $that->model_sale_order->getOrder($order['order_id']);
	    } else {
		    $that->load->model('checkout/order');
		    $order_info = $that->model_checkout_order->getOrder($order['order_id']);
	    }
		$that->load->model('sale/order');
	    $that->load->model('extension/taxjar_integration');
	    $amount = (float)$subtotal + (float)$shipping;

	    $products = $that->model_sale_order->getOrderProducts($order['order_id']);

	    if (isset($order_info['coupon_id'])) {
		    $coupon_id = $order_info['coupon_id'];
		    $coupon_code = $that->model_extension_taxjar_discount->getCouponData($coupon_id,$order_info['customer_id'],$products,$subtotal);
		    if ($coupon_id !== 0 || !empty($coupon_id)) {
			    if ($coupon_code['shipping'] === '1') {
				    $shipping = $shipping + $coupon;
			    }
		    }
		    $amount = (float)$subtotal + (float)$shipping + $coupon;
	    }
		foreach ($products as $key => $product) {
		    $tmp = $that->model_catalog_product->getProduct($product['product_id']);
		    $tax_code = $that->model_extension_taxjar_integration->getProductTaxCode($product['product_id']);
		    $discount = $that->model_extension_taxjar_integration->getProductDiscount($order['order_id'], $product['product_id']);
		    if ($tmp['sku']) {
			    $line['id'] = $tmp['sku'];
		    } else {
			    $line['id'] = $product['product_id'];
		    }
		    $line['description'] = $tmp['name'];
		    $line['quantity'] = (int)$product['quantity'];
			$line['product_tax_code'] = isset($tax_code) ? $tax_code : '11111';
		    $line['unit_price'] = round($product['price'], 2);
		    if (!empty($discount) && (float)$discount !== 0.00) {
			    $line['discount'] = -$discount;
		    }
		    $lines[] = $line;
	    }

	    $customer_id = $order['customer_id'];
	    $email = $order['email'];

	    //get customer tax exempt status
	    $customer_settings = $that->model_extension_taxjar_integration->getCustomerSettings($customer_id);
	    //get whether customer is already synced to TaxJar or not
	    $info = $that->model_extension_taxjar_integration->getTaxJarCustomer($email);

	    $id = 0;
	    if ($info && $info['synced']==='1' && $customer_settings['status']==='1') {
		    $id=(int)$info['taxjar_customer_id'];
	    }

        if($enable_export==='1') {
            if ($api_key || $sandbox_api_key) {
                if ($sandbox_mode === '1' && $sandbox_api_key) {
                    $client = TaxJar\Client::withApiKey($sandbox_api_key);
                    $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
                } else {
                    $client = TaxJar\Client::withApiKey($api_key);
                }
                $date = new DateTime($order['date_added']);
                $date = $date->format('Y-m-d');
                try {
                    $result = $client->showRefund('refund_'.$order['order_id']);
                    $exist = 1;
	                if ($debug==='1') {
		                $that->log->write('TaxJar refunded order API response: '.var_export($result,true));
	                }
                } catch (TaxJar\Exception $e) {
                    $exist = 0;
	                if ($debug==='1') {
		                $that->log->write('Unable to get TaxJar refunded order information, the error message is: '.$e->getMessage());
	                }
                }

                if ($exist===0) {
                	if ($id!==0) {
		                try {
			                $createRefund = $client->createRefund([
				                'transaction_id' => 'refund_'.$order['order_id'],
				                'transaction_reference_id' => $order['order_id'],
				                'transaction_date' => $date,
				                'from_country' => $from_country,
				                'from_zip' => $from_zipcode,
				                'from_state' => $from_state,
				                'from_city' => $from_city,
				                'from_street' => $from_street,
				                'to_country' => $order['shipping_iso_code_2'],
				                'to_zip' => $order['shipping_postcode'],
				                'to_state' => $order['shipping_zone_code'],
				                'to_city' => $order['shipping_city'],
				                'to_street' => $order['shipping_address_1'],
				                'amount' => $amount,
				                'shipping' => $shipping,
				                'sales_tax' => $tax,
				                'customer_id' => 'customer_'.$id,
				                'line_items' => $lines,
				                'plugin' => 'abantecart'
			                ]);
			                if ($debug==='1') {
				                $that->log->write('Create TaxJar refund order API response: '.var_export($createRefund,true));
			                }
		                } catch (TaxJar\Exception $e) {
			                if ( $debug === '1' ) {
				                $that->log->write( 'Unable to create TaxJar refund transaction, the error message is: ' . $e->getMessage() );
			                }
		                }
	                } else {
		                try {
			                $createRefund = $client->createRefund([
				                'transaction_id' => 'refund_'.$order['order_id'],
				                'transaction_reference_id' => $order['order_id'],
				                'transaction_date' => $date,
				                'from_country' => $from_country,
				                'from_zip' => $from_zipcode,
				                'from_state' => $from_state,
				                'from_city' => $from_city,
				                'from_street' => $from_street,
				                'to_country' => $order['shipping_iso_code_2'],
				                'to_zip' => $order['shipping_postcode'],
				                'to_state' => $order['shipping_zone_code'],
				                'to_city' => $order['shipping_city'],
				                'to_street' => $order['shipping_address_1'],
				                'amount' => $amount,
				                'shipping' => $shipping,
				                'sales_tax' => $tax,
				                'line_items' => $lines,
				                'plugin' => 'abantecart'
			                ]);
			                if ($debug==='1') {
				                $that->log->write('Create TaxJar refund order API response: '.var_export($createRefund,true));
			                }
		                } catch (TaxJar\Exception $e) {
			                if ( $debug === '1' ) {
				                $that->log->write( 'Unable to create TaxJar refund transaction, the error message is: ' . $e->getMessage() );
			                }
		                }
	                }
                } else {
	                if ( $id !== 0 ) {
		                try {
			                $updateRefund = $client->updateRefund( [
				                'transaction_id'           => 'refund_' . $order['order_id'],
				                'transaction_reference_id' => $order['order_id'],
				                'transaction_date'         => $date,
				                'from_country'             => $from_country,
				                'from_zip'                 => $from_zipcode,
				                'from_state'               => $from_state,
				                'from_city'                => $from_city,
				                'from_street'              => $from_street,
				                'to_country'               => $order['shipping_iso_code_2'],
				                'to_zip'                   => $order['shipping_postcode'],
				                'to_state'                 => $order['shipping_zone_code'],
				                'to_city'                  => $order['shipping_city'],
				                'to_city'                  => $order['shipping_city'],
				                'to_street'                => $order['shipping_address_1'],
				                'amount'                   => $amount,
				                'shipping'                 => $shipping,
				                'sales_tax'                => $tax,
				                'customer_id'              => 'customer_'.$id,
				                'line_items'               => $lines,
				                'plugin' => 'abantecart'
			                ] );
			                if ( $debug === '1' ) {
				                $that->log->write( 'Update TaxJar refund order API response: ' . var_export( $updateRefund, true ) );
			                }
		                } catch ( TaxJar\Exception $e ) {
			                if ( $debug === '1' ) {
				                $that->log->write( 'Unable to update TaxJar refund transaction, the error message is: ' . $e->getMessage() );
			                }
		                }
	                } else {
		                try {
			                $updateRefund = $client->updateRefund( [
				                'transaction_id'           => 'refund_' . $order['order_id'],
				                'transaction_reference_id' => $order['order_id'],
				                'transaction_date'         => $date,
				                'from_country'             => $from_country,
				                'from_zip'                 => $from_zipcode,
				                'from_state'               => $from_state,
				                'from_city'                => $from_city,
				                'from_street'              => $from_street,
				                'to_country'               => $order['shipping_iso_code_2'],
				                'to_zip'                   => $order['shipping_postcode'],
				                'to_state'                 => $order['shipping_zone_code'],
				                'to_city'                  => $order['shipping_city'],
				                'to_city'                  => $order['shipping_city'],
				                'to_street'                => $order['shipping_address_1'],
				                'amount'                   => $amount,
				                'shipping'                 => $shipping,
				                'sales_tax'                => $tax,
				                'line_items'               => $lines,
				                'plugin' => 'abantecart'
			                ] );
			                if ( $debug === '1' ) {
				                $that->log->write( 'Update TaxJar refund order API response: ' . var_export( $updateRefund, true ) );
			                }
		                } catch ( TaxJar\Exception $e ) {
			                if ( $debug === '1' ) {
				                $that->log->write( 'Unable to update TaxJar refund transaction, the error message is: ' . $e->getMessage() );
			                }
		                }
	                }
                }
            }
        }
    }

    public function onControllerPagesExtensionExtensions_InitData() {
        $that =& $this->baseObject;
        $current_ext_id = $that->request->get['extension'];

        if ($this->baseObject_method === 'edit') {
            if ($current_ext_id==='taxjar_integration') {
                $nexus = $this->dispatch('pages/extension/nexus_address/downloadNexus', array($that->request->post));
                $nexus->dispatchGetOutput();
            }
        }
    }

    public function onControllerPagesExtensionExtensions_UpdateData() {
        $that =& $this->baseObject;
        $current_ext_id = $that->request->get['extension'];
        //if (IS_ADMIN && $current_ext_id == 'taxjar_integration' && $this->baseObject_method == 'edit') {
        //    $html = '<a class="btn btn-white tooltips" target="_blank"'
        //        .' href="http://www.avalara.com/integrations/abantecart" title="Visit Avalara">'
        //        .'<i class="fa fa-external-link fa-lg"></i></a>';
        //    $that->view->addHookVar('extension_toolbar_buttons', $html);
        //}

        if ($this->baseObject_method === 'edit') {
            if ($current_ext_id === 'taxjar_integration') {
                if ($that->config->get('taxjar_integration_status') == 1) {
                    $taxjar_integration_total_status = 1;
                } else {
                    $taxjar_integration_total_status = 0;
                }
                $that->loadModel('setting/setting');
                $activateTotalArray = array(
                    'taxjar_integration_total' => array(
                        'taxjar_integration_total_status' => $taxjar_integration_total_status,
                    ),
                );
                foreach ($activateTotalArray as $group => $values) {
                    $that->model_setting_setting->editSetting($group, $values);
                }
                $that->load->model('extension/nexus_address');
                $that->view->assign('calculate_tax', $that->config->get('taxjar_integration_sales_tax_calculation'));
                $that->view->assign('tax_report', $that->config->get('taxjar_integration_sales_tax_report'));
                $nexus = $this->dispatch('pages/taxjar_integration/nexus_address/getNexus', array());
                $that->view->assign('nexus_address', $nexus->dispatchGetOutput());
                $sync_order = $this->dispatch('pages/taxjar_integration/sync_order/getSyncOrder', array());
                $that->view->assign('sync_order', $sync_order->dispatchGetOutput());
                $fallback_rates = $this->dispatch('pages/taxjar_integration/nexus_address/getFallbackRates', array());
                $that->view->assign('fallback_rates', $fallback_rates->dispatchGetOutput());
                $that->processTemplate('pages/extension/taxjar_integration_settings.tpl');
            }
        }
    }

    public function onControllerResponsesListingGridExtension_UpdateData() {
        $that = $this->baseObject;
        if ($this->baseObject_method === 'update') {
            if (isset($that->request->post['taxjar_integration']['taxjar_integration_status'])) {
                $that->loadModel('setting/setting');
                $activateTotalArray =
                    array(
                        'taxjar_integration_total' => array(
                            'taxjar_integration_total_status' => $that->request->post['taxjar_integration']['taxjar_integration_status'],
                        ),
                    );
                foreach ($activateTotalArray as $group => $values) {
                    $that->model_setting_setting->editSetting($group, $values);
                }
            }
        }
    }

    public function onControllerPagesCheckoutSuccess_UpdateData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that =& $this->baseObject;
        if ($this->baseObject_method='main') {
            if (isset($that->session->data['taxjar_order_id'])) {
            	//$that->cache->delete('TaxJar');
                $order_id=$that->session->data['taxjar_order_id'];
                $tax_rate_cache='TaxJar.order_id.'.$order_id.'.taxjar_tax_rate';
                $that->cache->delete($tax_rate_cache);
                $cache_zipcode='TaxJar.order_id.'.$order_id.'.zipcode';
                $that->cache->delete($cache_zipcode);
                $cache_quantity='TaxJar.order_id.'.$order_id.'.quantity';
                $that->cache->delete($cache_quantity);
                $cache_total='TaxJar.order_id.'.$order_id.'.total';
                $that->cache->delete($cache_total);
                unset($that->session->data['taxjar_order_id']);
            }
        }
    }

    public function onControllerPagesAccountEdit_InitData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = $this->baseObject;
        if ($that->request->is_POST() && $that->request->post['exemption_number']) {
            $that->loadModel('extension/taxjar_integration');
            $customer_settings =
                $that->model_extension_taxjar_integration->getCustomerSettings($that->customer->getId());
            if (in_array($customer_settings['status'], array(0, 2))) {
                $that->loadLanguage('taxjar_integration/taxjar_integration');
                $that->model_extension_taxjar_integration->setCustomerSettings(
                    $that->customer->getId(),
                    array(
                        'exemption_number' => $that->request->post['exemption_number'],
                        'exempt_group'  => $that->request->post['exempt_group'],
                    )
                );
                $that->messages->saveNotice(
                    $that->language->get('taxjar_integration_review_number_title'),
                    sprintf(
                        $that->language->get('taxjar_integration_review_number_message'),
                        $that->customer->getId()
                    ),
                    false);
            }
        }
    }

    public function onControllerPagesAccountEdit_UpdateData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $data = array();
        $that = $this->baseObject;
        $that->loadModel('extension/taxjar_integration');
        $that->loadLanguage('taxjar_integration/taxjar_integration');
        $data['text_tax_exemption'] = $that->language->get('taxjar_integration_text_tax_exemption');

        $customer_settings = $that->model_extension_taxjar_integration->getCustomerSettings($that->customer->getId());
        $data['form'] = array('fields' => array());
        if ($customer_settings['status'] == 1) {
            $data['text_status'] = $that->language->get('taxjar_integration_status_approved');
        } else {
            if ($customer_settings['status'] == 0 && $customer_settings['exemption_number']) {
                $data['text_status'] = $that->language->get('taxjar_integration_status_pending');
            } elseif ($customer_settings['status'] == 2) {
                $data['text_status'] = $that->language->get('taxjar_integration_status_declined');
            }
            if (!$customer_settings['exemption_number'] || $customer_settings['status'] == 2) {
                $form = new AForm();
                $form->setForm(array('form_name' => 'AccountFrm'));
                $data['entry_exemption_number'] = $that->language->get('taxjar_integration_exemption_number');
                $data['form']['fields']['exemption_number'] = $form->getFieldHtml(
                    array(
                        'type'  => 'input',
                        'name'  => 'exemption_number',
                        'value' => $customer_settings['exemption_number'],
                        'style' => 'highlight',
                    )
                );
                $data['entry_exempt_group'] = $that->language->get('taxjar_integration_exempt_group');
                $data['form']['fields']['exempt_group'] = $form->getFieldHtml(array(
                    'type'    => 'selectbox',
                    'name'    => 'exempt_group',
                    'value'   => $customer_settings['exempt_group'],
                    'options' => $this->exemptGroups,
                ));
            }
        }

        if ($data['text_status']) {
            $data['entry_status'] = $that->language->get('taxjar_integration_status');
        }

        $view = new AView($this->registry, 0);
        $view->batchAssign($data);
        $that->view->addHookVar('customer_attributes', $view->fetch('pages/account/tax_exempt_edit.tpl'));
    }

    public function onControllerPagesAccountCreate_UpdateData() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $data = array();
        /**
         * @var ControllerPagesAccountCreate $that
         */
        $that = $this->baseObject;
        $that->loadModel('extension/taxjar_integration');
        $that->loadLanguage('taxjar_integration/taxjar_integration');

        if ($that->request->is_POST() && $that->data['customer_id']
            && $that->request->post['exemption_number']
            && !$that->errors) {
            $customer_id = $that->data['customer_id'];
            $customer_settings = $that->model_extension_taxjar_integration->getCustomerSettings($customer_id);
            if (in_array($customer_settings['status'], array(0, 2))) {
                $that->model_extension_taxjar_integration->setCustomerSettings(
                    $customer_id,
                    array(
                        'exemption_number' => $that->request->post['exemption_number'],
                        'exempt_group'  => $that->request->post['exempt_group'],
                    )
                );
                $that->messages->saveNotice(
                    $that->language->get('taxjar_integration_review_number_title'),
                    sprintf($that->language->get('taxjar_integration_review_number_message'), $customer_id),
                    false);
            }
            return null;
        }

        $data['text_tax_exemption'] = $that->language->get('taxjar_integration_text_tax_exemption');
        $data['form'] = array('fields' => array());
        $form = new AForm();
        $form->setForm(array('form_name' => 'AccountFrm'));
        $data['entry_exemption_number'] = $that->language->get('taxjar_integration_exemption_number');
        $data['form']['fields']['exemption_number'] = $form->getFieldHtml(
            array(
                'type'  => 'input',
                'name'  => 'exemption_number',
                'value' => $that->request->post['exemption_number'],
                'style' => 'highlight',
            )
        );
        $data['entry_exempt_group'] = $that->language->get('taxjar_integration_exempt_group');
        $data['form']['fields']['exempt_group'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'exempt_group',
            'value'   => $that->request->post['exempt_group'],
            'options' => $this->exemptGroups,
        ));

        $view = new AView($this->registry, 0);
        $view->batchAssign($data);
        $that->view->addHookVar('customer_attributes', $view->fetch('pages/account/tax_exempt_edit.tpl'));
    }

    public function afterModelCheckoutOrder_confirm() {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that =& $this->baseObject;
        $order_id=$that->session->data['order_id'];
        $that->load->model('checkout/order');
        $that->load->model('extension/taxjar_discount');
        $that->load->model('extension/taxjar_integration');
        $that->load->model('catalog/product');
        $order_info=$that->model_checkout_order->getOrder($order_id);
        $totals=$that->model_extension_taxjar_integration->getOrderTotals($order_id);
        $products=$that->model_extension_taxjar_integration->getOrderProducts($order_id);
        $subtotal=$sales_tax=$shp_cost=$coupon=0.00;
	    $debug = $that->config->get('taxjar_integration_debug');
        foreach ($totals as $total) {
            if ($total['key'] === 'subtotal') {
                $subtotal = round($total['value'],2);
            } elseif($total['key'] === 'taxjar_integration_total') {
                $sales_tax = round($total['value'],2);
            } elseif ($total['key'] === 'shipping') {
                $shp_cost = round($total['value'],2);
            } elseif ($total['key'] === 'coupon') {
                $coupon = round($total['value'], 2);
            }
        }

        $to_street = $order_info['shipping_address_1'];
        $to_city = $order_info['shipping_city'];
        $to_state = $order_info['shipping_zone_code'];
        $to_country = $order_info['shipping_iso_code_2'];
        $to_zipcode = $order_info['shipping_postcode'];
        $date = new DateTime($order_info['date_added']);
        $date = $date->format('Y-m-d');
        $enable_export = $that->config->get('taxjar_integration_sales_tax_report');
        if ($enable_export) {
            $customer_group_id = $that->customer->getCustomerGroupId();
            $tax_exempt = $that->model_extension_taxjar_integration->getTaxExemptByGroupId($customer_group_id);
            $customer_id = $order_info['customer_id'];
            $customer_settings = $that->model_extension_taxjar_integration->getCustomerSettings($customer_id);
            $email = $order_info['email'];
            //get whether customer is synced to TaxJar or not
            $info = $that->model_extension_taxjar_integration->getTaxJarCustomer($email);
            $taxjar_success_id = $that->config->get('taxjar_integration_status_success_settled');

            $order_status_id = $order_info['order_status_id'];

            $lines = array();
            foreach ($products as $key => $product) {
                $tmp = $that->model_catalog_product->getProduct($product['product_id']);
                $tax_code = $that->model_extension_taxjar_integration->getProductTaxCode($product['product_id']);
                $discount = $that->model_extension_taxjar_integration->getProductDiscount($order_id, $product['product_id']);
                if ($tmp['sku']) {
                    $line['id'] = $tmp['sku'];
                } else {
                    $line['id'] = $product['product_id'];
                }
                $line['description'] = $tmp['name'];
                $line['quantity'] = (int)$product['quantity'];
	            $line['product_tax_code'] = isset($tax_code) ? $tax_code : '11111';
                $line['unit_price'] = round($product['price'], 2);
                if (!empty($discount) && (float)$discount !== 0.00) {
                    $line['discount'] = -$discount;
                }
                $lines[] = $line;
            }
            $from_country = $that->config->get('taxjar_integration_country');
            $from_zipcode = $that->config->get('taxjar_integration_postal_code');
            $from_state = $that->config->get('taxjar_integration_state');
            $from_city = $that->config->get('taxjar_integration_city');
            $from_street = $that->config->get('taxjar_integration_street');

            $api_key = $that->config->get('taxjar_integration_api_key');
            $sandbox_mode = $that->config->get('taxjar_integration_sandbox_status');
            $sandbox_api_key = $that->config->get('taxjar_integration_sandbox_api_key');
            if ($api_key || $sandbox_api_key) {
                if ($sandbox_mode === '1' && $sandbox_api_key) {
                    $client = TaxJar\Client::withApiKey($sandbox_api_key);
                    $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
                } else {
                    $client = TaxJar\Client::withApiKey($api_key);
                }
            }

            $amount = (float)$subtotal + (float)$shp_cost;
            if (isset($order_info['coupon_id'])) {
                $coupon_id = $order_info['coupon_id'];
                $coupon_code = $that->model_extension_taxjar_discount->getCouponData($coupon_id);
                if ($coupon_id !== 0 || !empty($coupon_id)) {
                    if ($coupon_code['shipping'] === '1') {
                        $shp_cost = $shp_cost + $coupon;
                    }
                }
                $amount = (float)$subtotal + (float)$shp_cost + $coupon;
            }

            if (isset($order_id) && $order_status_id === $taxjar_success_id) {
                if ($info['synced'] === '1' && $customer_settings['status']==='1') {
                    try {
                        $create = $client->createOrder([
                            'transaction_id' => $order_id,
                            'transaction_date' => $date,
                            'from_country' => $from_country,
                            'from_zip' => $from_zipcode,
                            'from_state' => $from_state,
                            'from_city' => $from_city,
                            'from_street' => $from_street,
                            'to_country' => $to_country,
                            'to_zip' => $to_zipcode,
                            'to_state' => $to_state,
                            'to_city' => $to_city,
                            'to_street' => $to_street,
                            'amount' => $amount,
                            'shipping' => (float)$shp_cost,
                            'sales_tax' => (float)$sales_tax,
                            'customer_id' => 'customer_'.$info['taxjar_customer_id'],
                            'line_items' => $lines,
                            'plugin' => 'abantecart'
                        ]);
                        if ($debug==='1') {
                        	$that->log->write('Transmit order to TaxJar API response: '.var_export($create,true));
                        }
                    } catch (TaxJar\Exception $e) {
	                    if ($debug === '1') {
		                    $that->log->write( 'Unable to transmit the order to TaxJar, the error message is: ' . $e->getMessage() );
	                    }
                    }
                } else {
                    try {
                        $create = $client->createOrder([
                            'transaction_id' => $order_id,
                            'transaction_date' => $date,
                            'from_country' => $from_country,
                            'from_zip' => $from_zipcode,
                            'from_state' => $from_state,
                            'from_city' => $from_city,
                            'from_street' => $from_street,
                            'to_country' => $to_country,
                            'to_zip' => $to_zipcode,
                            'to_state' => $to_state,
                            'to_city' => $to_city,
                            'to_street' => $to_street,
                            'amount' => $amount,
                            'shipping' => (float)$shp_cost,
                            'sales_tax' => (float)$sales_tax,
                            'line_items' => $lines,
                            'plugin' => 'abantecart'
                        ]);
	                    if ($debug==='1') {
		                    $that->log->write('Transmit order to TaxJar API without customer id response: '.var_export($create,true));
	                    }
                    } catch (TaxJar\Exception $e) {
	                    if ( $debug === '1' ) {
		                    $that->log->write( 'Unable to transmit TaxJar order, the error message is: ' . $e->getMessage() );
	                    }
                    }
                }
            }
        }
    }

    /**
     * Rounding Rates Function
     * @param $rate
     * @return string
     */
    private function rounding($rate) {
        $Rates = explode(".", $rate);
        $rates_len = strlen($Rates[1]);
        if ($rates_len === 4) {
            $splitRates = str_split($Rates[1], 2);
            $temp_rates = (int)$splitRates[1];
            if ($temp_rates <= 25) {
                $tmp_amount = $Rates[0] . '.' . $splitRates[0];
            } elseif ($temp_rates > 25 && $temp_rates <= 75) {
                $tmp_amount = $Rates[0] . '.' . $splitRates[0] . '5';
            } elseif ($temp_rates > 75) {
                $tmp_amount = $Rates[0] . '.' . (string)((int)$splitRates[0] + 1);
            }
            $amt = explode(".", $tmp_amount);
            $amt_len = strlen($amt[1]);
            if ($amt_len === 3) {
                $splitAmt = str_split($amt[1], 1);
                $temp_amt = (int)$splitAmt[2];
                $temp_amt2 = (int)$splitAmt[1] . $splitAmt[2];
                if ($temp_rates <= 2 || $temp_amt2 <= 25) {
                    $rates = $amt[0] . '.' . $splitAmt[0] . $splitAmt[1];
                } elseif (($temp_amt > 2 && $temp_amt <= 7) || ($temp_amt2 > 25 && $temp_amt2 <= 75)) {
                    $rates = $amt[0] . '.' . $splitAmt[0] . '5';
                } elseif ($temp_amt > 7) {
                    $rates = $amt[0] . '.' . (string)((int)($splitAmt[0] . $splitAmt[1]) + 1);
                }
            } else {
                $rates = $tmp_amount;
            }
        } elseif ($rates_len === 3) {
            $splitRates = str_split($Rates[1], 1);
            $temp_rates = (int)$splitRates[2];
            if ($temp_rates <= 2) {
                $rates = $Rates[0] . '.' . $splitRates[0] . $splitRates[1];
            } elseif ($temp_rates > 2 && $temp_rates <= 7) {
                $rates = $Rates[0] . '.' . $splitRates[0] . '5';
            } elseif ($temp_rates > 7) {
                $rates = $Rates[0] . '.' . (string)((int)($splitRates[0] . $splitRates[1]) + 1);
            }
        } else {
            $rates = $rate;
        }
        return $rates;
    }

    // Dispatch new controller to be ran
    protected function dispatch($dispatch_rt, $args = array('')) {
        return new ADispatcher($dispatch_rt, $args);
    }
}