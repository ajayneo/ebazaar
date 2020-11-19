<?php

$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table offer_type_attribute_relation(id int not null auto_increment, offer_id int, offer_name text, attribute_id varchar(50), primary key(id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
?>