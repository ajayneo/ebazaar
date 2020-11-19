<?php

$this->startSetup();

$this->run(" 
	ALTER TABLE creditsuvidha
ADD COLUMN `customer_id` INT(10) NOT NULL,
ADD COLUMN `assigned_credit_limit` INT(12) NULL,
ADD COLUMN `credit_approved_by` VARCHAR(100) NULL,
ADD COLUMN `credit_approval_date` DATE NULL;

");

$this->endSetup();

