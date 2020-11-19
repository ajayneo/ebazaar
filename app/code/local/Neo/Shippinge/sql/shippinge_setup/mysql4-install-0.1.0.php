<?php

$installer = $this;
$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS {$this->getTable('sales_order_track_status')};
CREATE TABLE {$this->getTable('sales_order_track_status')} (
  `shippinge_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `order_inc` int(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `tracking_no` varchar(50) NOT NULL,
  `tracking_title` varchar(255) NOT NULL default '',
  `track_status` varchar(50) NOT NULL,
  `actionstatus` varchar(100) NOT NULL,
  PRIMARY KEY (`shippinge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup();