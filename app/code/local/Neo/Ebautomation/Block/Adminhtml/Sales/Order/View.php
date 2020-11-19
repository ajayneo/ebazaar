<?php 
class Neo_Ebautomation_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {
    public function  __construct() {

        parent::__construct();

        $message = Mage::helper('sales')->__('Are you sure you want to process this order?');
        $url = Mage::getUrl('ebautomation/index/processOrderForShipment/');
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        $orderProcessUrl = Mage::getUrl('ebautomation/index/processOrderForShipment/order_id/'.$orderId);
        $navisionOrderProcessUrl = Mage::getUrl('ebautomation/index/processOrderInNavision/order_id/'.$orderId);

        $status = $order->getStatus();
        $allowed_status = array('financeapproved','codverified','banktransferapproved','pendingbilldesk');

        if($order->canShip() && in_array($status, $allowed_status)){
        	$this->_addButton('shipement_process', array(
	            'label'     => Mage::helper('ebautomation')->__('Process Order for Shipment'),
	            'onclick' => "confirmSetLocation('{$message}', '{$orderProcessUrl}')",
	            'class'     => 'go'
	       		 ), 0, 100, 'header', 'header');
        }
        
        if($order->hasShipments()){
            $this->_addButton('navision_process', array(
                'label'     => Mage::helper('ebautomation')->__('Push to Navision'),
                'onclick' => "confirmSetLocation('{$message}', '{$navisionOrderProcessUrl}')",
                'class'     => 'go'
                 ), 0, 200, 'header', 'header');
        }
        
    }
}
?>