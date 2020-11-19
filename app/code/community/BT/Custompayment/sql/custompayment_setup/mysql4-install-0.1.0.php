<?php
$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup */

$installer->startSetup();
$installer->run("

ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `ap_utr_no` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `ap_check_no` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `ap_bank_name` VARCHAR( 255 ) NOT NULL ;

ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `ap_utr_no` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `ap_check_no` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `ap_bank_name` VARCHAR( 255 ) NOT NULL ;

");

$installer->endSetup();

