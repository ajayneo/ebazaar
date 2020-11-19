<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table neo_vowdelight_imei(id int not null auto_increment, imei_numbers varchar(15), primary key(id));
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 