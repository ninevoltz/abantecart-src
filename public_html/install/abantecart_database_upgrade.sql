alter table `ac_product_option_values`
add column IF NOT EXISTS require_shipping smallint default 0 not null comment 'depends on "shipping" column of table "products" ' after prefix;