<?php
class Neo_Vexpressawb_Model_Mysql4_Vexpress extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("vexpressawb/vexpress", "id");
    }
}