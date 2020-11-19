<?php
$this->startSetup();

$this-> run("DROP TABLE IF EXISTS `dhl_awb_number`;");
$this->run(" 
	CREATE TABLE `dhl_awb_number` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `awb` varchar(50) NOT NULL,
	    `region` varchar(50) NOT NULL,
	    `status` int(10) NOT NULL DEFAULT 0
	);
");


$this->endSetup();
?>