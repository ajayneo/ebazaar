<?php
class Neo_Trackorder_Block_Details extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	/*
	@author Nikhil K R
	@date 21-8-13
	@description	Filters customers order depending on its Id
	*/
	
	public function getCustomerOrderId()
	{
		$customerid = Mage::getSingleton('customer/session')->getCustomer()->getId();		
		$orderCollection = Mage::getModel('sales/order')->getCollection()
    						->addFieldToFilter('customer_id', array('eq' => array($customerid)));
      	return $orderCollection;
	}	
	/*
	@author Nikhil K R
	@date 21-8-13
	@description	Sends Order details
	*/
	public function getOrderdetails()
	{
		$incr_id = $this->getRequest()->getParam('order_id');
		$orderObj = Mage::getModel('sales/order')->loadByIncrementId($incr_id);
		return $orderObj;
	}
	/*
	@author Nikhil K R
	@date 21-8-13
	@description	Sends tracker details
	*/
	public function getTrackerdetails($data){
		$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
							->setOrderFilter($data)
							->load();
		return $shipmentCollection;
	}
}