<?php
class Neo_CouponsOnList_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isValidCustomer(){
		
		// Check if any customer is logged in or not
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
		 
		    // Load the customer's data
		    $customer = Mage::getSingleton('customer/session')->getCustomer();
		    $email = $customer->getEmail();
		    $file = fopen("var/customerlist/Customerlist.csv","r");
		    $flag = true;
		    while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
		       if($flag) { $flag = false; continue; }
		       // check customer email is in list
		       if(in_array($email,$emapData)){
		            return true;
		       }
		    }
		}

		return false;
	}
}
	 