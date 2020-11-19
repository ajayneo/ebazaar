<?php

$this->startSetup();

$this->run(" 
	ALTER TABLE creditsuvidha
	ADD COLUMN `status` enum('0','1','2') NOT NULL DEFAULT '0',
	ADD COLUMN `mapped_status` enum('0','1') NOT NULL DEFAULT '0',
	ADD COLUMN `mapped_details` varchar(255) NOT NULL;
");

$this->endSetup();

