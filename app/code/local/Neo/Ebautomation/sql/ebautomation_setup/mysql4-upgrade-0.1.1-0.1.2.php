<?php 
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$this->getTable('sales_flat_shipment_track')} ADD COLUMN `payload` TEXT
");
$installer->endSetup();