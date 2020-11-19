<?php
class Neo_Offers_Model_Mysql4_Offers extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("offers/offers", "cashback_offers_id");
    }
}