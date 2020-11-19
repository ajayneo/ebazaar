<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Facebook
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Facebook_Purchase extends Creatuitycorp_Shareyourpurchase_Block_Social_Button_Facebook_Abstract {

    const XPATH_ENABLE = 'shareyourpurchase/facebook_aggregate_purchase/enable';
    const XPATH_USE_TEMPLATE = 'shareyourpurchase/facebook_aggregate_purchase/use_template';
    const XPATH_TEMPLATE_CAPTION = 'shareyourpurchase/facebook_aggregate_purchase/template_capteion';
    const XPATH_TEMPLATE_TITLE = 'shareyourpurchase/facebook_aggregate_purchase/template_title';
    const XPATH_TEMPLATE_DESCRIPTION = 'shareyourpurchase/facebook_aggregate_purchase/template_description';
    const XPATH_IMAGE = 'shareyourpurchase/facebook_aggregate_purchase/post_image';
    const XPATH_IMAGE_USER = 'shareyourpurchase/facebook_aggregate_purchase/user_image';
    const XPATH_ITEMS_LIMIT = 'shareyourpurchase/facebook_aggregate_purchase/items_limit';
    const XPATH_SHOW_PRODUCT_NAME = 'shareyourpurchase/facebook_aggregate_purchase/show_product_name';
    const XPATH_SHOW_PRODUCT_PRICE = 'shareyourpurchase/facebook_aggregate_purchase/show_product_price';
    const XPATH_ICON_SIZE = 'shareyourpurchase/facebook_aggregate_purchase/icon_size';

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
     * Check if we can interate on passed iterate number
     * 
     * @param type $iteration
     * @return bool 
     */
    protected function _isLimitReached($iteration) {
        if ((Mage::getStoreConfig(self::XPATH_ITEMS_LIMIT) > 0)
                && ($iteration == Mage::getStoreConfig(self::XPATH_ITEMS_LIMIT))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return main post content
     * @return string
     */
    public function getMainText() {
        $useTemplate = $this->_useTemplate();
        $template = $this->_getTitleTemplate();

        $itemsCollection = $this->getProducts();
        $names = array();
        $i = 0;
        foreach ($itemsCollection as $item) {
            if ($this->_isLimitReached($i)) {
                break;
            }
            $names[] = $item->getName();
            $i++;
        }

        $content = str_replace(array('{items_count}', '{store_link}', '{items_names}', '{website_name}'), array(count($itemsCollection), Mage::helper('core/url')->getHomeUrl(),
            !$useTemplate && !Mage::getStoreConfig(self::XPATH_SHOW_PRODUCT_NAME) ? '' : implode(',', $names),
            Mage::app()->getWebsite()->getName()), $template
        );
        return $content;
    }

    /**
     * Return description for post
     * 
     * @return string
     */
    public function getDescription() {
        $useTemplate = $this->_useTemplate();

        $itemsCollection = $this->getProducts();
        $list = array();
        $template = $this->_getTitleTemplate();

        $i = 0;
        foreach ($itemsCollection as $item) {
            if ($this->_isLimitReached($i)) {
                break;
            }

            $price = Mage::helper('core')->currency($item->getPrice(), true, false);
            $list[] = str_replace(array('{item_name}', '{item_price}'), array(!$useTemplate && !Mage::getStoreConfig(self::XPATH_SHOW_PRODUCT_NAME) ? '' : $item->getName(),
                !$useTemplate && !Mage::getStoreConfig(self::XPATH_SHOW_PRODUCT_PRICE) ? '' : $price), $template);
            $i++;
        }

        return str_replace(array('{items_count}'), array(count($itemsCollection)), implode(' | ', $list));
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

    public function getPictureUrl() {
        return $this->_getPictureUrl(self::XPATH_IMAGE, self::XPATH_IMAGE_USER);
    }

    protected function _getProduct() {
        $products = $this->getProducts();

        if (count($products)) {
            return Mage::getModel('catalog/product')->load($products[0]->getProductId());
        }
        return null;
    }

}
