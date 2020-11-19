<?php
class Neo_Vowdelight_Model_Mysql4_Vowdelight extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("vowdelight/vowdelight", "id");
    }
}