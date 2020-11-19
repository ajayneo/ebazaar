<?php
$this->startSetup();

$this-> run("DROP TABLE IF EXISTS `product_log`;");
$this->run(" 
	CREATE TABLE `product_log` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `product_id` varchar(100) NOT NULL,
	    `log_message` Text NOT NULL,
	    `added_by` varchar(255) NOT NULL, 
	    `created_at` TIMESTAMP
	);
");


$this->endSetup();
?>