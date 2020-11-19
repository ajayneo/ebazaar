<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout extends Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract {

    const XPATH_ONEPAGE_CHECKOUT_ENABLE = 'shareyourpurchase/general/onepage_checkout_enable';
    
    protected function _isEnabled() {
        return parent::_isEnabled() 
                && Mage::getStoreConfig(self::XPATH_ONEPAGE_CHECKOUT_ENABLE);
    }
    
    /**
     * Get quote object
     * 
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote() {
        return Mage::getSingleton('checkout/type_onepage')->getQuote();
    }

    /**
     * Get products from quote
     * 
     * @return array
     */
    protected function _provideProducts() {
        return $this->_getQuote()->getAllVisibleItems();
    }

}
