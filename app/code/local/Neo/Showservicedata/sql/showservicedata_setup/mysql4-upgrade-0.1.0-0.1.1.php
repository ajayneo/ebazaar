<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table neo_service_centre(id int not null auto_increment, 
 type varchar(10), 
 asp_name varchar(50), 
 address text(100), 
 contact_person varchar(50), 
 contact_number varchar(50), 
 mail_id varchar(50), 
 pincode int(6), 
 city varchar(50), 
 state varchar(50), 
 region varchar(50), 
primary key(id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 