<?php

class Neo_Ticker_Model_Ticker extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ticker/ticker');
    }
}