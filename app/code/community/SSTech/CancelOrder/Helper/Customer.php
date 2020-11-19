<?php
class SSTech_CancelOrder_Helper_Customer
    extends Mage_Core_Helper_Abstract
{
    const XML_PATH_CANCEL_NEW     = 'cancelorder/customer/cancel_new';
    const XML_PATH_CANCEL_PENDING = 'cancelorder/customer/cancel_pending';
    const XML_PATH_CANCEL_PROCESSING = 'cancelorder/customer/cancel_processing';
    const XML_PATH_CANCEL_STATUS  = 'cancelorder/customer/cancel_status';

    public function canCancelNew($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANCEL_NEW, $store);
    }

    public function canCancelPending($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANCEL_PENDING, $store);
    }

    public function canCancelProcessing($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANCEL_PROCESSING, $store);
    }

    public function getCancelStatus($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CANCEL_STATUS, $store);
    }

    public function canCancelApp(Mage_Sales_Model_Order $order)
    {
        if ($order->getState() == 'new' || $order->getState() == 'processing') {
            return 1; 
        }
       
        if (!in_array($order->getState(), Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates(), $strict = true)) {
            return 0;
        }

        if ($order->getStatus() == Mage_Sales_Model_Order::STATE_PROCESSING && $this->canCancelProcessing($order->getStore()) ) {
            return 1;
        }

        if (!$order->canCancel() || $order->hasInvoices() || $order->hasShipments()) {
            return 0;
        }

        if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW && $this->canCancelNew($order->getStore())) {
            return 1;
        }

        if ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT && $this->canCancelPending($order->getStore())) {
            return 0;
        }

        return 1;
    }

    public function canCancel(Mage_Sales_Model_Order $order)
    {
        if ($order->getState() == 'new' || $order->getState() == 'processing') {
            return true; 
        }
        if ($order->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
            return false;
        }        
        if (!in_array($order->getState(), Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates(), $strict = true)) {
            return false;
        }

        if ($order->getStatus() == Mage_Sales_Model_Order::STATE_PROCESSING && $this->canCancelProcessing($order->getStore()) ) {
            return true;
        }

        if (!$order->canCancel() || $order->hasInvoices() || $order->hasShipments()) {
            return false;
        }

        if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW && $this->canCancelNew($order->getStore())) {
            return true;
        }

        if ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT && $this->canCancelPending($order->getStore())) {
            return true;
        }

        return false;
    }
}
