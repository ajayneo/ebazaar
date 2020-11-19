<?php
class Neo_Productpricereport_Model_Mysql4_Productpricereport extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("productpricereport/productpricereport", "id");
    }
}