<?php

$this->startSetup();

$this->run(" 
	ALTER TABLE creditsuvidha
	ADD COLUMN arm_name varchar(100),
	ADD COLUMN arm_email varchar(100),
	ADD COLUMN crm_name varchar(100),
	ADD COLUMN crm_email varchar(100)
");

$this->endSetup();

