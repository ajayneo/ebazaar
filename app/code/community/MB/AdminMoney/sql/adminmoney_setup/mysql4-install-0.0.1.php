<?php


$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `bank_name` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `bank_branch_name` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` ADD `cheque_no` VARCHAR( 255 ) NOT NULL;

ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `bank_name` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `bank_branch_name` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_payment')}` ADD `cheque_no` VARCHAR( 255 ) NOT NULL;

    ");

$installer->endSetup();