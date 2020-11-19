<?php

$installer = $this;

$installer->startSetup();

$installer->run("

	ALTER TABLE `neo_gadget_form_submition` ADD `used_promo_code` VARCHAR(100) NULL;
");

$installer->endSetup();