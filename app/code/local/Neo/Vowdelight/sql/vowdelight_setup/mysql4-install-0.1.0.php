<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table neo_vowdelight(id int not null auto_increment, request_id int(10), sku varchar(100),  old_order_no varchar(100), old_imei_no varchar(100), rvp_awb_no varchar(100), new_order_no varchar(100), new_imei_no varchar(100), new_awb_no varchar(100),
customer_id int(10), forward_payload text, reverse_payload text, created_at timestamp, updated_at timestamp,  primary key(id));		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 