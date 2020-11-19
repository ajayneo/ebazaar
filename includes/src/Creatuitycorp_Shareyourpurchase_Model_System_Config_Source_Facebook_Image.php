<?php

/**
 * Option source data for choose field in admin
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Model_System_Config_Source_Facebook_Image
    extends Creatuitycorp_Shareyourpurchase_Model_System_Config_Source_Image {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array_merge(parent::toOptionArray(), array(
            array('value' => 'facebook', 'label' => Mage::helper('shareyourpurchase')->__('Finded by Facebook')),
        ));
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return array_merge(parent::toArray(), array(
            'facebook' => Mage::helper('shareyourpurchase')->__('Finded by Facebook'),
        ));
    }

}