<?php
class Neo_Orderreturn_Model_Mysql4_Banking extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("orderreturn/banking", "id");
    }
}