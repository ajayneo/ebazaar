<?php

$installer = $this;

$installer->startSetup();

$installer->run("

	ALTER TABLE `neo_gadget_form_submition` ADD `is_active` VARCHAR(10) NULL;
");

$installer->endSetup();