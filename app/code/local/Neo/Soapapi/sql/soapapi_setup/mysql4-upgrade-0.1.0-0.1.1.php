<?php
$this->startSetup();

$this-> run("DROP TABLE IF EXISTS `inventoryupdates`;");
$this->run(" 
	CREATE TABLE `inventoryupdates` (
	    `id` int AUTO_INCREMENT PRIMARY KEY,
	    `product_id` int(11) NOT NULL ,
	    `stock_item_id` int(11) NOT NULL ,
	    `qty_from_navision` varchar(50) NOT NULL,
	    `reason` text NOT NULL,
	    `updated_at` datetime
	);
");


$this->endSetup();
?>