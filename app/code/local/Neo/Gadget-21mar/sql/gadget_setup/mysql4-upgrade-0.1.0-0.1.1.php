<?php

$installer = $this;

$installer->startSetup();

$installer->run("

	ALTER TABLE `neo_gadget_form_submition` ADD `bank_customer_name` VARCHAR(255) NULL, ADD `bank_name` VARCHAR(255) NULL, ADD `bank_ifsc` VARCHAR(50) NULL, ADD `bank_account_no` INT(30) NOT NULL DEFAULT 0, ADD `address_id` INT(10) NOT NULL DEFAULT 0, ADD `imei_no` INT(20) NOT NULL DEFAULT 0;
");

$installer->endSetup();