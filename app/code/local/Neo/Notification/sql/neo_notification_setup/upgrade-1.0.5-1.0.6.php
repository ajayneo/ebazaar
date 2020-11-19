<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `neo_notification_new_device` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `device_type` varchar(15) NOT NULL COMMENT 'Device Type',
  `device_Id` varchar(255) NOT NULL COMMENT 'Device ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created Time',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
  PRIMARY KEY(`entity_id`)
);

ALTER TABLE `neo_notification_new_device` ADD UNIQUE(device_Id);
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 