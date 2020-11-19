<?php

class Neo_Custom_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('custom')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('custom')->__('Disabled')
        );
    }
}