<?php
/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ControllerPagesCatalogProductFeatures
 *
 * @property ModelProductFeaturesProductFeatures $model_taxjar_integration_taxjar_integration
 * @property ModelExtensionTaxjarIntegration     $model_extension_taxjar_integration
 */
class ControllerPagesSaleTaxjarCustomerData extends AController {
    public $data = array();
    public $error = array();

    public function main() {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('sale/customer');
        $this->loadModel('extension/taxjar_integration');

        $this->loadLanguage('sale/customer');
        $this->loadLanguage('taxjar_integration/taxjar_integration');

        $this->document->setTitle($this->language->get('taxjar_integration_name'));

        $this->view->assign('error_warning', $this->session->data['warning']);
        if (isset($this->session->data['warning'])) {
            unset($this->session->data['warning']);
        }
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $customer_id = $this->request->get['customer_id'];
	    $debug = $this->config->get('taxjar_integration_debug');

	    if ($this->request->is_POST() && $this->validateForm()) {
            $this->loadModel('extension/taxjar_integration');
            $this->load->model('sale/customer');
            $this->model_extension_taxjar_integration->setCustomerSettings(
                $customer_id,
                $this->request->post
            );
            $customer_address=$this->model_extension_taxjar_integration->getDefaultAddressByCustomerId($customer_id);
            $customer_info=$this->model_sale_customer->getCustomer($customer_id);
            $email=$customer_info['email'];
            $info=$this->model_extension_taxjar_integration->getTaxJarCustomer($email);
            $status = $this->request->post['status'];
            $update = $this->request->post['update'];

		    $api_key = $this->config->get('taxjar_integration_api_key');
		    $sandbox_mode = $this->config->get('taxjar_integration_sandbox_status');
		    $sandbox_api_key = $this->config->get('taxjar_integration_sandbox_api_key');
		    if ($sandbox_mode==='1' && $sandbox_api_key) {
			    $client = TaxJar\Client::withApiKey($sandbox_api_key);
			    $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
		    } else {
			    $client = TaxJar\Client::withApiKey($api_key);
		    }

		    If ($status === '1' && $update==='0') {
                if (empty($info['synced']) || $info['synced']===0) {
                    if ($info['taxjar_customer_id']) {
                        $taxjar_customer_id = $info['taxjar_customer_id'];
                    } else {
                        $taxjar_customer_id = $this->model_extension_taxjar_integration->addTaxJarCustomer($email);
                    }

	                try {
		                $clientExist = $client->showCustomer('customer_'.$taxjar_customer_id);
		                if ($debug==='1') {
			                $this->log->write('TaxJar customer API response: '.var_export($clientExist,true));
		                }
		                $exist = true;
	                } catch (TaxJar\Exception $e) {
                    	$exist = false;
		                if ($debug==='1') {
			                $this->log->write('Get customer error message: '.$e->getMessage());
		                }
	                }

		            $exempt_type=$this->request->post['exempt_group'];
                    if ($exist===true) {
	                    $data['customer_id'] = $customer_id;
	                    $data['exempt_group'] = $this->request->post['exempt_group'];
	                    $this->update_customer($data);
                    } else {
	                    try {
		                    $response       = $client->createCustomer( [
			                    'customer_id'    => 'customer_' . $taxjar_customer_id,
			                    'exemption_type' => $exempt_type,
			                    'name'           => $customer_address['firstname'] . ' ' . $customer_address['lastname'],
			                    'country'        => $customer_address['country'],
			                    'state'          => $customer_address['state'],
			                    'zip'            => $customer_address['postcode'],
			                    'city'           => ucfirst( $customer_address['city'] ),
			                    'street'         => $customer_address['address'],
			                    'plugin'         => 'abantecart'
		                    ] );
		                    $data['email']  = $email;
		                    $data['synced'] = 1;
		                    $this->model_extension_taxjar_integration->updateTaxJarCustomerSync( $data );
		                    $this->session->data['success'] = $this->language->get( 'text_add_customer_success' );
		                    if ( $debug === '1' ) {
			                    $this->log->write( 'Create TaxJar Customer API response: ' . var_export( $response, true ) );
		                    }
	                    } catch ( TaxJar\Exception $e ) {
		                    $this->session->data['success'] = $this->language->get( 'text_add_customer_failed' );
		                    if ( $debug === '1' ) {
			                    $this->log->write( 'Create Customer error message: ' . $e->getMessage() );
		                    }
	                    }
                    }
                }
            } elseif ($status === '1' && $update==='1') {
                $data['customer_id'] = $customer_id;
                $data['exempt_group'] = $this->request->post['exempt_group'];
                $this->update_customer($data);
            }

            $redirect_url = $this->html->getSecureURL('sale/taxjar_customer_data', '&customer_id=' . $customer_id);
            $this->redirect($redirect_url);
        }

        $this->getForm();
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getForm() {
        $customer_id = $this->request->get['customer_id'];
        $customer_info=$this->model_sale_customer->getCustomer($customer_id);
        $email=$customer_info['email'];
        $info=$this->model_extension_taxjar_integration->getTaxJarCustomer($email);
        $customer_settings=$this->model_extension_taxjar_integration->getCustomerSettings($customer_id);
        if ($info['synced']==='1' && $customer_settings['status']==='1') {
            $this->data['exist'] =  $this->language->get('text_customer_synced');
        } else {
            $this->data['exist'] = '';
        }
        $this->loadLanguage('taxjar_integration/taxjar_integration');
        $this->data['token'] = $this->session->data['token'];
        $this->data['error'] = $this->error;

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('sale/customer'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ));

        //allow to change this list via hook
        $this->data['fields'] = array_merge(array(
            'status'                => null,
            'exemption_number_name' => null,
            'exempt_group_name'  => null,
        ),
            (array)$this->data['fields']);
        $this->loadModel('sale/customer');
        $customer_info = $this->model_sale_customer->getCustomer($customer_id);

        $fields = array_keys($this->data['fields']);
        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } elseif (isset($customer_info)) {
                $this->data[$f] = $customer_info[$f];
            } else {
                $this->data[$f] = '';
            }
        }

        $this->data['customer_id'] = $customer_id;
        $this->data['action'] = $this->html->getSecureURL('sale/taxjar_customer_data', '&customer_id='.$customer_id);
        $this->data['heading_title'] = $this->language->get('text_edit')
            .$this->language->get('text_customer')
            .' - '
            .$customer_info['firstname']
            .' '.$customer_info['lastname'];
        $form = new AForm('ST');

        $this->document->addBreadcrumb(array(
            'href'      => $this->data['action'],
            'text'      => $this->data['heading_title'],
            'separator' => ' :: ',
            'current'   => true,
        ));
        $this->data['tabs'][] = array(
            'href' => $this->html->getSecureURL('sale/customer/update', '&customer_id='.$customer_id),
            'text' => $this->language->get('tab_customer_details'),
        );
        if (has_value($customer_id)) {
            $this->data['tabs'][] = array(
                'href' => $this->html->getSecureURL('sale/customer_transaction', '&customer_id='.$customer_id),
                'text' => $this->language->get('tab_transactions'),
            );
            $this->data['tabs']['general'] = array(
                'href'   => $this->html->getSecureURL('sale/taxjar_customer_data', '&customer_id='.$customer_id),
                'text'   => $this->language->get('taxjar_integration_name'),
                'active' => true,
            );
        }

        $form->setForm(array(
            'form_name' => 'cgFrm',
            'update'    => $this->data['update'],
        ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type'   => 'form',
            'name'   => 'cgFrm',
            'attr'   => 'data-confirm-exit="true" class="form-horizontal"',
            'action' => $this->data['action'],
        ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_save'),
        ));

        if ($info['synced']==='1' && $customer_settings['status']==='1') {
            $this->data['form']['update'] = $form->getFieldHtml(array(
                'type' => 'button',
                'name' => 'update',
                'text' => $this->language->get('button_update'),
            ));
            $this->data['update']='1';
        } else {
            $this->data['update']='0';
        }
        $this->data['form']['reset'] = $form->getFieldHtml(array(
            'type' => 'button',
            'name' => 'reset',
            'text' => $this->language->get('button_reset'),
        ));

        $this->loadModel('extension/taxjar_integration');
        $form_data = $this->model_extension_taxjar_integration->getCustomerSettings($customer_id);
        $this->data['entry_status'] = $this->language->get('exemption_status');
        $this->data['form']['fields']['details']['status'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'status',
            'options' => array(
                '0' => $this->language->get('exemption_status_pending'),
                '1' => $this->language->get('exemption_status_approved'),
                '2' => $this->language->get('exemption_status_declined'),
            ),
            'value'   => $form_data['status'],
        ));
        $this->data['entry_exemption_number'] = $this->language->get('exemption_number_name');
        $this->data['form']['fields']['details']['exemption_number'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'exemption_number',
            'value' => $form_data['exemption_number'],
        ));

        $this->data['entry_exempt_group'] = $this->language->get('exempt_group_name');
        $this->data['form']['fields']['details']['exempt_group'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'exempt_group',
            'value'   => $form_data['exempt_group'],
            'options' => array(
                'none'  => $this->language->get('text_select'),
                'wholesale' => 'A. Wholesale',
                'government' => 'B. Government',
                'other' => 'C. Other',
                'non_exempt' => 'D. Non Exempt'
            ),
        ));

        $this->data['section'] = 'details';
        $this->data['tabs']['general']['active'] = true;

        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/sale/taxjar_customer_form.tpl');
    }

    public function update_customer($data) {
        $customer_id = $data['customer_id'];
        $this->load->language('taxjar_integration/taxjar_integration');
        $this->load->model('sale/customer');
        $this->load->model('extension/taxjar_integration');
        $customer_info = $this->model_sale_customer->getCustomer($customer_id);
        $customer_address=$this->model_extension_taxjar_integration->getDefaultAddressByCustomerId($customer_id);
        $email=$customer_info['email'];
        $info=$this->model_extension_taxjar_integration->getTaxJarCustomer($email);
        $taxjar_customer_id = $info['taxjar_customer_id'];
        $api_key = $this->config->get('taxjar_integration_api_key');
        $sandbox_mode = $this->config->get('taxjar_integration_sandbox_status');
        $sandbox_api_key = $this->config->get('taxjar_integration_sandbox_api_key');

        if ($sandbox_mode==='1' && $sandbox_api_key) {
            $client = TaxJar\Client::withApiKey($sandbox_api_key);
            $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
        } else {
            $client = TaxJar\Client::withApiKey($api_key);
        }

        $exempt_type = $data['exempt_group'];
	    $debug = $this->config->get('taxjar_integration_debug');

        try {
            $response = $client->updateCustomer([
                'customer_id' => 'customer_'.$taxjar_customer_id,
                'exemption_type' => $exempt_type,
                'name' => $customer_address['firstname'] . ' ' . $customer_address['lastname'],
                'country' => $customer_address['country'],
                'state' => $customer_address['state'],
                'zip' => $customer_address['postcode'],
                'city' => ucfirst($customer_address['city']),
                'street' => $customer_address['address'],
            ]);

	        $info['email']  = $email;
	        $info['synced'] = 1;
	        $this->model_extension_taxjar_integration->updateTaxJarCustomerSync($info);

	        $this->session->data['success'] = $this->language->get('text_update_customer_success');
            if ($debug==='1') {
                $this->log->write('Update TaxJar customer API response: '.var_export($response,true));
            }
        } catch (TaxJar\Exception $e) {
            $this->session->data['warning'] = $this->language->get('text_update_customer_failed');
	        if ($debug==='1') {
		        $this->log->write( 'Update Customer error message: ' . $e->getMessage() );
	        }
        }
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/taxjar_customer_data')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}