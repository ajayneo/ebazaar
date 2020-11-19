<?php

	/* @var $this Mage_Core_Model_Resource_Setup */

	$this->startSetup();

	$this->run("
		CREATE TABLE `neo_bank_transfer_delivery` (
		    `id` int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		    `order_id` int(20) NOT NULL,
			`order_num` int(20) NOT NULL,
 			`delivery` varchar(255) NOT NULL
		);
	");

  
	$this->endSetup();

?> 