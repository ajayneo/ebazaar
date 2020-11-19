<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table pincoderules(pincode_id int not null auto_increment, pincodes text,rules varchar(100), status varchar(100) ,primary key(pincode_id));
    		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 