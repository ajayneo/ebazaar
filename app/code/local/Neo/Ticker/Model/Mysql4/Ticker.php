<?php

class Neo_Ticker_Model_Mysql4_Ticker extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the ticker_id refers to the key field in your database table.
        $this->_init('ticker/ticker', 'ticker_id');
    }
}