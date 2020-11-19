<?php
class Neo_Asmdetail_Model_Mysql4_Asmdetail extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("asmdetail/asmdetail", "id");
    }
}