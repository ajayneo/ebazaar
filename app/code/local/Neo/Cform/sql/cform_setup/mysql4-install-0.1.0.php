<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table neocform(neocform_id int not null auto_increment, state varchar(100), min_amount varchar(100),max_amount varchar(100),category varchar(100),percentage varchar(100),status varchar(100), primary key(neocform_id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 