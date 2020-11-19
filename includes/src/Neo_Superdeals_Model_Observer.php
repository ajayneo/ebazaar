<?php
class Neo_Superdeals_Model_Observer{
	public function notifySuperdealsCustomerLogin($observer){
		$beforeAuthUrl = Mage::getSingleton('customer/session')->getMyCustomAuthUrl();
		if($beforeAuthUrl){
			Mage::getSingleton('customer/session')->unsMyCustomAuthUrl();
			Mage::getSingleton('customer/session')->setBeforeAuthUrl($beforeAuthUrl);
		}
	}
}
