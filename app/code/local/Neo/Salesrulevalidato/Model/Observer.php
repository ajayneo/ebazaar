<?php
class Neo_Salesrulevalidato_Model_Observer
{

	public function salesruleValidatorProcess(Varien_Event_Observer $observer)
	{
		$ruleCode = $observer->getEvent()->getRule()->getCode();
		//echo $ruleCode;exit; 
		 
		 if($ruleCode == 'NEWSIGNUP200')//test
		 {
		 	$customerData = $observer->getEvent()->getQuote()->getCustomer();		 	

		 	//echo "<pre>";
		 	//print_r($customerData->getData()); //entity_id
		 	//exit;

		 	if($customerData->getEntityId() < 17941){
		 		$event = $observer->getEvent();
		        $result = $event->getResult();
		        $result->setBaseDiscountAmount(0)->setDiscountAmount(0);

		        //Mage::throwException('Customer Created after December 5, 2016 can only apply for this coupon');
		 	}	
	 	}
	}
		
}
 