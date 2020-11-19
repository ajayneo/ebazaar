<?php
$this->startSetup();

$this-> run("DROP TABLE IF EXISTS `bluedart_pincodes`;");
$this->run(" 
	CREATE TABLE `bluedart_pincodes` (
	    `entity_id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `shipment_type` varchar(50) NOT NULL,
	    `pincode` int(10) NOT NULL DEFAULT 0
	);
");


$this->endSetup();
?>