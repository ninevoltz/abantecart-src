alter table `ac_addresses` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify company varchar(32) not null,
    modify firstname varchar(32) default '' not null,
    modify lastname varchar(32) default '' not null,
    modify address_1 varchar(128) not null,
    modify address_2 varchar(128) not null,
    modify postcode varchar(10) default '' not null,
    modify city varchar(128) not null;

alter table `ac_ant_messages` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify id varchar(60) not null,
    modify title varchar(255) null,
    modify description text null,
    modify html longtext null,
    modify url text null,
    modify language_code varchar(2) default 'en' not null;

alter table `ac_banner_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) not null comment 'translatable',
    modify description longtext not null comment 'translatable',
    modify meta text default '' null comment 'translatable';

alter table `ac_banner_stat` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify user_info text default '' null;

alter table `ac_banners` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify banner_group_name varchar(255) default '' not null,
    modify target_url text default '' null;

alter table `ac_block_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify block_wrapper varchar(255) default '0' not null,
    modify name varchar(255) not null comment 'translatable',
    modify title varchar(255) not null comment 'translatable',
    modify description varchar(255) default '' not null comment 'translatable',
    modify content longtext default '' not null;

alter table `ac_block_layouts` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_block_templates` engine=InnoDB collate=utf8mb4_unicode_ci,
     modify template varchar(255) not null;

alter table `ac_blocks` engine=InnoDB  collate=utf8mb4_unicode_ci,
    modify block_txt_id varchar(255) not null,
    modify controller varchar(255) not null;

alter table `ac_categories` engine=InnoDB  collate=utf8mb4_unicode_ci;
alter table `ac_categories_to_stores` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_category_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) default '' not null comment 'translatable',
    modify meta_keywords varchar(255) not null comment 'translatable',
    modify meta_description varchar(255) not null comment 'translatable',
    modify description text not null comment 'translatable';

alter table `ac_collection_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_collections` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_content_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify title varchar(255) not null comment 'translatable',
    modify description varchar(255) default '' not null comment 'translatable',
    modify meta_keywords varchar(255) not null comment 'translatable',
    modify meta_description varchar(255) not null comment 'translatable',
    modify content longtext not null comment 'translatable';

alter table `ac_content_tags` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify tag varchar(32) not null comment 'translatable';

alter table `ac_contents` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify author varchar(128) default '' not null;

alter table `ac_contents_to_stores` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_countries` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify iso_code_2 varchar(2) default '' not null,
    modify iso_code_3 varchar(3) default '' not null,
    modify address_format text not null;

alter table `ac_country_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(128) not null comment 'translatable';

alter table `ac_coupon_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(128) not null comment 'translatable',
    modify description text not null comment 'translatable';

alter table `ac_coupons` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify code varchar(10) not null,
    modify type char not null,
    modify uses_customer varchar(11) not null,
    modify condition_rule enum ('OR', 'AND') default 'OR' not null;

alter table `ac_coupons_categories` engine=InnoDB;
alter table `ac_coupons_products` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_currencies` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify title varchar(32) default '' not null,
    modify code varchar(3) default '' not null,
    modify symbol_left varchar(12) not null,
    modify symbol_right varchar(12) not null,
    modify decimal_place char not null;

alter table `ac_custom_blocks` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_custom_lists` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify data_type varchar(70) not null;

alter table `ac_customer_groups` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(32) not null;

alter table `ac_customer_notifications` engine=InnoDB charset = utf8mb4,
    modify sendpoint varchar(255) not null,
    modify protocol varchar(30) not null;

alter table `ac_customer_sessions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify session_id varchar(128) default '' not null,
    modify ip varchar(50) default '' not null;

alter table `ac_customer_transactions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify transaction_type varchar(255) default '' not null comment 'text type of transaction',
    modify comment text null comment 'comment for internal use',
    modify description text null comment 'text for customer';

alter table `ac_customers` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify firstname varchar(32) default '' not null,
    modify lastname varchar(32) default '' not null,
    modify loginname varchar(96) default '' not null,
    modify email varchar(96) default '' not null,
    modify telephone varchar(32) default '' not null,
    modify fax varchar(32) default '' not null,
    modify sms varchar(32) default '' not null comment 'mobile phone number',
    modify salt varchar(8) default '' not null,
    modify password varchar(40) default '' not null,
    modify cart longtext null,
    modify wishlist longtext null,
    modify ip varchar(50) default '0' not null,
    modify data text null;

