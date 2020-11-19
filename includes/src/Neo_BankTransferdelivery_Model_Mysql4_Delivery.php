<?php
class Neo_BankTransferdelivery_Model_Mysql4_Delivery extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("banktransferdelivery/delivery", "id");
    }
}