<?php
class Neo_Ebautomation_OrderController extends Mage_Core_Controller_Front_Action{
	

	//order import in navision
	public function orderImportAction(){
	  	//if(Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
		$from = gmdate('Y-m-05');
		$orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id',200085834);
		/*$order->addFieldToFilter('mapped_status',0);
		$orders->addAttributeToFilter('main_table.created_at', array('from' => $from));*/
		//$orders->getSelect()->limit(1);
		print_r($orders->getColumnValues('increment_id'));
		/*foreach ($orders as $order) {
			Mage::getModel('ebautomation/order')->orderImport($order);
	    }*/
	}
}
?>