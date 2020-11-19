<?php 
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$this->getTable('sales_order_stock_location')} ADD COLUMN `invoice_id` VARCHAR(255)
");
$installer->endSetup();