<?php

$this->startSetup();

$this->run(" 
	ALTER TABLE creditsuvidha
	ADD COLUMN created_at timestamp,
	ADD COLUMN updated_at timestamp
");

$this->endSetup();

