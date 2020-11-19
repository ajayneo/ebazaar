<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout_Success_Lightbox
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout_Success_Lightbox 
    extends Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract {

    const XPATH_ENABLE = 'shareyourpurchase/general/lightbox_enable';

    protected function _construct() {

        if (Mage::getStoreConfig(Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract::XPATH_ENABLE)
                && Mage::getStoreConfig(self::XPATH_ENABLE)) {
            $this->setTemplate('shareyourpurchase/social_lightbox.phtml');
        }
    }

    protected function _getProduct() {
        return $this->getParentBlock()->getParentBlock()->getProduct();
    }
    
    public function getProducts() {
        
        $lastOrderId = Mage::getSingleton('checkout/session')
                   ->getLastRealOrderId();

        $orderId = Mage::getModel('sales/order')
                   ->loadByIncrementId($lastOrderId)
                   ->getEntityId();

        $order = Mage::getModel('sales/order')->load($orderId);

        $items = $order->getAllVisibleItems();

        return $items;
    }

    protected function _provideProducts() {
        return $this->getProducts();
    }
}