alter table `ac_dataset_column_properties` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify dataset_column_property_name varchar(255) not null,
    modify dataset_column_property_value varchar(255) null;

alter table `ac_dataset_definition` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify dataset_column_name varchar(255) not null,
    modify dataset_column_type varchar(100) not null;

alter table `ac_dataset_properties` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify dataset_property_name varchar(255) not null,
    modify dataset_property_value varchar(255) null;

alter table `ac_dataset_values` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify value_varchar varchar(255) null,
    modify value_text text null;

alter table `ac_datasets`
    modify dataset_name varchar(255) charset utf8mb3 not null,
    modify dataset_key varchar(255) charset utf8mb3 default '' null,
    engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_download_attribute_values` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify attribute_value_ids text null;

alter table `ac_download_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(64) default '' not null comment 'translatable';

alter table `ac_downloads`
    modify filename varchar(128) default '' not null,
    modify mask varchar(128) default '' not null,
    modify activate varchar(64) not null,
    modify activate_order_status_id varchar(255) default '' not null,
    engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_email_templates` engine=InnoDB  collate=utf8mb4_unicode_ci;

alter table `ac_encryption_keys` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify key_name varchar(32) default '' not null,
    modify comment text not null;

alter table `ac_extension_dependencies` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_extensions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify type varchar(32) not null,
    modify `key` varchar(32) not null,
    modify category varchar(32) not null,
    modify version varchar(32) null,
    modify license_key varchar(32) null,
    modify mp_product_url varchar(255) default '' null;

alter table `ac_field_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) not null comment 'translatable',
    modify description varchar(255) default '' not null comment 'translatable',
    modify error_text varchar(255) default '' not null comment 'translatable';

alter table `ac_field_values` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify value text default '' not null comment 'translatable';

alter table `ac_fields` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify field_name varchar(40) not null,
    modify element_type char default 'I' not null,
    modify attributes varchar(255) not null,
    modify settings text default '' not null,
    modify required char default 'N' not null,
    modify regexp_pattern varchar(255) default '' not null;

alter table `ac_fields_group_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) not null comment 'translatable',
    modify description varchar(255) default '' not null comment 'translatable';

alter table `ac_fields_groups` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_fields_history` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify table_name varchar(40) not null,
    modify field varchar(128) not null,
    modify text longtext not null;

alter table `ac_form_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify description varchar(255) default '' not null comment 'translatable';

alter table `ac_form_groups` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify group_name varchar(40) default '' not null;

alter table `ac_forms` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify form_name varchar(40) default '' not null,
    modify controller varchar(100) default '' not null,
    modify success_page varchar(100) default '' not null;

alter table `ac_global_attributes` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify element_type char default 'I' not null,
    modify settings text default '' not null,
    modify regexp_pattern varchar(255) null;

alter table `ac_global_attributes_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(64) not null comment 'translatable',
    modify placeholder varchar(255) default '' null comment 'translatable',
    modify error_text varchar(255) not null comment 'translatable';

alter table `ac_global_attributes_groups` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_global_attributes_groups_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(64) not null comment 'translatable';

alter table `ac_global_attributes_type_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify type_name varchar(64) not null comment 'translatable';
alter table `ac_global_attributes_type_descriptions` comment '';

alter table `ac_global_attributes_types` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify type_key varchar(64) not null,
    modify controller varchar(100) not null;

alter table `ac_global_attributes_value_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify value text default '' not null comment 'translatable';

alter table `ac_global_attributes_values` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify price_prefix char null,
    modify txt_id varchar(255) null;

