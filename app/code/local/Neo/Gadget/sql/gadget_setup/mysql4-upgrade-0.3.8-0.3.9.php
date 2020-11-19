<?php

$installer = $this;

$installer->startSetup();

$installer->run("
        ALTER TABLE `neo_gadget_form_submition`         
        ADD COLUMN `confrm_by_retailer_name_in_bank` varchar(150),
        ADD COLUMN `confrm_by_retailer_bank_name` varchar(150),        
        ADD COLUMN `confrm_by_retailer_acct_number` varchar(150),        
        ADD COLUMN `confrm_by_retailer_ifsc_code` varchar(150),        
        ADD COLUMN `confrm_by_retailer_address_id` varchar(150),        
        ADD COLUMN `retailers_sellback_at` datetime;        
");

$installer->endSetup();