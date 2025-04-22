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

class ModelTotalShipping extends Model
{
    public function getTotal(&$totalList, &$cost, &$taxes, &$customerData)
    {
        $shippingData = $customerData['shipping_method'];
        if (!isset($shippingData['cost'])) {
            return;
        }
        if ($this->cart->hasShipping() && isset($shippingData) && $this->config->get('shipping_status')) {
            $totalList[] = [
                'id'         => 'shipping',
                'title'      => $shippingData['title'] . ':',
                'text'       => $this->currency->format($shippingData['cost']),
                'value'      => $shippingData['cost'],
                'sort_order' => $this->config->get('shipping_sort_order'),
                'total_type' => $this->config->get('shipping_total_type'),
            ];

            if ($shippingData['tax_class_id']) {
                if (!isset($taxes[$shippingData['tax_class_id']])) {
                    $taxes[$shippingData['tax_class_id']]['total'] = $shippingData['cost'];
                    $taxes[$shippingData['tax_class_id']]['tax'] = $this->tax->calcTotalTaxAmount(
                        $shippingData['cost'],
                        $shippingData['tax_class_id']
                    );
                } else {
                    $taxes[$shippingData['tax_class_id']]['total'] += $shippingData['cost'];
                    $taxes[$shippingData['tax_class_id']]['tax'] += $this->tax->calcTotalTaxAmount(
                        $shippingData['cost'],
                        $shippingData['tax_class_id']
                    );
                }
            }

            $cost += (float)$shippingData['cost'];
        }
    }
}