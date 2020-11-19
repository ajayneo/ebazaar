<?php

$installer = $this;

$installer->startSetup();

$installer->run("

        ALTER TABLE `neo_vowdelight`         
        ADD COLUMN `comment` TEXT AFTER new_awb_no,        
        ADD COLUMN `reason` TEXT AFTER new_awb_no;        
");

$installer->endSetup();