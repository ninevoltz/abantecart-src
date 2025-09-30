<?php

/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelExtensionTaxjarIntegration extends Model {

    /* getProductTaxCode return string value taxCode of product for request tax to taxjar  */
    public function getProductTaxCode($product_id) {
        $query = $this->db->query("SELECT taxcode_value 
                                    FROM ".$this->db->table("taxjar_product_taxcode_values")." 
                                    WHERE product_id=".(int)$product_id." 
                                    LIMIT 1");
        return $query->row['taxcode_value'];
    }

    public function setOrderProductTaxCode($order_product_id, $value) {
        $this->db->query("UPDATE ".$this->db->table("order_products")." 
                        SET taxcode='".$value."' 
                        WHERE order_product_id=".(int)$order_product_id);
    }

    public function getCustomerSettings($customer_id) {
        $query = $this->db->query("SELECT * 
                                    FROM ".$this->db->table("taxjar_customer_settings_values")." 
                                    WHERE customer_id=".(int)$customer_id." 
                                    LIMIT 1");
        return $query->row;
    }

    /**
     * @param $customer_id
     * @param array $data
     * @return bool
     */
    public function setCustomerSettings($customer_id, $data = array()) {
        $sql = "SELECT * 
                FROM ".$this->db->table("taxjar_customer_settings_values")." 
                WHERE customer_id=".(int)$customer_id;
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            $sql = "UPDATE ".$this->db->table("taxjar_customer_settings_values")." 
                    SET exemption_number = '".$this->db->escape($data['exemption_number'])."',
                        exempt_group  = '".$this->db->escape($data['exempt_group'])."',
                        status = 0
                    WHERE customer_id=".(int)$customer_id;
        } else {
            $sql = "INSERT INTO ".$this->db->table("taxjar_customer_settings_values")." 
                    SET customer_id=".(int)$customer_id.",
                        exemption_number = '".$this->db->escape($data['exemption_number'])."',
                        exempt_group  = '".$this->db->escape($data['exempt_group'])."',
                        status = 0";
        }
        $this->db->query($sql);
        return true;
    }

    /**
     * @param $group_id
     * @return mixed
     */
    public function getTaxExemptByGroupId($group_id) {
        $sql="SELECT tax_exempt FROM " . $this->db->table('customer_groups') ." WHERE customer_group_id=".(int)$group_id;
        $query=$this->db->query($sql);
        $results=$query->row;
        return $results['tax_exempt'];
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getTaxJarCustomer($email) {
        $sql="SELECT taxjar_customer_id,synced FROM ". $this->db->table("taxjar_order_customer")." WHERE `email`='".$this->db->escape($email)."'";
        $query = $this->db->query($sql);
        return $query->row;
    }

	/**
	 * @param $email
	 *
	 * @return int
	 * @throws Exception
	 */
    public function addTaxJarCustomer($email) {
	    $date= new DateTime();
	    $timestamp = $date->getTimestamp();
	    $sql="INSERT INTO ". $this->db->table("taxjar_order_customer")." SET 
              `taxjar_customer_id`=".$timestamp.",   
              `email`='".$this->db->escape($email)."',
              `synced`=0";
	    $this->db->query($sql);
	    return $timestamp;
    }

    /**
     * @param $data
     */
    public function updateTaxJarCustomerSync($data) {
        $sql="UPDATE ". $this->db->table("taxjar_order_customer")." SET `synced`=".(int)$data['synced']." 
              WHERE `email`='".$this->db->escape($data['email'])."'";
        $this->db->query($sql);
    }

    /**
     * @param $order_id
     * @param $product_id
     * @param $discount
     */
    public function addProductDiscount($order_id,$product_id,$discount) {
        $this->db->query("DELETE FROM ".$this->db->table('taxjar_order_discount')." WHERE `order_id`=".(int)$order_id." AND `product_id`=".(int)$product_id);
        $sql="INSERT INTO ".$this->db->table('taxjar_order_discount')." SET
             `order_id`=".$order_id.",`product_id`=".$product_id.",`discount`=".$discount;
        $this->db->query($sql);
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function getOrderTotals($order_id) {
        $sql="SELECT * FROM ".$this->db->table("order_totals")." WHERE order_id=".(int)$order_id;
        $query=$this->db->query($sql);
        return $query->rows;
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function getOrderProducts($order_id) {
        $sql="SELECT * FROM ".$this->db->table("order_products")." WHERE order_id=".(int)$order_id;
        $query=$this->db->query($sql);
        return $query->rows;
    }

    /**
     * @param $order_id
     * @param $product_id
     * @return mixed
     */
    public function getProductDiscount($order_id,$product_id) {
        $sql="SELECT `discount` FROM ".$this->db->table('taxjar_order_discount')." WHERE `order_id`=".(int)$order_id." 
              AND `product_id`=".(int)$product_id;
        $query=$this->db->query($sql);
        $result=$query->row;
        return $result['discount'];
    }

    /**
     * @param $discount
     * @param $order_id
     */
    public function updateProductDiscount($discount, $order_id) {
        $sql="UPDATE ".$this->db->table('taxjar_order_discount')." SET discount=".$discount." WHERE `order_id`=".(int)$order_id;
        $this->db->query($sql);
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function getProductDiscountByOrderId($order_id) {
        $sql = "SELECT COUNT(*) as total FROM ".$this->db->table('taxjar_order_discount')." WHERE `order_id`=".(int)$order_id;
        $query = $this->db->query($sql);
        return (int)$query->row['total'];
    }

    /**
     * @param $order_id
     */
    public function deleteProductDiscountByOrderId($order_id) {
        $this->db->query("DELETE FROM ".$this->db->table('taxjar_order_discount')." WHERE `order_id`=".(int)$order_id);
    }
}