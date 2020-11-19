<?php
//ALTER TABLE `neo_gadget_form_submition` CHANGE `mobile` `mobile` VARCHAR(100) NOT NULL;
$installer = $this;

$installer->startSetup();

$installer->run("

	ALTER TABLE `neo_gadget_form_submition` CHANGE `mobile` `mobile` VARCHAR(100) NOT NULL;
");

$installer->endSetup();
?>