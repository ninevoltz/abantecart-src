<?php
/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesTotalTaxjarIntegrationTotal extends AController {
    public $data = array();
    private $error = array();
    private $fields = array(
        'taxjar_integration_total_status',
        'taxjar_integration_total_sort_order',
        'taxjar_integration_total_calculation_order',
        'taxjar_integration_total_total_type',
    );

    public function main()
    {

        $this->loadModel('setting/setting');
        $this->loadLanguage('extension/total');
        $this->loadLanguage('taxjar_integration/taxjar_integration');

        if ($this->request->is_POST() && $this->validate()) {
            $this->model_setting_setting->editSetting('taxjar_integration_total', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('total/taxjar_integration_total'));
        }

        $this->document->setTitle($this->language->get('total_name'));

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        $this->data['success'] = $this->session->data['success'];
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('extension/total'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('total/taxjar_integration_total'),
            'text'      => $this->language->get('total_name'),
            'separator' => ' :: ',
        ));

        foreach ($this->fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } else {
                $this->data [$f] = $this->config->get($f);
            }
        }

        $this->data ['action'] = $this->html->getSecureURL('total/taxjar_integration_total');
        $this->data['cancel'] = $this->html->getSecureURL('extension/total');
        $this->data ['heading_title'] = $this->language->get('text_edit').' '.$this->language->get('total_name');
        $this->data ['form_title'] = $this->language->get('total_name');
        $this->data ['update'] =
            $this->html->getSecureURL('listing_grid/total/update_field', '&id=taxjar_integration_total');

        $form = new AForm ('HS');
        $form->setForm(array('form_name' => 'editFrm', 'update' => $this->data ['update']));

        $this->data['form']['form_open'] =
            $form->getFieldHtml(array('type' => 'form', 'name' => 'editFrm', 'action' => $this->data ['action']));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'submit',
            'text'  => $this->language->get('button_save'),
            'style' => 'button1',
        ));
        $this->data['form']['cancel'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'cancel',
            'text'  => $this->language->get('button_cancel'),
            'style' => 'button2',
        ));

        $this->data['form']['fields']['status'] = $form->getFieldHtml(array(
            'type'  => 'checkbox',
            'name'  => 'taxjar_integration_total_status',
            'value' => $this->data['taxjar_integration_total_status'],
            'style' => 'btn_switch',
        ));

        $this->data['form']['fields']['total_type'] = $form->getFieldHtml(array(
            'type'  => 'hidden',
            'name'  => 'taxjar_integration_total_total_type',
            'value' => 'taxjar_integration',
        ));
        $this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'taxjar_integration_total_sort_order',
            'value' => $this->data['taxjar_integration_total_sort_order'],
        ));
        $this->data['form']['fields']['calculation_order'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'taxjar_integration_total_calculation_order',
            'value' => $this->data['taxjar_integration_total_calculation_order'],
        ));

        $this->view->assign('help_url', $this->gen_help_url('edit_taxjar_integration_total'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/total/form.tpl');

    }

    private function validate()
    {
        if (!$this->user->canModify('total/taxjar_integration_total')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
