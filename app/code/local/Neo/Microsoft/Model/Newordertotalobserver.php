<?php
class Neo_Microsoft_Model_Newordertotalobserver
{
	 public function saveMicrosoftdiscountTotal(Varien_Event_Observer $observer)
    {
         $order = $observer -> getEvent() -> getOrder();
         $quote = $observer -> getEvent() -> getQuote();
         $shippingAddress = $quote -> getShippingAddress();
         if($shippingAddress && $shippingAddress -> getData('microsoftdiscount_total')){
             $order -> setData('microsoftdiscount_total', $shippingAddress -> getData('microsoftdiscount_total'));
             }
        else{
             $billingAddress = $quote -> getBillingAddress();
             $order -> setData('microsoftdiscount_total', $billingAddress -> getData('microsoftdiscount_total'));
             }
         $order -> save();
     }
    
     public function saveMicrosoftdiscountTotalForMultishipping(Varien_Event_Observer $observer)
    {
         $order = $observer -> getEvent() -> getOrder();
         $address = $observer -> getEvent() -> getAddress();
         $order -> setData('microsoftdiscount_total', $shippingAddress -> getData('microsoftdiscount_total'));
		 $order -> save();
     }
}