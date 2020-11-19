<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table easy_finance(easy_finance_id int not null auto_increment, first_name varchar(100),last_name varchar(100),email varchar(100),phone varchar(100),city varchar(100),primary key(easy_finance_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 