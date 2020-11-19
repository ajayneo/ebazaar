<?php 
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$this->getTable('dhl_awb_number')} ADD COLUMN `shipment_mode` VARCHAR(50)
");
$installer->endSetup();