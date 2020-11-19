<?php
class Neo_Checkout_Model_Observer{
	
	public function setBlankPincode($observer){
        Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->setPostcode('');
    }
	
	public function paymentMethodIsActive($observer){
		$event = $observer->getEvent();
        $method = $event->getMethodInstance();
        $result = $event->getResult();
		$quote = $observer->getData('quote');
		foreach ($quote->getAllItems() as $item) {
    		$product = $item->getProduct();
			$product_id = $product->getId();
			if($product_id == "3266"){
				if($method->getCode() == 'cashondelivery' || $method->getCode() == 'innoviti'){
					$result->isAvailable = false;
				}
			}else{
				if($method->getCode() == 'neftrtgs'){
					$result->isAvailable = false;
				}
			}
		}
	}
}