<?php

/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelTotalTaxjarIntegrationTotal extends Model {
    
    public function getTotal(&$total_data, &$total, &$taxes, &$cust_data) {

        if (!$this->config->get('taxjar_integration_status')
            || !$this->config->get('taxjar_integration_total_status')) {
            return null;
        }

        if ($this->request->get_or_post('order_id')) {
            $cust_data['order_id'] = $this->request->get_or_post('order_id');
        }

        $taxjarExtension = new ExtensiontaxjarIntegration();
        $enable_calculation = $this->config->get('taxjar_integration_sales_tax_calculation');
        if ($enable_calculation==='1') {
            $tax_amount = $taxjarExtension->calculateTax($this, $cust_data, false, $total_data);
			//if ((float)$tax_amount !== 0.00) {
                $total_data[] = array(
                    'id' => 'taxjar_integration_total',
                    'title' => $this->config->get('taxjar_integration_tax_name'),
                    'text' => $this->currency->format($tax_amount, $cust_data['currency']),
                    'value' => $tax_amount,
                    'sort_order' => $this->config->get('taxjar_integration_total_sort_order'),
                    'total_type' => $this->config->get('taxjar_integration_total_total_type'),
                );
                $total += $tax_amount;
            //}
        } else {
            $total += 0.00;
        }
    }
}
