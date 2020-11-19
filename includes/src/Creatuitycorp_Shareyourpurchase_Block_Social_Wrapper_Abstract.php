<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
abstract class Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract extends Mage_Core_Block_Template {

    const XPATH_ENABLE = 'shareyourpurchase/general/enable';
    const PRODUCTS_ALIAS = 'social.products';

    protected function _construct() {
        if ($this->_isEnabled()) {
            $this->setTemplate('shareyourpurchase/social.phtml');
        }
    }

    protected function _isEnabled() {
        return Mage::getStoreConfig(self::XPATH_ENABLE);
    }
    
    public function setChild($alias, $block) {
        if ($block instanceof Creatuitycorp_Shareyourpurchase_Block_Social_Button_Abstract
                || $alias == self::PRODUCTS_ALIAS) {
            $block->setProducts($this->_provideProducts());
        }
        return parent::setChild($alias, $block);
    }

    public function getOrderIncrementId() {
        return null;
    }
    
    abstract protected function _provideProducts();
}
