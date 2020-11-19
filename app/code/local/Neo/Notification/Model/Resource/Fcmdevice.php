<?php
class Neo_Notification_Model_Resource_Fcmdevice extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("neo_notification/fcmdevice", "entity_id");
    }
}