<?php

class Neo_Custom_Model_Custom extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('custom/custom');
    }
}