drop index `ac_lang_definition_idx` on `ac_language_definitions`;
alter table `ac_language_definitions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify block varchar(160) not null,
    modify language_key varchar(170) not null,
    modify language_value text not null comment 'translatable';
create index `ac_lang_definition_idx` on `ac_language_definitions` (language_value(500));

alter table `ac_languages` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(32) default '' not null,
    modify code varchar(5) not null,
    modify locale varchar(255) not null,
    modify image varchar(255) not null,
    modify directory varchar(32) default '' not null,
    modify filename varchar(64) default '' not null;

alter table `ac_layouts` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify template_id varchar(100) not null,
    modify layout_name varchar(255) default '' not null;

alter table `ac_length_class_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify title varchar(32) not null comment 'translatable',
    modify unit varchar(4) not null comment 'translatable';

alter table `ac_length_classes` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify iso_code varchar(5) not null;

alter table `ac_locations` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(32) default '' not null,
    modify description varchar(255) default '' not null;

alter table `ac_manufacturers` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(64) default '' not null;

alter table `ac_manufacturers_to_stores` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_messages` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify title varchar(128) default '' not null,
    modify message text not null,
    modify status char default '' not null;

alter table `ac_online_customers` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify ip varchar(50) not null,
    modify url text not null,
    modify referer text not null;

alter table `ac_order_data` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify data text null;

alter table `ac_order_data_types` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(64) default '' not null comment 'translatable';

alter table `ac_order_downloads` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(64) default '' not null,
    modify filename varchar(128) default '' not null,
    modify mask varchar(128) default '' not null,
    modify activate varchar(64) not null,
    modify activate_order_status_id varchar(256) default '' not null,
    modify attributes_data longtext null;

alter table `ac_order_downloads_history` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify filename varchar(128) default '' not null,
    modify mask varchar(128) default '' not null;

alter table `ac_order_history` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify comment text not null;

alter table `ac_order_options` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) not null,
    modify sku varchar(64) default '' not null,
    modify value text not null,
    modify prefix char default '' not null,
    modify settings longtext null;

alter table `ac_order_product_stock_locations` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify location_name varchar(255) not null;

alter table `ac_order_products` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) default '' not null,
    modify model varchar(24) default '' not null,
    modify sku varchar(64) default '' not null,
    modify weight_iso_code varchar(5) default '' not null,
    modify length_iso_code varchar(5) default '' not null;

alter table `ac_order_status_ids` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify status_text_id varchar(64) not null;

alter table `ac_order_statuses` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(32) not null comment 'translatable';

alter table `ac_order_totals` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify title varchar(255) default '' not null,
    modify text varchar(255) default '' not null,
    modify type varchar(255) default '' not null,
    modify `key` varchar(128) default '' not null;

alter table `ac_orders` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify invoice_prefix varchar(10) default '' not null,
    modify store_name varchar(64) not null,
    modify store_url varchar(255) not null,
    modify firstname varchar(32) default '' not null,
    modify lastname varchar(32) not null,
    modify telephone varchar(32) default '' not null,
    modify fax varchar(32) default '' not null,
    modify email varchar(96) default '' not null,
    modify shipping_firstname varchar(32) not null,
    modify shipping_lastname varchar(32) default '' not null,
    modify shipping_company varchar(32) not null,
    modify shipping_address_1 varchar(128) not null,
    modify shipping_address_2 varchar(128) not null,
    modify shipping_city varchar(128) not null,
    modify shipping_postcode varchar(10) default '' not null,
    modify shipping_zone varchar(128) not null,
    modify shipping_country varchar(128) not null,
    modify shipping_address_format text not null,
    modify shipping_method varchar(128) default '' not null,
    modify shipping_method_key varchar(128) default '' not null,
    modify payment_firstname varchar(32) default '' not null,
    modify payment_lastname varchar(32) default '' not null,
    modify payment_company varchar(32) not null,
    modify payment_address_1 varchar(128) not null,
    modify payment_address_2 varchar(128) not null,
    modify payment_city varchar(128) not null,
    modify payment_postcode varchar(10) default '' not null,
    modify payment_zone varchar(128) not null,
    modify payment_country varchar(128) not null,
    modify payment_address_format text not null,
    modify payment_method varchar(128) default '' not null,
    modify payment_method_key varchar(128) default '' not null,
    modify comment text not null,
    modify currency varchar(3) not null,
    modify ip varchar(50) default '' not null,
    modify payment_method_data text default '' not null;

alter table `ac_page_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) not null comment 'translatable',
    modify title varchar(255) not null comment 'translatable',
    modify seo_url varchar(100) default '' not null,
    modify keywords varchar(255) default '' not null comment 'translatable',
    modify description varchar(255) default '' not null comment 'translatable',
    modify content text null comment 'translatable';

