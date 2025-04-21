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

class ControllerBlocksCategorySlides extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $request = $this->request->get;
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $this->loadModel('tool/seo_url');

        if (isset($request['path'])) {
            $path = '';
            $parts = explode('_', $request['path']);
            if (count($parts) == 1) {
                //see if this is a category ID to sub category, need to build full path
                $parts = explode('_', $mdl->buildPath($request['path']));
            }
            foreach ($parts as $path_id) {
                $category_info = $mdl->getCategory($path_id);
                if ($category_info) {
                    if (!$path) {
                        $path = $path_id;
                    } else {
                        $path .= '_' . $path_id;
                    }
                }
            }
            $categoryId = array_pop($parts);
        } else {
            $categoryId = !is_array($this->request->get['category_id'])
                ? (int)$this->request->get['category_id']
                : (int)current($this->request->get['category_id']);
        }
        $resources = [];
        if ($categoryId) {
            $AResource = new AResource('image');
            $resources = $AResource->getResourceAllObjects(
                'categories',
                $categoryId,
                [
                    'main' => [
                        'height' => 350,
                        'width'  => 320
                    ]
                ]
            );
        }
        //remove first resource
        unset($resources[0]);

        $resources = array_values($resources);
        $this->view->assign('resources', $resources);
        $this->processTemplate();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}