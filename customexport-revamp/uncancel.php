<?php
//added on 11 Sept 2017
//Auth Mahesh Gurav 
require_once('../app/Mage.php'); 
Mage::app();


try{
		
		$increment_id = 200076378;

		if($increment_id){
			$_order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
			// $_order->setState(Mage_Sales_Model_Order::STATE_NEW, true);  
			$_order->setState(Mage_Sales_Model_Order::STATE_NEW, true);  
			$_order->setStatus('pendingbilldesk');
			$history = $_order->addStatusHistoryComment('Undo Cancel request by Keval Chokshi', false);
			$history->setIsCustomerNotified(false);
			$_order->save();
			$history->save();
			print_r($_order->getData());
			exit;

		}
		
}catch(Exception $e){
	echo $e->getMessage();
}