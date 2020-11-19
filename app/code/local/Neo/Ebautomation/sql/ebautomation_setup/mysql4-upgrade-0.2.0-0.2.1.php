<?php 
$installer = $this;
$installer->startSetup();

$installer->run("CREATE TABLE `priceupdatelog` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `product_id` varchar(50) NOT NULL,
	    `user` varchar(50) NOT NULL,
	    `user_id` varchar(50) NOT NULL,
	    `price` varchar(50) NOT NULL,
	    `message` varchar(255) NOT NULL,
	    `created_at` timestamp 
	);");


$installer->endSetup();

?>
