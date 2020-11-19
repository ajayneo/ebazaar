<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout_Success_Product_Button
 * 
 * This block contains social media buttons for the purchased product
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout_Success_Product_Buttons extends Mage_Core_Block_Template {

    /**
     * Add Facebook button 
     */
    public function addFacebookButton() {
        $this->_addButton('facebook');
    }

    /**
     * Add Twitter button 
     */
    public function addTwitterButton() {
        $this->_addButton('twitter');
    }
    
    /**
     * Add Pinterest button 
     */
    public function addPinterestButton() {
        $this->_addButton('pinterest');
    }

    /**
     * Add social media button
     * 
     * @param string $button 
     */
    protected function _addButton($button) {
        $block = Mage::app()
                ->getLayout()
                ->createBlock('shareyourpurchase/social_button_' . $button . '_product');
        $this->setChild($button . '_product_button', $block);
    }

}
