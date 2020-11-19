<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Button_Abstract
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
abstract class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Abstract extends Mage_Core_Block_Template {

    const IMAGE_PRODUCT = 'product_image';
    const IMAGE_SHOP_LOGO = 'shop_logo';
    const IMAGE_UPLOADED_IMAGE = 'uploaded_image';
    const IMAGE_FINDED_BY_FACEBOOK = 'facebook';
    const XPATH_TEMPLATE_DESCRIPTION_DEFAULT = 'shareyourpurchase/template/template_description';
    const XPATH_TEMPLATE_TITLE_DEFAULT = 'shareyourpurchase/template/template_title';

    abstract protected function _getProduct();

    /**
     * Return link to first product
     * 
     * @return string
     */
    public function getAttachedLink() {
        $product = $this->_getProduct();
        
        if ($product && $product->getId()) {
            return Mage::getModel('core/url')->getUrl($product->getUrlPath());
        }
        return Mage::helper('core/url')->getHomeUrl();
    }

    /**
     * Return full url to image
     * 
     * @return string
     */
    protected function _getPictureUrl($imageXpath, $uploadedImageXpath) {
        switch (Mage::getStoreConfig($imageXpath)) {
            case self::IMAGE_PRODUCT:
                return $this->_getProductImage();
                break;
            case self::IMAGE_SHOP_LOGO:
                return $this->_getShopLogo();
                break;
            case self::IMAGE_UPLOADED_IMAGE:
                return $this->_getUploadedImage($uploadedImageXpath);
                break;
            case self::IMAGE_FINDED_BY_FACEBOOK:
            default:
                return '';
        }
    }

    /**
     * Return url of product image
     * 
     * @return string
     */
    protected function _getProductImage() {
        $product = $this->_getProduct();

        if ($product && $product->getId()) {
            return $product->getImageUrl();
        }
        return '';
    }

    /**
     * Return url of shop logo
     * 
     * @return string
     */
    protected function _getShopLogo() {
        /** @var $block Mage_Page_Block_Html_Header * */
        $block = Mage::getBlockSingleton('page/html_header');
        return $block->getLogoSrc();
    }

    /**
     * Return url of uploaded image
     * 
     * @return string
     */
    protected function _getUploadedImage($uploadedImageXpath) {
        return Mage::getSingleton('shareyourpurchase/system_config_backend_image_social')
                        ->getBaseMediaUrl() . Mage::getStoreConfig($uploadedImageXpath);
    }
    
    /**
     * Return icons set class name
     * 
     * @return string
     */
    /**
     * Get first product from collection
     * 
     * @return Mage_Catalog_Model_Product | null
     */
    /*
    protected function _getFirstProduct() {
        $products = $this->getProducts();

        if (count($products)) {
            return Mage::getModel('catalog/product')->load($products[0]->getProductId());
        }
        return null;
    }
     * 
     */

}

?>