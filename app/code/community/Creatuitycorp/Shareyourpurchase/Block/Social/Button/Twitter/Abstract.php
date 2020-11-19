<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Abstract
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
abstract class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Abstract extends Creatuitycorp_Shareyourpurchase_Block_Social_Button_Abstract {

    protected function _construct() {
        if ($this->_isEnabled()) {
            $this->setTemplate('shareyourpurchase/twitter.phtml');
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

    /**
     * Get Twitter button code
     * 
     * @return string 
     */
    public function getTwitterButton() {
        $productPage = $this->getAttachedLink();
        $productInfo = '&text=' . urlencode($this->_getMessageTemplate());
        $html = '<a href="https://twitter.com/share?url=' . $productPage . $productInfo . '">';
        $html .= $this->__('Tweet');
        $html .= '</a>';
        return $html;
    }

}
