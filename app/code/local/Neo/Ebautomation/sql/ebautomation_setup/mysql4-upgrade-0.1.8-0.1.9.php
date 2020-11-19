<?php 
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE  sales_flat_order ADD COLUMN mapped_status enum('0','1') NOT NULL default '0';");

$installer->run("ALTER TABLE  sales_flat_order ADD COLUMN mapped_details Text;");

$installer->endSetup();

?>