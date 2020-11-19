<?php
class Neo_Affiliatecommision_Model_Newordertotalobserver
{
	 public function saveAffiliatecommisionTotal(Varien_Event_Observer $observer)
    {
         $order = $observer -> getEvent() -> getOrder();
         $quote = $observer -> getEvent() -> getQuote();
         $shippingAddress = $quote -> getShippingAddress();
         if($shippingAddress && $shippingAddress -> getData('affiliatecommision_total')){
             $order -> setData('affiliatecommision_total', $shippingAddress -> getData('affiliatecommision_total'));
             }
        else{
             $billingAddress = $quote -> getBillingAddress();
             $order -> setData('affiliatecommision_total', $billingAddress -> getData('affiliatecommision_total'));
             }
         $order -> save();
     }
    
     public function saveAffiliatecommisionTotalForMultishipping(Varien_Event_Observer $observer)
    {
         $order = $observer -> getEvent() -> getOrder();
         $address = $observer -> getEvent() -> getAddress();
         $order -> setData('affiliatecommision_total', $shippingAddress -> getData('affiliatecommision_total'));
		 $order -> save();
     }
}