<?php

$installer = $this;

$installer->startSetup();


$installer->run("

	ALTER TABLE `neo_orderreturn_banking` ADD `beneficiary_name` VARCHAR(255) NULL DEFAULT NULL;
");

$installer->endSetup();