<?php
class Neo_Deliveryvalidator_Model_Mysql4_Deliveryvalidator extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("deliveryvalidator/deliveryvalidator", "pincode_id");
    }
}