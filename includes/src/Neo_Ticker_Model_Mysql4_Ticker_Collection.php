<?php

class Neo_Ticker_Model_Mysql4_Ticker_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ticker/ticker');
    }
}