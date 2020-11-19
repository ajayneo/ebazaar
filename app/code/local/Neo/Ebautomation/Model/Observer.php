<?php
class Neo_Ebautomation_Model_Observer
{
			public function setStatus() {
		        Mage::log("WORKS!",null,'indexing_cron.log',true);
		    }

			function customerPlaceOrderLog(Varien_Event_Observer $observer){
				$order = $observer->getEvent()->getOrder();
				

				if($order->getPayment()->getMethod() == 'banktransfer'){
					$customerId = $order->getCustomerId();
					$credit = Mage::getModel('ebautomation/customer')->getCreditLimitLog($customerId); 
					$credit = json_encode($credit);
					$order->addStatusHistoryComment('Credit Log From Navision :'.$credit, false);
			 		$order->save();
				}
			}
}
