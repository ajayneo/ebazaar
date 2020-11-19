<?php

class Neo_Career_Model_Mysql4_Career_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('career/career');
    }
}