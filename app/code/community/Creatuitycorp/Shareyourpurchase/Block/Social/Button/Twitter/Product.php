<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Product
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Product extends Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Abstract {

    const XPATH_ENABLE = 'shareyourpurchase/twitter_one_product/enable';
    const XPATH_USE_TEMPLATE = 'shareyourpurchase/twitter_one_product/use_template';
    const XPATH_TEMPLATE = 'shareyourpurchase/twitter_one_product/template';
    const XPATH_SHOW_PRODUCT_NAME = 'shareyourpurchase/twitter_one_product/show_product_name';
    const XPATH_SHOW_PRODUCT_PRICE = 'shareyourpurchase/twitter_one_product/show_product_price';

    /**
     * Check if this button is enabled
     * 
     * @return boolean
     */
    protected function _isEnabled() {
        return parent::_isEnabled() && Mage::getStoreConfig(self::XPATH_ENABLE);
    }

    /**
     * Get message template
     * 
     * @return string
     */
    protected function _getMessageTemplate() {
        $useTemplate = $this->_useTemplate();
        $template = Mage::getStoreConfig($useTemplate ? self::XPATH_TEMPLATE : self::XPATH_TEMPLATE_DESCRIPTION_DEFAULT);

        $item = $this->_getProduct();

        $price = Mage::helper('core')->currency($item->getPrice(), true, false);
        $content = str_replace(array('{item_name}', '{item_price}', '{store_link}', '{website_name}'), array(!$useTemplate && !Mage::getStoreConfig(self::XPATH_SHOW_PRODUCT_NAME) ? '' : $item->getName(),
            !$useTemplate && !Mage::getStoreConfig(self::XPATH_SHOW_PRODUCT_PRICE) ? '' : $price, Mage::helper('core/url')->getHomeUrl(), Mage::app()->getWebsite()->getName()), $template);

        return $content;
    }

    protected function _useTemplate() {
        return Mage::getStoreConfig(self::XPATH_USE_TEMPLATE);
    }
    
    /**
     * Check if the counter should be shown
     * 
     * @return boolean
     */

    /**
     * Get button size (0 - normal, 1 - large)
     * 
     * @return int
     */

    /**
     * Get purchased product
     * 
     * @return Mage_Catalog_Model_Product|null
     */
    protected function _getProduct() {
        return $this->getParentBlock()->getParentBlock()->getProduct();
    }
    
    public function getProductName(){
        return $this->getParentBlock()->getParentBlock()->getProduct()->getName();
    }
}
