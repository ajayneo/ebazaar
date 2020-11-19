<?php

/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("
	CREATE TABLE `neo_gadget_form_submition` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `sku` varchar(255) NOT NULL,
	    `condition` varchar(255) NOT NULL,
	    `price` varchar(255) NOT NULL
	);
");

$this->run("
	CREATE TABLE `neo_gadget` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY, 
	    `sku` varchar(255) NOT NULL,
	    `brand` varchar(255) NOT NULL,
	    `working_price` varchar(255) NOT NULL,
	    `non_working_price` varchar(255) NOT NULL,
	);
");

$this->run("
	CREATE TABLE `neo_gadget_pick_up_services` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY
	);
");

$this->endSetup();