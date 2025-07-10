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
 * Class ModelExtensionTaxjarDiscount
 */

class ModelExtensionTaxjarDiscount extends Model {
    /**
     * @param $product_id
     * @param $product_total
     * @param $customer_id
     * @param $products
     * @param $subtotal
     * @param $final_discount
     * @param $coupon_id
     * @return float|int
     */
	public function getDiscount($product_id,$product_total,$customer_id,$products,$subtotal,$final_discount,$coupon_id) {
	    if (isset($coupon_id) && $this->config->get('coupon_status')) {
            $coupon = $this->getCouponData($coupon_id,$customer_id,$products,$subtotal);
            if (!$coupon['product']) {
                $coupon_total = $subtotal;
            } else {
                $coupon_total = 0;

                foreach ($products as $product) {
                    if (in_array($product['product_id'], $coupon['product'])) {
                        $coupon_total += $product['total'];
                    }
                }
            }

            if ($coupon['type'] == 'F') {
                $coupon['discount'] = min($coupon['discount'], $coupon_total);
            }

            $discount = 0;

            $product_coupon=array();
            if (!$coupon['product']) {
                $status = true;
            } else {
                if (in_array($product_id, $coupon['product'])) {
                    $product_coupon[]=$product_id;
                    $status = true;
                } else {
                    $status = false;
                }
            }

            if ($status===true) {
               $discount = round($final_discount * ($product_total / $coupon_total), 2);
            }

            return $discount;
        }
    }

    /**
     * @param $coupon_id
     * @param $customer_id
     * @param $products
     * @param $subtotal
     * @return array
     */
    public function getCouponData($coupon_id,$customer_id,$products,$subtotal) {
        if (empty ($coupon_id)) {
            return array();
        }

        $status = true;
        $coupon_query = $this->db->query("SELECT *
										  FROM ".$this->db->table("coupons")." c
										  LEFT JOIN ".$this->db->table("coupon_descriptions")." cd
												ON (c.coupon_id = cd.coupon_id AND cd.language_id = '".(int)$this->config->get('storefront_language_id')."' )
										  WHERE c.coupon_id = ".$coupon_id."
												AND ((date_start = '0000-00-00' OR date_start < NOW())
												AND (date_end = '0000-00-00' OR date_end > NOW()))
												AND c.status = '1'");
        $coupon_product_data = array();
        if ($coupon_query->num_rows) {
            if ($coupon_query->row['total'] > $subtotal) {
                $status = false;
            }
            $coupon_redeem_query = $this->db->query("SELECT COUNT(*) AS total
													 FROM `".$this->db->table("orders")."`
													 WHERE order_status_id > '0' AND coupon_id = '".(int)$coupon_query->row['coupon_id']."'");

            if ($coupon_redeem_query->row['total'] >= $coupon_query->row['uses_total'] && $coupon_query->row['uses_total'] > 0) {
                $status = false;
            }
            if ($coupon_query->row['logged'] && !$customer_id) {
                $status = false;
            }

            if ($customer_id) {
                $coupon_redeem_query = $this->db->query("SELECT COUNT(*) AS total
														 FROM `".$this->db->table("orders")."`
														 WHERE order_status_id > '0'
																AND coupon_id = '".(int)$coupon_query->row['coupon_id']."'
																AND customer_id = '".(int)$customer_id."'");

                if ($coupon_redeem_query->row['total'] >= $coupon_query->row['uses_customer'] && $coupon_query->row['uses_customer'] > 0) {
                    $status = false;
                }
            }

            $coupon_product_query = $this->db->query("SELECT *
													   FROM ".$this->db->table("coupons_products")."
													   WHERE coupon_id = '".(int)$coupon_query->row['coupon_id']."'");

            foreach ($coupon_product_query->rows as $result) {
                $coupon_product_data[] = $result['product_id'];
            }

            if ($coupon_product_data) {
                $coupon_product = false;

                foreach ($products as $product) {
                    if (in_array($product['product_id'], $coupon_product_data)) {
                        $coupon_product = true;
                        break;
                    }
                }

                if (!$coupon_product) {
                    $status = false;
                }
            }
        } else {
            $status = false;
        }

        if ($status) {
            $coupon_data = array(
                'coupon_id'     => $coupon_query->row['coupon_id'],
                'code'          => $coupon_query->row['code'],
                'name'          => $coupon_query->row['name'],
                'type'          => $coupon_query->row['type'],
                'discount'      => $coupon_query->row['discount'],
                'shipping'      => $coupon_query->row['shipping'],
                'total'         => $coupon_query->row['total'],
                'product'       => $coupon_product_data,
                'date_start'    => $coupon_query->row['date_start'],
                'date_end'      => $coupon_query->row['date_end'],
                'uses_total'    => $coupon_query->row['uses_total'],
                'uses_customer' => $coupon_query->row['uses_customer'],
                'status'        => $coupon_query->row['status'],
                'date_added'    => $coupon_query->row['date_added'],
            );

            return $coupon_data;
        }
        return array();
    }

}
