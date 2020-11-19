<?php

class Neo_Career_Model_Career extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('career/career');
    }
}