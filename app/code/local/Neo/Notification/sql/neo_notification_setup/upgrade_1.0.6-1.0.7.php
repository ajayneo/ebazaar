<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `neo_notification_fcm_push` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `device_type` varchar(15) NOT NULL COMMENT 'Device Type',
  `version` varchar(15) NOT NULL COMMENT 'Device Version',
  `device_Id` varchar(255) NOT NULL COMMENT 'Device ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `status` smallint(6) DEFAULT NULL COMMENT 'Enabled',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created Time',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
  PRIMARY KEY(`entity_id`)
);

ALTER TABLE `neo_notification_fcm_push` ADD UNIQUE(device_Id);

CREATE TABLE IF NOT EXISTS `neo_notification_fcm_device` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `device_type` varchar(15) NOT NULL COMMENT 'Device Type',
  `version` varchar(15) NOT NULL COMMENT 'Device Version',
  `device_Id` varchar(255) NOT NULL COMMENT 'Device ID',
  `status` smallint(6) DEFAULT NULL COMMENT 'Enabled',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created Time',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated Time',
  PRIMARY KEY(`entity_id`)
);

ALTER TABLE `neo_notification_fcm_device` ADD UNIQUE(device_Id);
SQLTEXT;

$installer->run($sql);
$installer->endSetup();
	 