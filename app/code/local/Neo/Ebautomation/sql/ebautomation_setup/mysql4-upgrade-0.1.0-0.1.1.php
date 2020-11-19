<?php
$this->startSetup();

$this-> run("DROP TABLE IF EXISTS `stock_tamil_nadu`;");
$this->run(" 
	CREATE TABLE `stock_tamil_nadu` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `sku` varchar(255) NOT NULL,
	    `name` varchar(255) NOT NULL,
	    `qty` int(10) NOT NULL DEFAULT 0
	);
");


$this->endSetup();
?>