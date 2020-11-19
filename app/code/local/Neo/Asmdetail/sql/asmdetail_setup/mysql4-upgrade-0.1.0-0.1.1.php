<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table neo_asmdetail_arm(id int not null auto_increment, name varchar(100), primary key(id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 