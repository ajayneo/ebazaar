<?php

$installer = $this;

$installer->startSetup();

$installer->run("

        ALTER TABLE `neo_gadget_form_submition`         
        ADD COLUMN `confirmed_by_user_id` INT(11),        
        ADD COLUMN `is_order_confirmed` enum('Yes','No') NOT NULL DEFAULT 'No';        
");

$installer->endSetup();