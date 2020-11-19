<?php

$installer = $this;

$installer->startSetup();

$installer->run("

	ALTER TABLE `neo_gadget_form_submition` ADD `awb_number` VARCHAR(255) NULL;
	ALTER TABLE `neo_gadget_form_submition` ADD `awb_request_date` TIMESTAMP;
");

$installer->endSetup();