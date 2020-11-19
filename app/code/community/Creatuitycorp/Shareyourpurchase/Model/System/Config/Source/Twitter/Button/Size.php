<?php

class Creatuitycorp_Shareyourpurchase_Model_System_Config_Source_Twitter_Button_Size {
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => '0', 'label' => Mage::helper('shareyourpurchase')->__('Normal')),
            array('value' => '1', 'label' => Mage::helper('shareyourpurchase')->__('Large')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return array(
            '0' => Mage::helper('shareyourpurchase')->__('Normal'),
            '1' => Mage::helper('shareyourpurchase')->__('Large'),
        );
    }
    
}
