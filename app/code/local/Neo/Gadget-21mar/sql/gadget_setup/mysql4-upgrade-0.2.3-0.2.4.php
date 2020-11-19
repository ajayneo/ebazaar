<?php

$installer = $this;

$installer->startSetup();

$installer->run("

	ALTER TABLE `neo_gadget_form_submition` ADD `awb_response` Text NULL;
");

$installer->endSetup();