<?php
$installer = $this;
$installer->startSetup();

// $installer->run("
// 	CREATE TABLE `neo_ecom_cod_awb` (
// 	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
// 	    `awb` int(11) NOT NULL,
// 	    `status` int(11) NOT NULL DEFAULT '0'
// 	);
// ");


$installer->run("
	CREATE TABLE `neo_ecom_ppd_awb` (
	    `id` INTEGER AUTO_INCREMENT PRIMARY KEY,
	    `awb` int(11) NOT NULL,
	    `status` int(11) NOT NULL DEFAULT '0'
	);
");
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 