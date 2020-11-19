<?php
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE `neo_gadget_other` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `brand` VARCHAR(50) NOT NULL , `model` VARCHAR(50) NOT NULL , `description` VARCHAR(255) NULL , `customer_id` INT(10) NOT NULL , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`));
");


$installer->endSetup();
?>