<?php

/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("
	CREATE TABLE `neo_vexpress_awb_no` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `awb` int(11) NOT NULL,
	    `status` int(11) NOT NULL DEFAULT '0'
	);
");

$this->endSetup();