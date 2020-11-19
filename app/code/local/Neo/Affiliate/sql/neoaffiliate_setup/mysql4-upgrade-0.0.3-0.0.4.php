<?php
$installer = $this;
$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS {$this->getTable('neo_affiliate')};
CREATE TABLE {$this->getTable('neo_affiliate')} (
  `affiliate_id` int(11) unsigned NOT NULL auto_increment,
  `affiliate_name` varchar(255) NOT NULL,
  `repcode` varchar(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `order_no` varchar(255) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `item_price_excl_tax` varchar(255) NOT NULL,
  `tax_amount` varchar(255) NOT NULL,
  `row_total` varchar(255) NOT NULL,
  `order_total` varchar(255) NOT NULL,
  `aff_customer_name` varchar(255) NOT NULL,
  `aff_customer_email` varchar(255) NOT NULL,
  `aff_customer_mobile` varchar(255) NOT NULL,
  PRIMARY KEY (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
 
$installer->endSetup();

?>