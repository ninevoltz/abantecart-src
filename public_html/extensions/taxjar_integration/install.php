<?php
/*------------------------------------------------------------------------------

  For Abante Cart, E-commerce Solution
  http://www.AbanteCart.com

  Copyright (c) 2014-2019 We Hear You 2, Inc.  (WHY2)

------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

//add taxjar_integration total
/**
 * @var AController $this
 */
$child_extension_id = $this->extension_manager->add(
    array(
        'type'     => 'total',
        'key'      => 'taxjar_integration_total',
        'status'   => 1,
        'priority' => 10,
        'version'  => '1.0',
    )
);
// edit settings
$this->load->model('setting/setting');
//insert taxjar_integration_total before total
$sort = $this->config->get('total_sort_order');
$calc = $this->config->get('total_calculation_order');
$this->model_setting_setting->editSetting('total',
    array('total_sort_order' => ($sort + 1), 'total_calculation_order' => ($calc + 1)));
$this->model_setting_setting->editSetting(
    'taxjar_integration_total',
    array(
        'taxjar_integration_total_status'            => 0,
        'taxjar_integration_total_sort_order'        => $sort,
        'taxjar_integration_total_calculation_order' => $calc,
        'taxjar_integration_total_total_type'        => 'taxjar_integration',
    )
);

$this->extension_manager->addDependant('taxjar_integration_total', 'taxjar_integration');

$check= "SHOW columns from ".$this->db->table('order_products')." where field='taxcode'";
$q=$this->db->query($check);
$exist=count($q->rows);
if ($exist==0) {
    $sql = "ALTER TABLE ".$this->db->table('order_products')." ADD `taxcode` VARCHAR(255) DEFAULT ''";
    $this->db->query($sql);
}

$sql1="CREATE TABLE IF NOT EXISTS ".$this->db->table("taxjar_product_taxcode_values")." (
`id` int(11) NOT NULL AUTO_INCREMENT,
	`product_id` int(11) NOT NULL,
	`taxcode_value` char(255),
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$this->db->query($sql1);

$sql2="CREATE TABLE IF NOT EXISTS ".$this->db->table("taxjar_customer_settings_values")." (
`id` int(11) NOT NULL AUTO_INCREMENT,
	`customer_id` int(11) NOT NULL,
	`status` int(1) NOT NULL,
	`exemption_number` char(255),
	`exempt_group` char(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$this->db->query($sql2);

$sql3="CREATE TABLE IF NOT EXISTS ".$this->db->table("taxjar_customer_exempt")." (
`id` int(11) NOT NULL AUTO_INCREMENT,
	`key` char(255) NOT NULL,
	`value` char(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$this->db->query($sql3);

$sql4="CREATE TABLE IF NOT EXISTS ".$this->db->table("taxjar_customer_settings_values")." (
`id` int(11) NOT NULL AUTO_INCREMENT,
	`customer_id` int(11) NOT NULL,
		`status` int(1) NOT NULL,
		`exemption_number` char(255),
	`exempt_group` char(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$this->db->query($sql4);

$sql5="CREATE TABLE IF NOT EXISTS ".$this->db->table("taxjar_order_discount")."(
    `id` int(11) NOT NULL AUTO_INCREMENT,
	`order_id` int(11),
	`product_id` int(11),
	`discount` DECIMAL (12,2),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$this->db->query($sql5);

$sql6="CREATE TABLE IF NOT EXISTS ".$this->db->table("taxjar_order_customer")." (
	`customer_id` int(11) NOT NULL AUTO_INCREMENT,
	`taxjar_customer_id` varchar(32),
	`email` CHAR(32),
	`synced` int(1) DEFAULT 0,
PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$this->db->query($sql6);

$check= "SHOW columns from ".$this->db->table('taxjar_order_customer')." where field='taxjar_customer_id'";
$q=$this->db->query($check);
$exist=count($q->rows);
if ($exist==0) {
	$sql = "ALTER TABLE ".$this->db->table('taxjar_order_customer')." ADD `taxjar_customer_id` varchar(32) after `customer_id`";
	$this->db->query($sql);
}

$sql7="CREATE TABLE IF NOT EXISTS ".$this->db->table("taxjar_nexus")." (
`nexus_id` int(11) NOT NULL AUTO_INCREMENT,
	`country_code` CHAR(3),
	`country` VARCHAR(256),
	`region_code` CHAR(2),
	`region` VARCHAR(256),
PRIMARY KEY (`nexus_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$this->db->query($sql7);

$this->db->query("INSERT INTO ".$this->db->table("taxjar_customer_exempt")." (`key`,`value`) VALUES ('wholesale','Whole Sale');");
$this->db->query("INSERT INTO ".$this->db->table("taxjar_customer_exempt")." (`key`,`value`) VALUES ('government','Government');");
$this->db->query("INSERT INTO ".$this->db->table("taxjar_customer_exempt")." (`key`,`value`) VALUES ('other','Other');");
$this->db->query("INSERT INTO ".$this->db->table("taxjar_customer_exempt")." (`key`,`value`) VALUES ('non_exempt','Non Exempt');");