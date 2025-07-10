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
 * Class ControllerPagesTaxjarIntegrationNexusAddress
 * @property ModelExtensionNexusAddress $model_extension_nexus_address
 */

class ControllerPagesTaxjarIntegrationNexusAddress extends AController {
    public $data=array();

    public function downloadNexus() {
        $this->data = func_get_arg(0);
        $api_key=$this->data['taxjar_integration_api_key'];
        $sandbox_api_key=$this->data['taxjar_integration_sandbox_api_key'];
        $sandbox_mode=$this->data['taxjar_integration_sandbox_status'];
	    $debug = $this->config->get('taxjar_integration_debug');
        if ($this->data['taxjar_integration_sales_tax_calculation']==='1') {
            $nexus_exist = $this->model_extension_nexus_address->getNexus();
            if ($nexus_exist === 0) {
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
                    foreach ($nexus as $value) {
                        $data['country_code']=$value->country_code;
                        $data['country']=$value->country;
                        $data['region_code']=$value->region_code;
                        $data['region']=$value->region;
                        $this->model_extension_nexus_address->addNexusAddress($data);
                    }
	                if ($debug==='1') {
		                $this->log->write('TaxJar Nexus Address API response: '.var_export($nexus,true));
	                }
                } catch (TaxJar\Exception $e) {
                	if ($debug==='1') {
		                $this->log->write( 'Get Nexus Addresses error message: ' . $e->getMessage() );
	                }
                }
            }
        }
    }

    public function getNexus() {
        $this->load->model('extension/nexus_address');
        $this->load->language('taxjar_integration/taxjar_integration');
        $this->data['text_nexus_information'] = $this->language->get('text_nexus_information');
        $this->data['text_or'] = $this->language->get('text_or');
        $this->data['text_manage'] = $this->language->get('text_manage');
        $this->data['button_sync'] = $this->language->get('button_sync');
        $this->data['manage_nexus_url']='https://app.taxjar.com/account#states';
        $this->data['sync_url']=$this->html->getSecureURL('r/extension/taxjar_integration/updateNexus');
        $this->data['redirect']=$this->html->getSecureURL('extension/extensions/edit','&extension=taxjar_integration');
        $this->data['nexus']=$this->model_extension_nexus_address->getNexusAddress();
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/taxjar_integration/nexus_address.tpl');
    }

    public function getFallbackRates() {
        $this->load->language('taxjar_integration/taxjar_integration');
        $this->load->model('extension/nexus_address');
        $info=$this->model_extension_nexus_address->getNexusAddress();
        $form=new AForm();
        foreach ($info as $data) {
            $data['fallback_rate']=$this->config->get('taxjar_integration_fallback_'.$data['region_code']);
            $this->data['form']['fields']['taxjar_integration_fallback_'.$data['region_code']]=$form->getFieldHtml(array(
                'type' => 'input',
                'name' => 'taxjar_integration_fallback_'.$data['region_code'],
                'value' => $data['fallback_rate']));
        }
        $this->data['text_fallback'] = $this->language->get('text_fallback');
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/taxjar_integration/fallback_rates.tpl');
    }
}