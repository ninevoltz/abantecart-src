<?php
/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

/**
 * @var AController $this
 */
$this->extension_manager->deleteDependant('taxjar_integration_total', 'taxjar_integration');
$this->extension_manager->delete('taxjar_integration_total');

$this->db->query("DELETE FROM ".$this->db->table("settings")." WHERE `group`= 'taxjar_integration';");
$this->db->query("DROP TABLE IF EXISTS ".$this->db->table("taxjar_product_taxcode_values").";");
$this->db->query("DROP TABLE IF EXISTS ".$this->db->table("taxjar_customer_settings_values").";");
$this->db->query("DROP TABLE IF EXISTS ".$this->db->table("taxjar_customer_exempt").";");
$this->db->query("DROP TABLE IF EXISTS ".$this->db->table("taxjar_customer_settings_values").";");
$this->db->query("DROP TABLE IF EXISTS ".$this->db->table("taxjar_order_discount").";");
$this->db->query("DROP TABLE IF EXISTS ".$this->db->table("taxjar_order_customer").";");