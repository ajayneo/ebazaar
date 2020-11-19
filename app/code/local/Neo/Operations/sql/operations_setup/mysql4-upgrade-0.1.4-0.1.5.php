<?php 
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$this->getTable('city_pincodes')} ADD COLUMN `ecom_qc` VARCHAR(50);
");
$installer->endSetup();