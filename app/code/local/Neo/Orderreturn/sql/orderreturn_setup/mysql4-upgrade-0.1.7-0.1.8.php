<?php //ALTER TABLE `sales_flat_shipment_track` ADD `out_for_delivery_notify` TINYINT(2) NOT NULL DEFAULT '0' AFTER `payload`;
/**
* Adding new column for checking notification on out for delivery event
* Mahesh Gurav, 29th Mar'18
**/
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE `sales_flat_shipment_track` ADD `out_for_delivery_notify` TINYINT(2) NOT NULL DEFAULT '0';
");
$installer->endSetup();