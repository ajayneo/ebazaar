<?php
class Neo_Operations_Model_Mysql4_Bluedartpincodes extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("operations/bluedartpincodes", "entity_id");
    }
}