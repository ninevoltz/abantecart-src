<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesAccountCreate extends AController
{
    public $errors = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if ($this->customer->isLogged()) {
            redirect($this->html->getSecureURL('account/account'));
        }

        $this->document->setTitle($this->language->get('heading_title'));
        /** @var ModelAccountCustomer $mdl */
        $mdl = $this->loadModel('account/customer');
        $request_data = $this->request->post;
        if ($this->request->is_POST()) {
            if ($this->csrftoken->isTokenValid()) {
                $this->errors = array_merge( $this->errors, $mdl->validateRegistrationData($request_data) );
            } else {
                $this->errors['warning'] = $this->language->get('error_unknown');
            }
            if (!$this->errors) {
                //if allow login as email, need to set loginname = email
                if (!$this->config->get('prevent_email_as_login')) {
                    $request_data['loginname'] = $request_data['email'];
                }

                $this->data['customer_id'] = $mdl->addCustomer($request_data);
                $mdl->editCustomerNotifications($request_data, $this->data['customer_id']);
                unset($this->session->data['guest']);

                try {
                    if (!$this->config->get('config_customer_approval')) {
                        //add and send account activation link if required
                        if (!$this->config->get('config_customer_email_activation')) {
                            //send welcome email
                            $mdl->sendWelcomeEmail($this->request->post['email'], true);
                            //login customer after create account is approving and
                            // email activation are disabled in settings
                            $this->customer->login($request_data['loginname'], $request_data['password']);
                        } else {
                            //send activation email request and wait for confirmation
                            $mdl->emailActivateLink($this->data['customer_id']);
                        }
                    } else {
                        //send welcome email, but need manual approval
                        $mdl->sendWelcomeEmail($this->request->post['email'], false);
                    }
                }catch (Exception $e) {
                    $this->log->write(__CLASS__ . '::' . __FUNCTION__ . '() error: ' . $e->getMessage());
                    $this->messages->saveError("Mailer Critical Error!", $e->getMessage());
                }

                $this->extensions->hk_UpdateData($this, __FUNCTION__);

                //set success text for non-approved customers on login page after redirect
                if ($this->config->get('config_customer_approval')) {
                    $this->loadLanguage('account/success');
                    $this->session->data['success'] = sprintf(
                        $this->language->get('text_approval', 'account/success'),
                        $this->config->get('store_name'),
                        $this->html->getSecureURL('content/contact')
                    );
                }

                if ($this->config->get('config_customer_email_activation') || !$this->session->data['redirect']) {
                    $redirect_url = $this->html->getSecureURL('account/success');
                } else {
                    $redirect_url = $this->session->data['redirect'];
                }
                redirect($redirect_url);
            } else {
                if (!$this->errors['warning']) {
                    $this->errors['warning'] = implode('<br>', $this->errors);
                }
            }
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/account'),
                'text'      => $this->language->get('text_account'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/create'),
                'text'      => $this->language->get('text_create'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        if ($this->config->get('prevent_email_as_login')) {
            $this->data['noemaillogin'] = true;
        }

        $form = new AForm();
        $form->setForm(['form_name' => 'AccountFrm']);
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'AccountFrm',
                'action' => $this->html->getSecureURL('account/create'),
                'csrf'   => true,
            ]
        );

        if ($this->config->get('prevent_email_as_login')) { // require login name
            $this->data['form']['fields']['general']['loginname'] = $form->getFieldHtml(
                [
                    'type'     => 'input',
                    'name'     => 'loginname',
                    'value'    => $this->request->post['loginname'],
                    'required' => true,
                ]
            );
        }
        $this->data['form']['fields']['general']['firstname'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'firstname',
                'value'    => $this->request->post['firstname'],
                'required' => true,
            ]
        );
        $this->data['form']['fields']['general']['lastname'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'lastname',
                'value'    => $this->request->post['lastname'],
                'required' => true,
            ]
        );
        $this->data['form']['fields']['general']['email'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'email',
                'value'    => $this->request->get_or_post('email'),
                'required' => true,
            ]
        );
        $this->data['form']['fields']['general']['telephone'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'telephone',
                'value' => $this->request->post['telephone'],
            ]
        );

        //get only active IM drivers
        $im_drivers = $this->im->getIMDriverObjects();

        if ($im_drivers) {
            foreach ($im_drivers as $protocol => $driver_obj) {
                /** @var AMailIM $driver_obj */
                if (!is_object($driver_obj) || $protocol == 'email') {
                    continue;
                }
                $fld = $driver_obj->getURIField($form, $this->request->post[$protocol]);
                $this->data['form']['fields']['general'][$protocol] = $fld;
                $this->data['entry_'.$protocol] = $fld->label_text;
            }
        }

        $this->data['form']['fields']['address']['company'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'company',
                'value'    => $this->request->post['company'],
                'required' => false,
            ]
        );
        $this->data['form']['fields']['address']['address_1'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'address_1',
                'value'    => $this->request->post['address_1'],
                'required' => true,
            ]
        );
        $this->data['form']['fields']['address']['address_2'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'address_2',
                'value'    => $this->request->post['address_2'],
                'required' => false,
            ]
        );
        $this->data['form']['fields']['address']['city'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'city',
                'value'    => $this->request->post['city'],
                'required' => true,
            ]
        );

        $this->view->assign('zone_id', $this->request->post['zone_id'] ?: $this->config->get('config_zone_id'));
        $this->data['form']['fields']['address']['zone'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'zone_id',
                'required' => true,
            ]
        );

        $this->data['form']['fields']['address']['postcode'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'postcode',
                'value'    => $this->request->post['postcode'],
                'required' => true,
            ]
        );

        $this->loadModel('localisation/country');
        $countries = $this->model_localisation_country->getCountries();
        $options = [];
        if (count($countries) > 1) {
            $options = ["FALSE" => $this->language->get('text_select')];
        }
        $options = $options + array_column($countries, 'name','country_id');

        $this->data['form']['fields']['address']['country'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'country_id',
                'options'  => $options,
                'value'    => $this->request->post['country_id'] ?? $this->config->get('config_country_id'),
                'required' => true,
            ]
        );

        $this->data['form']['fields']['password']['password'] = $form->getFieldHtml(
            [
                'type'     => 'password',
                'name'     => 'password',
                'value'    => $this->request->post['password'],
                'required' => true,
            ]
        );
        $this->data['form']['fields']['password']['confirm'] = $form->getFieldHtml(
            [
                'type'     => 'password',
                'name'     => 'confirm',
                'value'    => $this->request->post['confirm'],
                'required' => true,
            ]
        );

        $this->data['form']['fields']['newsletter']['newsletter'] = $form->getFieldHtml(
            [
                'type'    => 'radio',
                'name'    => 'newsletter',
                'value'   => (!is_null($this->request->get_or_post('newsletter'))
                    ? $this->request->get_or_post('newsletter')
                    : -1),
                'options' => [
                    '1' => $this->language->get('text_yes'),
                    '0' => $this->language->get('text_no'),
                ],
            ]
        );

        //If captcha enabled, validate
        if ($this->config->get('config_account_create_captcha')) {
            if ($this->config->get('config_recaptcha_site_key')) {
                $this->data['form']['fields']['newsletter']['captcha'] = $form->getFieldHtml(
                    [
                        'type'               => 'recaptcha',
                        'name'               => 'g-recaptcha-response',
                        'recaptcha_site_key' => $this->config->get('config_recaptcha_site_key'),
                        'language_code'      => $this->language->getLanguageCode(),
                    ]
                );
            } else {
                $this->data['form']['fields']['newsletter']['captcha'] = $form->getFieldHtml(
                    [
                        'type' => 'captcha',
                        'name' => 'captcha',
                        'attr' => '',
                    ]
                );
            }
        }

        $agree = $this->request->post['agree'] ?? false;
        $this->data['form']['agree'] = $form->getFieldHtml(
            [
                'type'    => 'checkbox',
                'name'    => 'agree',
                'value'   => 1,
                'checked' => $agree,
            ]
        );

        $this->data['form']['continue'] = $form->getFieldHtml(
            [
                'type' => 'submit',
                'name' => $this->language->get('button_continue'),
            ]
        );

        $this->data['error_warning'] = $this->errors['warning'];
        $this->data['error_loginname'] = $this->errors['loginname'];
        $this->data['error_firstname'] = $this->errors['firstname'];
        $this->data['error_lastname'] = $this->errors['lastname'];
        $this->data['error_email'] = $this->errors['email'];
        $this->data['error_telephone'] = $this->errors['telephone'];
        $this->data['error_password'] = $this->errors['password'];
        $this->data['error_confirm'] = $this->errors['confirm'];
        $this->data['error_address_1'] = $this->errors['address_1'];
        $this->data['error_city'] = $this->errors['city'];
        $this->data['error_postcode'] = $this->errors['postcode'];
        $this->data['error_country'] = $this->errors['country'];
        $this->data['error_zone'] = $this->errors['zone'];
        $this->data['error_captcha'] = $this->errors['captcha'];

        $this->data['action'] = $this->html->getSecureURL('account/create');
        $this->data['newsletter'] = $this->request->post['newsletter'];

        if ($this->config->get('config_account_id')) {
            $this->loadModel('catalog/content');
            $content_info = $this->model_catalog_content->getContent($this->config->get('config_account_id'));
            if ($content_info) {
                $text_agree = $this->language->get('text_agree');
                $this->data['text_agree_href'] = $this->html->getURL(
                    'r/content/content/loadInfo', '&content_id='.$this->config->get('config_account_id')
                );
                $this->data['text_agree_href_text'] = $content_info['title'];
            } else {
                $text_agree = '';
            }
        } else {
            $text_agree = '';
        }
        $this->data['text_agree'] = $text_agree;
        $text_account_already = sprintf(
            $this->language->get('text_account_already'),
            $this->html->getSecureURL('account/login')
        );

        $this->data['text_account_already'] = $text_account_already;
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/create.tpl');
        unset($this->session->data['fc']);

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function resend()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        /** @var ModelAccountCustomer $mdl */
        $mdl = $this->loadModel('account/customer');
        $enc = new AEncryption($this->config->get('encryption_key'));
        list($customer_id, $activation_code) = explode("::", $enc->decrypt($this->request->get['rid']));
        if ($customer_id && $activation_code) {
            try {
                $mdl->emailActivateLink($customer_id);
            }catch (Exception $e) {
                $this->log->write(__CLASS__ . '::' . __FUNCTION__ . '() error: ' . $e->getMessage());
                $this->messages->saveError("Mailer Critical Error!", $e->getMessage());
            }
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        redirect($this->html->getSecureURL('account/success'));
    }
}
