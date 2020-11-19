<?php
class Neo_Asmdetail_Model_Mysql4_Asm extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("asmdetail/asm", "id");
    }
}