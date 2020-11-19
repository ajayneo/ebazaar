<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout_Success
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */

class Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout_Success
    extends Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract {

    
    protected function _isEnabled() {
        if(parent::_isEnabled() === false) {
            return false;
        }
        
        return !Mage::helper('shareyourpurchase/config')->isLightboxEnabled();
    }
    
    /**
     * Get frontend checkout session object
     * 
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession() {
        return Mage::getSingleton('checkout/type_onepage')->getCheckout();
    }

    /**
     * Get order object
     * 
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder() {
        $orderId = $this->_getCheckoutSession()->getLastOrderId();
        /** $model Mage_Sales_Model_Order */
        return Mage::getModel('sales/order')->load($orderId);
    }

    /**
     * Get products from order
     * 
     * @return array
     */
    protected function _provideProducts() {
        return $this->_getOrder()->getAllVisibleItems();
    }
    
    public function getOrderIncrementId() {
        return $this->_getOrder()->getIncrementId();
    }

}