alter table `ac_pages` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify controller varchar(100) not null,
    modify key_param varchar(40) default '' not null,
    modify key_value varchar(40) default '' not null;

alter table ac_product_filter_descriptions engine=InnoDB collate=utf8mb4_unicode_ci,
    modify value varchar(255) default '' not null comment 'translatable';

alter table ac_product_filter_ranges engine=InnoDB collate=utf8mb4_unicode_ci;
alter table ac_product_filter_ranges_descriptions engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) default '' not null comment 'translatable';

alter table ac_product_filters engine=InnoDB collate=utf8mb4_unicode_ci,
    modify filter_type char default '' not null,
    modify categories_hash text not null;

alter table `ac_pages_forms` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_pages_layouts` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_product_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) not null comment 'translatable',
    modify meta_keywords varchar(255) not null comment 'translatable',
    modify meta_description varchar(255) not null comment 'translatable',
    modify description longtext not null comment 'translatable',
    modify blurb text not null comment 'translatable';

alter table `ac_product_discounts` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify price_prefix char default '' not null;

alter table `ac_product_option_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) not null comment 'translatable',
    modify option_placeholder varchar(255) default '' null comment 'translatable',
    modify error_text varchar(255) not null comment 'translatable';

alter table `ac_product_option_value_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name text null comment 'translatable',
    modify grouped_attribute_names text null;

alter table `ac_product_option_values` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify txt_id varchar(255) null,
    modify sku varchar(255) null,
    modify prefix char not null,
    modify weight_type varchar(3) not null,
    modify grouped_attribute_data text null;

alter table `ac_product_options` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify element_type char default 'I' not null,
    modify regexp_pattern varchar(255) default '' not null,
    modify settings text null;

alter table `ac_product_specials` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify price_prefix char default '' not null;

alter table `ac_product_stock_locations` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_product_tags` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify tag varchar(32) not null comment 'translatable';

alter table `ac_products` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify model varchar(64) not null,
    modify sku varchar(64) not null,
    modify location varchar(128) not null,
    modify stock_checkout char default '' null,
    modify settings longtext null;

alter table `ac_products_featured` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_products_related` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_products_to_categories` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_products_to_downloads` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_products_to_stores` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_resource_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) default '' null comment 'translatable',
    modify title varchar(255) default '' null comment 'translatable',
    modify description text null comment 'translatable',
    modify resource_path varchar(255) null,
    modify resource_code text null;

alter table `ac_resource_library` engine=InnoDB collate=utf8mb4_unicode_ci;

alter table `ac_resource_map` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify object_name varchar(40) not null;

alter table `ac_resource_types` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify type_name varchar(40) default '' not null,
    modify default_directory varchar(255) not null,
    modify default_icon varchar(255) null,
    modify file_types varchar(255) default '' not null;

alter table `ac_reviews` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify author varchar(64) default '' not null,
    modify text longtext not null;

drop index `ac_settings_idx` on `ac_settings`;
alter table `ac_settings` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify `group` varchar(32) not null,
    modify `key` varchar(64) default '' not null,
    modify value text not null;
create index `ac_settings_idx` on `ac_settings` (value(500));

alter table `ac_stock_statuses` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(32) not null comment 'translatable';

alter table `ac_store_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify description longtext default '' null comment 'translatable',
    modify title longtext default '' null comment 'translatable',
    modify meta_description longtext default '' null comment 'translatable',
    modify meta_keywords longtext default '' null comment 'translatable';

alter table `ac_stores` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(64) not null,
    modify alias varchar(15) not null;

alter table `ac_task_details` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify created_by varchar(255) default '' null,
    modify settings longtext default '' null;

alter table `ac_task_steps` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify controller varchar(255) default '' null,
    modify settings longtext default '' null;

alter table `ac_tasks` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(255) default '' not null;

alter table `ac_tax_class_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify title varchar(128) not null comment 'translatable',
    modify description varchar(255) default '' not null comment 'translatable';

