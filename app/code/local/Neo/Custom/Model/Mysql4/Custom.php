<?php

class Neo_Custom_Model_Mysql4_Custom extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the custom_id refers to the key field in your database table.
        $this->_init('custom/custom', 'custom_id');
    }
}