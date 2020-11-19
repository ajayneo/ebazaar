<?php 
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$this->getTable('city_pincodes')} ADD COLUMN `ecom` VARCHAR(50);
ALTER TABLE {$this->getTable('city_pincodes')} ADD COLUMN `dhl_bluedart` VARCHAR(50);
");
$installer->endSetup();