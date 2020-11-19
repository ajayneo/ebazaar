<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table neo_asmdetail_crm(id int not null auto_increment, name varchar(100), primary key(id));
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 