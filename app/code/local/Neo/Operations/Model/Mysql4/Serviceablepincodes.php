<?php
class Neo_Operations_Model_Mysql4_Serviceablepincodes extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("operations/serviceablepincodes", "entity_id");
    }
}