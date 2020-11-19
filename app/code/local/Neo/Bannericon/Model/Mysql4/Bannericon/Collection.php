<?php

class Neo_Bannericon_Model_Mysql4_Bannericon_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('bannericon/bannericon');
    }
}