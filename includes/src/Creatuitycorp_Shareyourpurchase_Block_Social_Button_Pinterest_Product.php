<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Button_Pinterest_Product
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Pinterest_Product 
    extends Creatuitycorp_Shareyourpurchase_Block_Social_Button_Pinterest_Abstract {

    const XPATH_ENABLE = 'shareyourpurchase/pinterest_one_product/enable';
    const XPATH_USE_TEMPLATE = 'shareyourpurchase/pinterest_one_product/use_template';
    const XPATH_TEMPLATE = 'shareyourpurchase/pinterest_one_product/template';
    const XPATH_BUTTON_TYPE = 'shareyourpurchase/pinterest_one_product/button_type';
    const XPATH_IMAGE = 'shareyourpurchase/pinterest_one_product/post_image';
    const XPATH_IMAGE_USER = 'shareyourpurchase/pinterest_one_product/user_image';
    const XPATH_SHOW_PRODUCT_NAME = 'shareyourpurchase/pinterest_one_product/show_product_name';
    const XPATH_SHOW_PRODUCT_PRICE = 'shareyourpurchase/pinterest_one_product/show_product_price';

    protected function _isEnabled() {
        return parent::_isEnabled() && Mage::getStoreConfig(self::XPATH_ENABLE);
    }

    public function _getTemplate() {
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

    public function _getButtonType() {
        return Mage::getStoreConfig(self::XPATH_BUTTON_TYPE);
    }

    public function getPictureUrl() {
        return $this->_getPictureUrl(self::XPATH_IMAGE, self::XPATH_IMAGE_USER);
    }

    protected function _getProduct() {
        return $this->getParentBlock()->getParentBlock()->getProduct();
    }
}

?>