<?php
    /* @var $installer Mage_Customer_Model_Entity_Setup */
    $installer = $this;
    $installer->startSetup();

    $installer->run("-- DROP TABLE IF EXISTS {$this->getTable('`gst_details`')};
CREATE TABLE {$this->getTable('`gst_details`')} (
  `id` INT(11) NOT NULL AUTO_INCREMENT , `customer_id` INT(11) NOT NULL , `email` VARCHAR(100) NOT NULL , `partner_store_name` VARCHAR(100) NOT NULL , `director_name` VARCHAR(100) NOT NULL , `contact_name` VARCHAR(100) NOT NULL , `address_line_1` TEXT NOT NULL , `address_line_2` TEXT NOT NULL , `postcode` INT(6) NOT NULL , `city` VARCHAR(100) NOT NULL , `state` VARCHAR(100) NOT NULL , `mobile` VARCHAR(20) NOT NULL ,`gstin` VARCHAR(15) NOT NULL , `pan` VARCHAR(10) NOT NULL ,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $installer->endSetup();
?>    