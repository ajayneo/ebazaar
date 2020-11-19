<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table cashback_offers(cashback_offers_id int not null auto_increment, offer text, offer_desc text,primary key(cashback_offers_id));		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 