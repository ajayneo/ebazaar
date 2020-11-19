<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Facebook
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
abstract class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Pinterest_Abstract extends Creatuitycorp_Shareyourpurchase_Block_Social_Button_Abstract {

    protected function _construct() {
        if ($this->_isEnabled()) {
            $this->setTemplate('shareyourpurchase/pinterest.phtml');
        }
    }

    /**
     * Check if button is enabled
     * 
     * @return boolean
     */
    protected function _isEnabled() {
        return Mage::getStoreConfig(Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract::XPATH_ENABLE);
    }

    abstract public function getPictureUrl();
    abstract protected function _getTemplate();
    abstract protected function _getButtonType();
    
    public function getPinterestButton() {
        $html = '<a href="//pinterest.com/pin/create/button/';

        if ($this->_getButtonType()) {
            $html .= '?url=' . urlencode($this->getAttachedLink())
                    . '&media=' . urlencode($this->getPictureUrl())
                    . '&description=' . urlencode($this->_getTemplate())
                    . '" data-pin-do="buttonPin" ';
        } else {
            $html .= '" data-pin-do="buttonBookmark"';
        }
        $html .= '><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>';
        return $html;
    }
}