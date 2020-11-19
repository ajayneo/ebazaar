<?php

/**
 * Option source data for choose field in admin
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Model_System_Config_Source_Image {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'product_image', 'label' => Mage::helper('shareyourpurchase')->__('Product Image')),
            array('value' => 'shop_logo', 'label' => Mage::helper('shareyourpurchase')->__('Shop Logo')),
            array('value' => 'uploaded_image', 'label' => Mage::helper('shareyourpurchase')->__('Upload Image')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return array(
            'product_image' => Mage::helper('shareyourpurchase')->__('Product Image'),
            'shop_logo' => Mage::helper('shareyourpurchase')->__('Shop Logo'),
            'uploaded_image' => Mage::helper('shareyourpurchase')->__('User Image'),
        );
    }

}