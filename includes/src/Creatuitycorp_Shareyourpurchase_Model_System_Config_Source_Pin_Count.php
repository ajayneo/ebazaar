<?php

/**
 * Option source data for choose field in admin
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Model_System_Config_Source_Pin_Count {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 0, 'label' => Mage::helper('shareyourpurchase')->__('Above the button')),
            array('value' => 1, 'label' => Mage::helper('shareyourpurchase')->__('Besides the button')),
            array('value' => 2, 'label' => Mage::helper('shareyourpurchase')->__('Not Shown')),
        );
    }

}