alter table `ac_tax_classes` engine=InnoDB collate=utf8mb4_unicode_ci;
alter table `ac_tax_rate_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify description varchar(255) default '' not null comment 'translatable';

alter table `ac_tax_rates` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify rate_prefix char default '%' not null,
    modify threshold_condition char(2) not null,
    modify tax_exempt_groups text null;

alter table `ac_url_aliases` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify query varchar(255) not null,
    modify keyword varchar(255) not null comment 'translatable';

alter table `ac_user_groups` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(64) not null,
    modify permission longtext not null;

alter table `ac_user_notifications` engine=InnoDB charset = utf8mb4,
    modify sendpoint varchar(255) not null,
    modify protocol varchar(30) not null,
    modify uri text not null;

alter table `ac_user_sessions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify token varchar(128) default '' not null,
    modify ip varchar(50) default '' not null;

alter table `ac_users` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify username varchar(20) default '' not null,
    modify salt varchar(8) default '' not null,
    modify password varchar(40) default '' not null,
    modify firstname varchar(32) default '' not null,
    modify lastname varchar(32) default '' not null,
    modify email varchar(96) default '' not null,
    modify ip varchar(50) default '' not null;

alter table `ac_weight_class_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify title varchar(32) not null comment 'translatable',
    modify unit varchar(4) default '' not null comment 'translatable';

alter table `ac_weight_classes` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify iso_code varchar(5) not null;

alter table `ac_zone_descriptions` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify name varchar(128) not null comment 'translatable';

alter table `ac_zones` engine=InnoDB collate=utf8mb4_unicode_ci,
    modify code varchar(32) default '' not null;

alter table `ac_zones_to_locations` engine=InnoDB collate=utf8mb4_unicode_ci;

#suppliers
create table if not exists `ac_suppliers`
(
    `id`            int auto_increment,
    `code`          varchar(100)                        not null,
    `name`          varchar(100)                        not null,
    `date_added`    timestamp default CURRENT_TIMESTAMP not null,
    `date_modified` timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint `ac_suppliers_pk`
        primary key (`id`, `code`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

create table if not exists `ac_object_types`
(
    `id`         int auto_increment,
    `name`       varchar(100) not null,
    `related_to` varchar(100)  not null,
    constraint `ac_object_types_pk`
        primary key (`id`, `name`, `related_to`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment 'list of types for mapping data';

create table if not exists `ac_supplier_data`
(
    id             int auto_increment,
    supplier_code  varchar(100)                          not null,
    object_type_id int                                   not null,
    object_id      int                                   not null,
    uid            varchar(255)                          not null comment 'unique id of object from supplier API',
    data           longtext collate utf8mb4_bin          not null comment 'json encoded data' check (json_valid(`data`)),
    date_added     timestamp default current_timestamp() not null,
    date_modified  timestamp default current_timestamp() not null on update current_timestamp(),
    primary key (id, supplier_code, object_type_id, object_id, uid)
) ENGINE=InnoDb DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
#
alter table `ac_products`
    add supplier_code varchar(100) null after call_to_order,
    add supplier_id varchar(100) null after supplier_code,
    add constraint `ac_products_supplier_idx`
        unique (supplier_code, supplier_id);
#
alter table `ac_product_stock_locations` add supplier_code varchar(100) null;
alter table `ac_product_stock_locations` add supplier_id varchar(100) null;
CREATE UNIQUE INDEX `ac_product_stock_locations_supplier_idx`
    on `ac_product_stock_locations` (`supplier_code`, `supplier_id`);

##
alter table `ac_product_option_values`
    add supplier_code varchar(100) null,
    add supplier_id varchar(100) null,
    add date_added timestamp default current_timestamp() null,
    add date_modified timestamp default current_timestamp() not null on update current_timestamp(),
    add constraint `ac_product_option_values_supplier_idx`
        unique (supplier_id, supplier_code);
#
alter table `ac_categories`
    add supplier_code varchar(100) null after status,
    add supplier_id varchar(100) null after supplier_code,
    add constraint `ac_categories_supplier_idx` unique (supplier_code, supplier_id);



