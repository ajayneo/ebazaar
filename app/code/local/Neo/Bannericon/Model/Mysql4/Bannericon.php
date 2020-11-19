<?php

class Neo_Bannericon_Model_Mysql4_Bannericon extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the bannericon_id refers to the key field in your database table.
        $this->_init('bannericon/bannericon', 'bannericon_id');
    }
}