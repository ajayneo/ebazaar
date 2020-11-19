<?php
$installer = $this;
$installer->startSetup();
$sql="ALTER TABLE `neo_buyback_paytm` ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `buyback_due`, ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `buyback_due`;";

$installer->run($sql);
$installer->endSetup();
	 