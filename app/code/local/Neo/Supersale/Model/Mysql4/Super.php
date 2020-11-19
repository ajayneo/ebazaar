<?php
class Neo_Supersale_Model_Mysql4_Super extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("supersale/super", "id");
    }
}