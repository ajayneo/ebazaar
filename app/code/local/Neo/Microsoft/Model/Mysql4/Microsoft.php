<?php
class Neo_Microsoft_Model_Mysql4_Microsoft extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("microsoft/microsoft", "id");
    }
}