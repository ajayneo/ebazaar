<?php
class Neo_Affiliatelimit_Model_Mysql4_Limit extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("affiliatelimit/limit", "id");
    }
}