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
 * Class ModelExtensionTaxjarIntegration
 *
 * @property ModelSettingStore $model_setting_store
 * @property ModelSaleCustomer $model_sale_customer
 */
class ModelExtensionTaxjarIntegration extends Model {

    public $data=array();

    /* getProductTaxCode return string value taxCode of product for request tax to taxjar  */
    public function getProductTaxCode($product_id) {
        $query = $this->db->query("SELECT taxcode_value 
									FROM ".$this->db->table("taxjar_product_taxcode_values")." 
									WHERE product_id=".(int)$product_id." 
									LIMIT 1");
        if ($query->num_rows) {
            return $query->row['taxcode_value'];
        }
        return "";
    }

    /**
     * @param $product_id
     * @param $value
     * @return bool
     */
    public function setProductTaxCode($product_id, $value) {
        if (strlen(trim($value)) == 0) {
            $this->deleteProductTaxCode($product_id);
        } else {
            $query = $this->db->query("SELECT taxcode_value 
										FROM ".$this->db->table("taxjar_product_taxcode_values")." 
										WHERE product_id=".(int)$product_id." LIMIT 1");
            if ($query->num_rows) {
                $this->db->query("UPDATE ".$this->db->table("taxjar_product_taxcode_values")." 
									SET taxcode_value='".$this->db->escape($value)."' 
									WHERE product_id=".(int)$product_id);
                return true;
            } else {
                $this->db->query("INSERT INTO ".$this->db->table("taxjar_product_taxcode_values")." 
									(product_id, taxcode_value) 
									VALUES (".(int)$product_id.",'".$this->db->escape($value)."')");
                return true;
            }
        }
    }

    /**
     * @param $product_id
     */
    public function deleteProductTaxCode($product_id) {
        $this->db->query("DELETE FROM ".$this->db->table("taxjar_product_taxcode_values")." 
							WHERE product_id=".(int)$product_id);
    }

    /**
     * @param $customer_id
     * @return array
     */
    public function getCustomerSettings($customer_id) {
        $query = $this->db->query("SELECT * 
									FROM ".$this->db->table("taxjar_customer_settings_values")." 
									WHERE customer_id=".(int)$customer_id." 
									LIMIT 1");
        if ($query->num_rows) {
            return $query->row;
        }
        return array();
    }

    /**
     * @param $customer_id
     * @param $data
     * @return bool
     * @throws AException
     */
    public function setCustomerSettings($customer_id, $data) {
        $query = $this->db->query("SELECT * 
									FROM ".$this->db->table("taxjar_customer_settings_values")." 
									WHERE customer_id=".(int)$customer_id." 
									LIMIT 1");
        if ($query->num_rows == 1) {
            $set_string = "";
            if (isset($data['status'])) {
                $set_string .= " `status` = ".(int)$data['status'];
            }
            if (isset($data['exemption_number'])) {
                if ($set_string == "") {
                    $set_string .= "`exemption_number` = '".$this->db->escape($data['exemption_number'])."'";
                } else {
                    $set_string .= ",`exemption_number` = '".$this->db->escape($data['exemption_number'])."'";
                }
            }
            if (isset($data['exempt_group'])) {
                if ($set_string == "") {
                    $set_string .= "`exempt_group`='".$this->db->escape($data['exempt_group'])."'";
                } else {
                    $set_string .= ",`exempt_group`='".$this->db->escape($data['exempt_group'])."'";
                }
            }

            $this->db->query("UPDATE ".$this->db->table("taxjar_customer_settings_values")." 
							SET ".$set_string."  
							WHERE customer_id=".(int)$customer_id);
            //send email when declined

            if ($data['status'] === '2' && $query->row['status'] !== '2') {
                $this->load->model('sale/customer');
                $customer_info = $this->model_sale_customer->getCustomer($customer_id);
                if ($customer_info) {
                    $this->load->language('taxjar_integration/taxjar_integration');
                    $this->load->model('setting/store');
                    $store_info = $this->model_setting_store->getStore($customer_info['store_id']);
                    $mail = new AMail($this->config);
                    $mail->setTo($customer_info['email']);
                    $mail->setFrom($this->config->get('store_main_email'));
                    $mail->setSender($store_info['store_name']);
                    $mail->setSubject(sprintf($this->language->get('taxjar_integration_subject'), $store_info['store_name']));
                    $mail_text = sprintf($this->language->get('taxjar_integration_mail_text'), $store_info['config_url'].'index.php?rt=account/edit');
                    $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
                    $this->data['mail_template_data']['store_url'] = $this->config->get('config_url');
                    $this->data['mail_template_data']['message'] = $this->language->get('taxjar_integration_mail_text');
                    $url=$store_info['config_url'].'index.php?rt=account/edit';
                    $this->data['mail_template_data']['edit_url'] = '<a href="'.$url.'" target="_BLANK">'.$url.'</a>';
                    $config_mail_logo = $this->config->get('config_mail_logo');
                    $config_mail_logo = !$config_mail_logo ? $this->config->get('config_logo') : $config_mail_logo;
                    if ($config_mail_logo) {
                        if (is_numeric($config_mail_logo)) {
                            $r = new AResource('image');
                            $resource_info = $r->getResource($config_mail_logo);
                            if ($resource_info) {
                                $this->data['mail_template_data']['logo_html'] = html_entity_decode(
                                    $resource_info['resource_code'],
                                    ENT_QUOTES, 'UTF-8'
                                );
                            }
                        } else {
                            $this->data['mail_template_data']['logo_uri'] = 'cid:'
                                .md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
                                .'.'.pathinfo($config_mail_logo, PATHINFO_EXTENSION);
                        }
                    }

                    $this->data['mail_template'] = 'email/declined.tpl';
                    $view = new AView($this->registry, 0);
                    $view->batchAssign($this->data['mail_template_data']);
                    $html_body = $view->fetch($this->data['mail_template']);
                    $mail->setHtml($html_body);
                    if (is_file(DIR_RESOURCE.$config_mail_logo)) {
                        $mail->addAttachment(DIR_RESOURCE.$config_mail_logo,
                            md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
                            .'.'.pathinfo($config_mail_logo, PATHINFO_EXTENSION));
                    }
                    $this->extensions->hk_UpdateData($this, __FUNCTION__);
                    $mail->send();
                }
            }

        } else {
            if (!isset($data['status'])) {
                $data['status'] = (int)$data['status'];
            }
            if (!isset($data['exemption_number'])) {
                $data['exemption_number'] = "";
            }
            if (!isset($data['exempt_group'])) {
                $data['exempt_group'] = "";
            }
            $this->db->query("INSERT INTO ".$this->db->table("taxjar_customer_settings_values")." 
							(customer_id,`status`,`exemption_number`,`exempt_group`)  
							VALUES (".(int)$customer_id.",
									".(int)$data['status'].",
									'".$this->db->escape($data['exemption_number'])."',
									'".$this->db->escape($data['exempt_group'])."')");

        }

        return true;
    }

    /**
     * @param $customer_id
     * @return mixed
     */
    public function getCustomerGroupByCustomerId($customer_id) {
        $sql = "SELECT cg.customer_group_id FROM ".$this->db->table("customers")." c
					LEFT JOIN ".$this->db->table("customer_groups")." cg on c.customer_group_id = cg.customer_group_id
					WHERE customer_id = '".(int)$customer_id."' 
					AND status = '1'";
        $query = $this->db->query($sql);
        $result = $query->row;
        return $result['customer_group_id'];
    }

    /**
     * @param $group_id
     * @return int
     */
    public function getTaxExemptByGroupId($group_id) {
        $sql="SELECT tax_exempt FROM " . $this->db->table('customer_groups') ." WHERE customer_group_id=".(int)$group_id;
        $query=$this->db->query($sql);
        $results=$query->row;
        return (int)$results['tax_exempt'];
    }

    /**
     * @return mixed
     */
    public function getExemptGroups() {
        $sql="SELECT `key`,`value` from " . $this->db->table('taxjar_customer_exempt');
        $query=$this->db->query($sql);
        return $query->rows;
    }

    /**
     * @param $customer_id
     * @return int
     */
    public function getTaxExemptByCustomerId($customer_id) {
        $sql="SELECT cg.tax_exempt FROM " . $this->db->table('customer_groups') ." cg 
              LEFT JOIN " . $this->db->table('customers') ." c ON c.customer_group_id=cg.customer_group_id
              WHERE c.customer_id=".(int)$customer_id;
        $query=$this->db->query($sql);
        $results=$query->row;
        return (int)$results['tax_exempt'];
    }

    /**
     * @param $customer_id
     * @return mixed
     */
    public function getDefaultAddressByCustomerId($customer_id) {
        $sql="SELECT addr.firstname,addr.lastname,addr.address_1 as address,addr.postcode,addr.city,st.code as state,
                cn.iso_code_2 as country FROM " . $this->db->table('customers'). " c 
                LEFT JOIN " .$this->db->table('addresses'). " addr on addr.address_id=c.address_id
                LEFT JOIN " .$this->db->table('countries') ." cn on cn.country_id=addr.country_id
                LEFT JOIN " .$this->db->table('zones') ." st on st.zone_id=addr.zone_id
                WHERE c.customer_id=".(int)$customer_id;
        $query=$this->db->query($sql);
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
     * @param $email
     * @return mixed
     */
    public function getTaxJarCustomer($email) {
        $sql="SELECT taxjar_customer_id,synced FROM ". $this->db->table("taxjar_order_customer")." WHERE `email`='".$this->db->escape($email)."'";
        $query = $this->db->query($sql);
        $result = $query->row;
        return $result;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function taxjarCustomerExist($email) {
        $sql="SELECT COUNT(customer_id) as total FROM". $this->db->table("taxjar_order_customer")." WHERE `email`='".$this->db->escape($email)."'";
        $query = $this->db->query($sql);
        $result = $query->row;
        return (int)$result['total'];
    }

    /**
     * @param $order_id
     * @param $product_id
     * @param $discount
     */
    public function addProductDiscount($order_id,$product_id,$discount) {
        $this->db->query("DELETE FROM ".$this->db->table('taxjar_order_discount')." 
                          WHERE `order_id`=".$order_id." AND `product_id`=".$product_id);
        $sql="INSERT INTO ".$this->db->table('taxjar_order_discount')." SET
             `order_id`=".$order_id.",`product_id`=".$product_id.",`discount`=".$discount;
        $this->db->query($sql);
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
     * @param $taxcode
     * @param $name
     */
    public function addCategoryTaxCodes($taxcode,$name) {
      $this->db->query("DELETE FROM ".$this->db->table("taxjar_category_taxcodes")." WHERE `taxcode`='".$taxcode."'");
      $sql="INSERT INTO ".$this->db->table("taxjar_category_taxcodes")."
            SET `taxcode`='".$taxcode."',`name`='".$name."' 
            ON DUPLICATE KEY UPDATE `taxcode`=VALUES(`taxcode`),`name`=VALUES(`name`)";
      $this->db->query($sql);
    }

    /**
     * @return mixed
     */
    public function getCategoryTaxCodes() {
        $sql="SELECT `taxcode`,`name` FROM ".$this->db->table("taxjar_category_taxcodes");
        $query=$this->db->query($sql);
        return $query->rows;
    }
}