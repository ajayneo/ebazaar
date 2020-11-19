<?php
$installer = $this;
$installer->startSetup();
$sql="CREATE TABLE `neo_buyback_paytm`( `id` INT(11) NOT NULL AUTO_INCREMENT, `paytm_invoice` TEXT  NULL, `customer_name` VARCHAR(100)  NULL, `email` VARCHAR(100)  NULL, `mobile` VARCHAR(12)  NULL, `address` TEXT  NULL, `buyback_due` INT(2)  NOT NULL DEFAULT 0, PRIMARY KEY (`id`));";

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 