<?php
class Neo_Notification_Model_Resource_Device extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("neo_notification/device", "entity_id");
    }
}