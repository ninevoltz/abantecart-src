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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesCatalogProductLayout extends AController
{
    protected $error = [];

    public function main()
    {
        $page_controller = 'pages/product/product';
        $page_key_param = 'product_id';
        $productId = (int)$this->request->get['product_id'];
        $page_url = $this->html->getSecureURL('catalog/product_layout', '&product_id=' . $productId);

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/product');
        $this->loadLanguage('design/layout');
        $this->loadModel('catalog/product');

        if ($productId && $this->request->is_GET()) {
            $product_info = $this->model_catalog_product->getProduct($productId);
            if (!$product_info) {
                unset($this->session->data['success']);
                $this->session->data['warning'] = $this->language->get('error_product_not_found');
                redirect($this->html->getSecureURL('catalog/product'));
            }
        }

        $this->data['help_url'] = $this->gen_help_url('product_layout');
        $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions(
            $productId,
            $this->language->getContentLanguageID()
        );
        $this->data['heading_title'] = $this->language->get('text_design')
            . ' - '
            . $this->data['product_description']['name'];
        $this->document->setTitle($this->data['heading_title']);

        // Alert messages
        if (isset($this->session->data['warning'])) {
            $this->data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product'),
                'text'      => $this->language->get('heading_title', 'catalog/product'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $productId),
                'text'      => $this->data['heading_title'],
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $page_url,
                'text'      => $this->language->get('text_design'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );
        //active tab
        $this->data['active'] = 'layout';
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/catalog/product_tabs', [$this->data]);
        $this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

        $templateTxtId = $this->request->get['tmpl_id'] ?: $this->config->get('config_storefront_template');
        $layout = new ALayoutManager($templateTxtId);
        //get existing page layout or generic
        $page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $productId);
        $pageId = $page_layout['page_id'];
        $layoutId = $page_layout['layout_id'];


        $params = [
            'product_id' => $productId,
            'page_id'    => $pageId,
            'layout_id'  => $layoutId,
            'tmpl_id'    => $templateTxtId,
        ];
        $url = '&' . $this->html->buildURI($params);

        // get templates
        $this->data['templates'] = [];
        $directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $this->data['templates'][] = basename($directory);
        }
        $enabled_templates = $this->extensions->getExtensionsList(
            [
                'filter' => 'template',
                'status' => 1,
            ]
        );
        foreach ($enabled_templates->rows as $template) {
            $this->data['templates'][] = $template['key'];
        }

        $action = $this->html->getSecureURL('catalog/product_layout/save');
        // Layout form data
        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'layout_form',
            ]
        );

        $this->data['form_begin'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'layout_form',
                'attr'   => 'data-confirm-exit="true"',
                'action' => $action,
            ]
        );

        $this->data['hidden_fields'] = [];
        foreach ($params as $name => $value) {
            $this->data[$name] = $value;
            $this->data['hidden_fields'][] = $form->getFieldHtml(
                [
                    'type'  => 'hidden',
                    'name'  => $name,
                    'value' => $value,
                ]
            );
        }

        $this->data['page_url'] = $page_url;
        $this->data['current_url'] = $this->html->getSecureURL('catalog/product_layout', $url);

        // insert external form of layout
        $layout = new ALayoutManager($templateTxtId, $pageId, $layoutId);

        $layoutForm = $this->dispatch('common/page_layout', [$layout]);
        $this->data['block_layout_form'] = $layoutForm->dispatchGetOutput();

        //build pages and available layouts for cloning
        $this->data['pages'] = $layout->getAllPages();
        $avLayouts = ["0" => $this->language->get('text_select_copy_layout')]
            + array_column($this->data['pages'], 'layout_name','layout_id');
        unset($avLayouts[$layoutId]);

        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'cp_layout_frm',
            ]
        );

        $this->data['cp_layout_select'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'source_layout_id',
                'value'   => '',
                'options' => $avLayouts,
            ]
        );

        $this->data['cp_layout_frm'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'cp_layout_frm',
                'attr'   => 'class="aform form-inline"',
                'action' => $action,
            ]
        );
        if ($this->config->get('config_embed_status')) {
            $this->data['product_store'] = $this->model_catalog_product->getProductStores($productId);
            $btnData = getEmbedButtonsData(
                'common/do_embed/product',
                ['product_id' => $productId],
                $this->data['product_store']
            );
            $this->data['embed_url'] = $btnData['embed_url'];
            $this->data['embed_stores'] = $btnData['embed_stores'];
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_layout.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function save()
    {
        if ($this->request->is_GET() || !$this->request->post) {
            redirect($this->html->getSecureURL('catalog/product_layout'));
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $post = $this->request->post;
        $pageData = [
            'controller' => 'pages/product/product',
            'key_param'  => 'product_id',
            'key_value'  => (int)$post['product_id'],
        ];

        $this->loadLanguage('catalog/product');
        if (!$pageData['key_value']) {
            unset($this->session->data['success']);
            $this->session->data['warning'] = $this->language->get('error_product_not_found');
            redirect($this->html->getSecureURL('catalog/product/update'));
        }

        /** @var ModelCatalogProduct $mdl */
        $mdl = $this->loadModel('catalog/product');
        $productInfo = $mdl->getProductDescriptions($pageData['key_value']);
        if ($productInfo) {
            $post['layout_name'] = $this->language->get('text_product')
                . ': '
                . $productInfo[$this->language->getContentLanguageID()]['name'];
            $pageData['page_descriptions'] = $productInfo;
        }

        if (saveOrCreateLayout($post['tmpl_id'], $pageData, $post)) {
            $this->session->data['success'] = $this->language->get('text_success_layout');
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        redirect(
            $this->html->getSecureURL(
                'catalog/product_layout',
                '&product_id=' . $pageData['key_value'] . '&tmpl_id=' . $post['tmpl_id']
            )
        );
    }
}