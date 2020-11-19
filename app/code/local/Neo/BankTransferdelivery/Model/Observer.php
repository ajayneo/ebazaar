<?php

class Neo_BankTransferdelivery_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Exports new orders to an xml file
     * @param Varien_Event_Observer $observer
     * @return Feed_Sales_Model_Order_Observer
     */
    public function export_new_order(Varien_Event_Observer $observer)
    {
        $method = Mage::app()->getRequest()->getPost('payment');

        if($method['method'] == 'banktransfer')
        { 
            $order = $observer->getEvent()->getOrder(); 
            $postData = Mage::app()->getRequest()->getPost('field');

            $post_data['order_id'] = $order->getId();
            $post_data['order_num'] = $order->getIncrementId();
            $post_data['delivery'] = $postData;


            $model = Mage::getModel("banktransferdelivery/delivery")
            ->addData($post_data)
            ->save();
    	}

        return $this;
    }
}
	 