<?php

$this->startSetup();

$this->run(" 
	ALTER TABLE creditsuvidha
	ADD COLUMN `form_status` enum('0','1') NOT NULL DEFAULT '0'
");

$this->endSetup();

