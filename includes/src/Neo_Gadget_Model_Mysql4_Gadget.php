<?php
class Neo_Gadget_Model_Mysql4_Gadget extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("gadget/gadget", "id");
    }
}