<?php

class Neo_Career_Model_Mysql4_Career extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the career_id refers to the key field in your database table.
        $this->_init('career/career', 'career_id');
    }
}