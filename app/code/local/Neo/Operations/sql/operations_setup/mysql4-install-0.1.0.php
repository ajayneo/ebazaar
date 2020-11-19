<?php
$this->startSetup();

$this-> run("DROP TABLE IF EXISTS `serviceable_pincodes`;");
$this->run(" 
	CREATE TABLE `serviceable_pincodes` (
	    `entity_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `state_code` varchar(50) NOT NULL,
	    `city` varchar(50) NOT NULL,
	    `pincode` int(10) NOT NULL DEFAULT 0
	);
");


$this->endSetup();
?>