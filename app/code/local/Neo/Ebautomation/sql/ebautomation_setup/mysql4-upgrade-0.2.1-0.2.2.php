<?php 
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE  sales_flat_order ADD COLUMN app_version varchar(100);");

$installer->endSetup();

?>