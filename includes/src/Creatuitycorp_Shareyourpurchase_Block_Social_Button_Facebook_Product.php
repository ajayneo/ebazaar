<?php

class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Facebook_Product extends Creatuitycorp_Shareyourpurchase_Block_Social_Button_Facebook_Abstract {

    const XPATH_ENABLE = 'shareyourpurchase/facebook_one_product/enable';
    const XPATH_USE_TEMPLATE = 'shareyourpurchase/facebook_one_product/use_template';
    const XPATH_TEMPLATE_CAPTION = 'shareyourpurchase/facebook_one_product/template_capteion';
    const XPATH_TEMPLATE_TITLE = 'shareyourpurchase/facebook_one_product/template_title';
    const XPATH_TEMPLATE_DESCRIPTION = 'shareyourpurchase/facebook_one_product/template_description';
    const XPATH_IMAGE = 'shareyourpurchase/facebook_one_product/post_image';
    const XPATH_IMAGE_USER = 'shareyourpurchase/facebook_one_product/user_image';
    const XPATH_SHOW_PRODUCT_NAME = 'shareyourpurchase/facebook_one_product/show_product_name';
    const XPATH_SHOW_PRODUCT_PRICE = 'shareyourpurchase/facebook_one_product/show_product_price';
    const XPATH_ICON_SIZE = 'shareyourpurchase/facebook_one_product/icon_size';
    const XPATH_TEMPLATE_TITLE_DEFAULT = 'shareyourpurchase/template_one_product/template_title';

    protected function _isEnabled() {
        return parent::_isEnabled() && Mage::getStoreConfig(self::XPATH_ENABLE);
    }

    /**
     * Return caption to post
     * 
     * @return string
     */
    public function getCaption() {
        return Mage::getStoreConfig(self::XPATH_TEMPLATE_CAPTION);
    }

    /**
     * Return description for post
     * 
     * @return string
     */
    public function getDescription() {
        $useTemplate = $this->_useTemplate();
        $template = $this->_getTitleTemplate();

        $item = $this->_getProduct();

        $price = Mage::helper('core')->currency($item->getPrice(), true, false);
        $content = str_replace(array('{item_name}', '{item_price}'), array(!$useTemplate && !Mage::getStoreConfig(self::XPATH_SHOW_PRODUCT_NAME) ? '' : $item->getName(),
            !$useTemplate && !Mage::getStoreConfig(self::XPATH_SHOW_PRODUCT_PRICE) ? '' : $price), $template);

        return $content;
    }

    /**
     * Return information use template provided by user OR hardcoded in config.xml
     * @return bool
     */
    protected function _useTemplate() {
        return Mage::getStoreConfig(self::XPATH_USE_TEMPLATE);
    }

    /**
     * Return template to use in post title
     * @return string
     */
    protected function _getTitleTemplate() {
        $useTemplate = $this->_useTemplate();
        return Mage::getStoreConfig($useTemplate ? self::XPATH_TEMPLATE_TITLE : self::XPATH_TEMPLATE_TITLE_DEFAULT);
        ;
    }

    public function getMainText() {
        $useTemplate = $this->_useTemplate();
        $template = $this->_getTitleTemplate();
        
        $item = $this->_getProduct();

        $content = str_replace(array('{store_link}', '{item_name}', '{website_name}'), array(Mage::helper('core/url')->getHomeUrl(),
            !$useTemplate && !Mage::getStoreConfig(self::XPATH_SHOW_PRODUCT_NAME) ? '' : $item->getName(),
            Mage::app()->getWebsite()->getName()), $template
        );
        return $content;
    }

    public function getPictureUrl() {
        return $this->_getPictureUrl(self::XPATH_IMAGE, self::XPATH_IMAGE_USER);
    }

    protected function _getProductImage() {
        $product = Mage::getModel('catalog/product')->load($this->getProduct());
        return $product->getImageUrl();
    }

    protected function _getProduct() {
        return $this->getParentBlock()->getParentBlock()->getProduct();
    }
    
    public function getProductName(){
        return $this->getParentBlock()->getParentBlock()->getProduct()->getName();
    }

}
