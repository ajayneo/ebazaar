<?php
/**
 * Short description for file
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2008-2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 * @since      File available since Revision 1
 */

class Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Sales_Order_View 
    extends Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract {
    
    const XPATH_USER_SALES_HISTORY_ENABLE = 'shareyourpurchase/general/user_sales_history_enable';

    protected function _isEnabled() {
        return parent::_isEnabled() 
                && Mage::getStoreConfig(self::XPATH_USER_SALES_HISTORY_ENABLE);
    }
    
    /**
     * Get order object
     * 
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder() {
        $orderId = $this->getRequest()->getParam('order_id');

        /** $model Mage_Sales_Model_Order */
        return Mage::getModel('sales/order')->load($orderId);
    }
    
    /**
     * Get products from order
     * 
     * @return array
     */
    protected function _provideProducts() {
        return $this->_getOrder()->getAllVisibleItems();
    }
    
    public function getOrderIncrementId() {
        return $this->_getOrder()->getIncrementId();
    }
    
}

