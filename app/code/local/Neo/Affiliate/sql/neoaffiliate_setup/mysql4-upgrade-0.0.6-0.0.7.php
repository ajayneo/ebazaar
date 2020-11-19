<?php
	$installer = $this;
	$installer->startSetup();
	$table = $installer->getTable('neo_affiliate');
	$installer->run("ALTER TABLE ".$table." ADD COLUMN tracking_title VARCHAR(100)");
	$installer->run("ALTER TABLE ".$table." ADD COLUMN tracking_no VARCHAR(100)");
	$installer->run("ALTER TABLE ".$table." ADD COLUMN istrackingadded VARCHAR(45) NOT NULL DEFAULT '0'");
	$installer->endSetup();
?>