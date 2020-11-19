<?php
/*$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `neo_order_return` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `order_number` int(15) NOT NULL,
  `canceled_imei` longtext NOT NULL,
  `reason` longtext NOT NULL,
  `return_action` varchar(255) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `status` longtext NOT NULL,
  `pickup_address` text NOT NULL,
  `utr_filepath` varchar(255) NOT NULL,
  `replace_increment_id` longtext NOT NULL,
  `product_img` longtext NOT NULL,
  `remarks` text NOT NULL,
  `docket_no` varchar(55) NOT NULL,
  `sales_entry_pass` longtext NOT NULL,
  `sales_entry_no` longtext,
  `payment_done` longtext NOT NULL,
  `pay_reference_no` longtext,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL, primary_key(id)
);

CREATE TABLE IF NOT EXISTS `neo_orderreturn_banking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_ifsc` varchar(20) DEFAULT NULL,
  `account_number` int(20) NOT NULL, primary key(id)
);		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();*/