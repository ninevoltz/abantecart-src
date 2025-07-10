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
 * Class ControllerPagesTaxjarIntegrationSyncOrder
 * @property ModelLocalisationOrderStatus $model_localisation_order_status
 */

class ControllerPagesTaxjarIntegrationSyncOrder extends AController {
    public $data=array();

    public function getSyncOrder() {
        $this->load->model('localisation/order_status');
        $this->load->language('taxjar_integration/taxjar_integration');
        $results=$this->model_localisation_order_status->getOrderStatuses();
        foreach ($results as $item) {
            $order_statuses[$item['order_status_id']] = $item['name'];
        }
        $form=new AForm();

        $this->data['form']['fields']['taxjar_integration_save_transaction']= $form->getFieldHtml(array(
           'type' => 'checkbox',
           'name' => 'taxjar_integration_save_transaction',
           'value' => $this->config->get('taxjar_integration_save_transaction'),
           'style' => 'btn_switch',
        ));
        $this->data['text_taxjar_integration_save_transaction']=$this->language->get('taxjar_integration_save_transaction');

        $this->data['form']['fields']['taxjar_integration_status_success_settled'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'taxjar_integration_status_success_settled',
            'options' => $order_statuses,
            'value'   => $this->config->get('taxjar_integration_status_success_settled') ? $this->config->get('taxjar_integration_status_success_settled') : '5',
        ));
        $this->data['text_taxjar_integration_status_success_settled']=$this->language->get('taxjar_integration_status_success_settled');

        $this->data['form']['fields']['taxjar_integration_delete_transaction']= $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'taxjar_integration_delete_transaction',
            'value' => $this->config->get('taxjar_integration_delete_transaction'),
            'style' => 'btn_switch',
        ));
        $this->data['text_taxjar_integration_delete_transaction']=$this->language->get('taxjar_integration_delete_transaction');

        $this->data['form']['fields']['taxjar_integration_status_delete_settled'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'taxjar_integration_status_delete_settled',
            'options' => $order_statuses,
            'value'   => $this->config->get('taxjar_integration_status_delete_settled') ? $this->config->get('taxjar_integration_status_delete_settled') : '7',
        ));
        $this->data['text_taxjar_integration_status_delete_settled']=$this->language->get('taxjar_integration_status_delete_settled');

        $this->data['form']['fields']['taxjar_integration_refund_transaction']= $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'taxjar_integration_refund_transaction',
            'value' => $this->config->get('taxjar_integration_refund_transaction'),
            'style' => 'btn_switch',
        ));
        $this->data['text_taxjar_integration_refund_transaction']=$this->language->get('taxjar_integration_refund_transaction');

        $this->data['form']['fields']['taxjar_integration_status_refund_settled'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'taxjar_integration_status_refund_settled',
            'options' => $order_statuses,
            'value'   => $this->config->get('taxjar_integration_status_refund_settled') ? $this->config->get('taxjar_integration_status_refund_settled') : '12',
        ));
        $this->data['text_taxjar_integration_status_refund_settled']=$this->language->get('taxjar_integration_status_refund_settled');

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/taxjar_integration/sync_order.tpl');
    }
}