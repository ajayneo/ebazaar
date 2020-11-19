<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Facebook
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
abstract class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Facebook_Abstract extends Creatuitycorp_Shareyourpurchase_Block_Social_Button_Abstract {

    const XPATH_APP_ID = 'shareyourpurchase/facebook/app_id';

    protected function _construct() {
        if ($this->_isEnabled()) {
            $this->setTemplate('shareyourpurchase/facebook.phtml');
        }
    }
    
    protected function _isEnabled() {
        return Mage::getStoreConfig(Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract::XPATH_ENABLE);
    }

    /**
     * Return Facebook APP ID
     * 
     * @return string
     */
    public function getAppId() {
        return Mage::getStoreConfig(self::XPATH_APP_ID);
    }

    /**
     * Return url to social controller
     * @return string
     */
    public function getRedirectUrl() {
        return Mage::getUrl('social');
    }
}
