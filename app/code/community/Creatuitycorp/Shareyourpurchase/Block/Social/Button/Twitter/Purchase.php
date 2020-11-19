<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Purchase
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Purchase extends Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Abstract {

    const XPATH_ENABLE = 'shareyourpurchase/twitter_aggregate_purchase/enable';
    const XPATH_USE_TEMPLATE = 'shareyourpurchase/twitter_aggregate_purchase/use_template';
    const XPATH_TEMPLATE = 'shareyourpurchase/twitter_aggregate_purchase/template';
    const XPATH_ITEMS_LIMIT = 'shareyourpurchase/twitter_aggregate_purchase/items_limit';
    const XPATH_BUTTON_SIZE = 'shareyourpurchase/twitter_aggregate_purchase/button_size';
    const XPATH_SHOW_COUNTER = 'shareyourpurchase/twitter_aggregate_purchase/counter';
    const XPATH_SHOW_PRODUCT_NAME = 'shareyourpurchase/twitter_aggregate_purchase/show_product_name';
    const XPATH_SHOW_PRODUCT_PRICE = 'shareyourpurchase/twitter_aggregate_purchase/show_product_price';

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

        $itemsCollection = $this->getProducts();
        $list = array();
        $template = Mage::getStoreConfig($useTemplate ? self::XPATH_TEMPLATE : self::XPATH_TEMPLATE_DESCRIPTION_DEFAULT);

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
        return str_replace(array('{items_count}', '{store_link}', '{website_name}'), array(count($itemsCollection), Mage::helper('core/url')->getHomeUrl(), Mage::app()->getWebsite()->getName()), implode(' | ', $list));
    }

    protected function _useTemplate() {
        return Mage::getStoreConfig(self::XPATH_USE_TEMPLATE);
    }

    protected function _isLimitReached($iteration) {
        if ((Mage::getStoreConfig(self::XPATH_ITEMS_LIMIT) > 0)
                && ($iteration == Mage::getStoreConfig(self::XPATH_ITEMS_LIMIT))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get first of purchased products
     * 
     * @return Mage_Catalog_Model_Product|null
     */
    protected function _getProduct() {
        $products = $this->getProducts();

        if (count($products)) {
            return Mage::getModel('catalog/product')->load($products[0]->getProductId());
        }
        return null;
    }

}
