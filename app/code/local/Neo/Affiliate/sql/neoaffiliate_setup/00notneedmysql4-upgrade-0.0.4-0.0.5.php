<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE {$this->getTable('neo_affiliate')}
    	ADD COLUMN `order_id` VARCHAR(45) NOT NULL AFTER `customer_id`
    ");
$installer->endSetup();
?>