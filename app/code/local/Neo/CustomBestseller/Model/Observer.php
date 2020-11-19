<?php
class Neo_CustomBestseller_Model_Observer
{

			public function loginredirection(Varien_Event_Observer $observer)
			{
				//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
				//$user = $observer->getEvent()->getUser();
				//$user->doSomething();
				// Mage::getSingleton('core/session')->unsFacebookRedirect();
				// Mage::getSingleton('core/session')->unsTwitterRedirect();
				// Mage::getSingleton('core/session')->unsGoogleRedirect();
				// Mage::getSingleton('core/session')->unsGoogleCsrf();
				// Mage::getSingleton('core/session')->unsFacebookCsrf();

				// print_r(Mage::getSingleton('core/session')->getData()); exit;
				// if(Mage::getSingleton('core/session')->getRedirectFlag() != 'other'){
				// 	Mage::app()->getResponse()->setRedirect(Mage::getSingleton('core/session')->getRedirectto());
				// 	Mage::getSingleton('core/session')->unsRedirectto();
				// }
			}
		
}
