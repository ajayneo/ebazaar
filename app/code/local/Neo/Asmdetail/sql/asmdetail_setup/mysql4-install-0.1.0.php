<?php

/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("
	CREATE TABLE `neo_asmdetail` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `name` int(11) unsigned NOT NULL,
	    `state` int(11) unsigned NOT NULL,
	    `email` varchar(255) NOT NULL
	);
");

$this->endSetup();