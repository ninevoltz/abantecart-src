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

class ControllerPagesCatalogProductRelations extends AController
{
    public $error = [];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $productId = (int)$this->request->get['product_id'];
        $languageId = $this->language->getContentLanguageID();

        $this->loadLanguage('catalog/product');
        $this->loadModel('catalog/product');

        if ($productId && ($this->request->is_GET())) {
            $product_info = $this->model_catalog_product->getProduct($productId);
            if (!$product_info) {
                $this->session->data['warning'] = $this->language->get('error_product_not_found');
                redirect($this->html->getSecureURL('catalog/product'));
            }
        }

        if ($this->request->is_POST()) {
            //cast for model
            foreach (['product_store', 'product_related', 'product_category'] as $n) {
                $this->request->post[$n] = (array)$this->request->post[$n];
            }
            $this->model_catalog_product->updateProductLinks($productId, $this->request->post);
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('catalog/product_relations', '&product_id=' . $productId));
        }

        $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions(
            $productId,
            $languageId
        );

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
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
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $title = $this->language->get('text_edit') . '&nbsp;'
            . $this->language->get('text_product') . ' - '
            . $this->data['product_description']['name'];
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $productId),
                'text'      => $title,
                'separator' => ' :: ',
            ]
        );
        $this->document->setTitle($title);
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/product_relations', '&product_id=' . $productId),
                'text'      => $this->language->get('tab_relations'),
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->loadModel('setting/store');
        $this->loadModel('catalog/category');
        $this->data['categories'] = [];

        $products_stores = array_column($this->model_setting_store->getStores(), 'store_id');

        $results = $this->model_catalog_category->getCategories(ROOT_CATEGORY_ID, $products_stores);

        foreach ($results as $r) {
            $this->data['categories'][$r['category_id']] = $r['name']
                . (count($products_stores) > 1 ? "   (" . $r['store_name'] . ")" : '');
        }

        $this->loadModel('setting/store');
        $this->data['stores'] = [0 => $this->language->get('text_default')];
        $results = $this->model_setting_store->getStores();
        foreach ($results as $r) {
            $this->data['stores'][$r['store_id']] = $r['name'];
        }

        $this->data['product_category'] = $this->model_catalog_product->getProductCategories($productId);
        $this->data['product_store'] = $this->model_catalog_product->getProductStores($productId);
        $this->data['product_related'] = $this->model_catalog_product->getProductRelated($productId);

        $this->data['active'] = 'relations';
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/catalog/product_tabs', [$this->data]);
        $this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $this->data['category_products'] = $this->html->getSecureURL('product/product/category');
        $this->data['related_products'] = $this->html->getSecureURL('product/product/related');
        $this->data['action'] = $this->html->getSecureURL('catalog/product_relations', '&product_id=' . $productId);
        $this->data['form_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product');
        $this->data['update'] = $this->html->getSecureURL(
            'listing_grid/product/update_relations_field',
            '&id=' . $productId
        );
        $form = new AForm('HS');

        $form->setForm(
            [
                'form_name' => 'productFrm',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'productFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'productFrm',
                'action' => $this->data['action'],
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'href'  => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $productId),
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );
        $this->data['cancel'] = $this->html->getSecureURL('catalog/product');

        $this->data['form']['fields']['category'] = $form->getFieldHtml(
            [
                'type'        => 'checkboxgroup',
                'name'        => 'product_category[]',
                'value'       => $this->data['product_category'],
                'options'     => $this->data['categories'],
                'style'       => 'chosen',
                'placeholder' => $this->language->get('text_select_category'),
            ]
        );
        //load only prior saved products
        $this->data['products'] = [];
        if (count($this->data['product_related'])) {
            $ids = array_map('intval', array_unique($this->data['product_related']));

            $this->loadModel('catalog/product');
            if ($ids) {
                $filter = ['subsql_filter' => 'p.product_id in (' . implode(',', $ids) . ')'];
                $results = $this->model_catalog_product->getProducts($filter);
            } else {
                $results = [];
            }

            $product_ids = array_column($results, 'product_id');

            //get thumbnails by one pass
            $resource = new AResource('image');
            $thumbnails = $product_ids
                ? $resource->getMainThumbList(
                    'products',
                    $product_ids,
                    $this->config->get('config_image_grid_width'),
                    $this->config->get('config_image_grid_height')
                )
                : [];

            foreach ($results as $r) {
                $this->data['products'][$r['product_id']] =
                    [
                        'name'  => $r['name'] . " (" . $r['model'] . ")",
                        'image' => $thumbnails[$r['product_id']]['thumb_html'],
                        'url'   => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $r['product_id'])
                    ];
            }
        }

        $this->data['form']['fields']['related'] = $form->getFieldHtml(
            [
                'type'        => 'multiselectbox',
                'name'        => 'product_related[]',
                'value'       => $this->data['product_related'],
                'options'     => $this->data['products'],
                'style'       => 'chosen',
                'ajax_url'    => $this->html->getSecureURL('r/product/product/products', '&exclude[]=' . $productId),
                'placeholder' => $this->language->get('text_select_from_lookup'),
            ]
        );

        $this->data['form']['fields']['store'] = $form->getFieldHtml(
            [
                'type'    => 'checkboxgroup',
                'name'    => 'product_store[]',
                'value'   => $this->data['product_store'],
                'options' => $this->data['stores'],
                'style'   => 'chosen',
            ]
        );
        if ($this->config->get('config_embed_status')) {
            $btnData = getEmbedButtonsData(
                'common/do_embed/product',
                ['product_id' => $productId],
                $this->data['product_store']
            );
            $this->data['embed_url'] = $btnData['embed_url'];
            $this->data['embed_stores'] = $btnData['embed_stores'];
        }
        $this->addChild(
            'pages/catalog/product_summary',
            'summary_form',
            'pages/catalog/product_summary.tpl'
        );
        $this->view->assign('help_url', $this->gen_help_url('product_relations'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_relations.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
