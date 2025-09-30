<?php
/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

require DIR_EXT.'taxjar_integration/core/vendor/autoload.php';

/**
 * Class ControllerResponsesExtensionTaxjarIntegration
 * @property ModelExtensionTaxjarIntegration $model_extension_taxjar_integration
 * @property ModelExtensionNexusAddress $model_extension_nexus_address
 */
class ControllerResponsesExtensionTaxjarIntegration extends AController {

    public $data = array();

    public function test(){
        $this->load->language('taxjar_integration/taxjar_integration');
        $api_key = $this->config->get('taxjar_integration_api_key');
        $sandbox_mode = $this->config->get('taxjar_integration_sandbox_status');
        $sandbox_api_key = $this->config->get('taxjar_integration_sandbox_api_key');
        $status = $this->config->get('taxjar_integration_status');
	    $debug = $this->config->get('taxjar_integration_debug');
        $json=array();

        if ($status==='0') {
            $json['message'] = $this->language->get('error_turn_extension_on');
            $json['error'] = true;
        } elseif (empty($api_key)) {
            $json['message'] = $this->language->get('error_api_key');
            $json['error'] = true;
        } elseif ($sandbox_mode === '1' && empty($sandbox_api_key)) {
            $json['message'] = $this->language->get('error_sandbox_api_key');
            $json['error'] = true;
        } elseif (empty($street)) {
            $json['message'] = $this->language->get('error_street');;
            $json['error'] = true;
        } elseif (empty($city)) {
            $json['message'] = $this->language->get('error_city');;
            $json['error'] = true;
        } elseif (empty($state)) {
            $json['message'] = $this->language->get('error_state');;
            $json['error'] = true;
        } elseif (empty($postal_code)) {
            $json['message'] = $this->language->get('error_postal_code');;
            $json['error'] = true;
        } elseif (empty($country)) {
            $json['message'] = $this->language->get('error_country');;
            $json['error'] = true;
        }

        if ($status==='1') {
            if ($sandbox_mode === '1' && $sandbox_api_key) {
                $client = TaxJar\Client::withApiKey($sandbox_api_key);
                $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
            } elseif ($api_key) {
                $client = TaxJar\Client::withApiKey($api_key);
            }
        }

        if ($status==='1' && (($sandbox_mode==='1' && $sandbox_api_key) || ($sandbox_mode==='0' && $api_key))) {
            try {
                $response = $client->categories();
                if ($debug==='1') {
                	$this->log->write('TaxJar Categories API response: '.var_export($response,true));
                }
                $json['message'] = $this->language->get('text_connection_success');
                $json['error'] = false;
            } catch (TaxJar\Exception $e) {
            	if ($debug==='1') {
            		$this->log->write('Connection to TaxJar server failed: ' . $e->getMessage());
	            }
                $json['message'] = 'Connection to TaxJar server failed: ' . $e->getMessage();
                $json['error'] = true;
            }
        }
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

    public function updateNexus() {
        $this->load->model('extension/nexus_address');
        $this->load->language('taxjar_integration/taxjar_integration');
        $api_key=$this->config->get('taxjar_integration_api_key');
        $sandbox_api_key=$this->config->get('taxjar_integration_sandbox_api_key');
        $sandbox_mode=$this->config->get('taxjar_integration_sandbox_status');
	    $debug = $this->config->get('taxjar_integration_debug');
        if ($api_key || $sandbox_api_key) {
            if ($sandbox_mode === '1' && $sandbox_api_key) {
                $client = TaxJar\Client::withApiKey($sandbox_api_key);
                $client->setApiConfig('api_url', TaxJar\Client::SANDBOX_API_URL);
            } else {
                $client = TaxJar\Client::withApiKey($api_key);
            }
        }
        try {
            $nexus = $client->nexusRegions();
            $this->model_extension_nexus_address->deleteNexus();
            foreach ($nexus as $value) {
                $data['country_code'] = $value->country_code;
                $data['country'] = $value->country;
                $data['region_code'] = $value->region_code;
                $data['region'] = $value->region;
                $this->model_extension_nexus_address->addNexusAddress($data);
            }
            if ($debug==='1') {
            	$this->log->write('TaxJar Nexus Address API response: '.var_export($nexus,true));
            }
            $json['error'] = false;
        } catch (TaxJar\Exception $e) {
        	if ($debug==='1') {
		        $this->log->write( 'Get Nexus Addresses error message: ' . $e->getMessage() );
	        }
            $json['error'] = true;
        }

        //$this->data['nexus']=$this->model_extension_nexus_address->getNexusAddress();
        //$html = $this->dispatch('responses/extension/taxjar_integration/syncNexus',array( $this->data));
        //$this->response->setOutput($html->dispatchGetOutput());
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

    public function syncNexus() {
        $this->data = func_get_arg(0);
        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/extension/nexus_address.tpl');
    }
}