<?php
class Neo_Ecom_Model_Mysql4_Ppdawb extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("ecom/ppdawb", "id");
    }
}