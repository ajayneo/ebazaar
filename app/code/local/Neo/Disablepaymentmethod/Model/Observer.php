<?php
class Neo_Disablepaymentmethod_Model_Observer
{

			public function filterpaymentmethods(Varien_Event_Observer $observer)
			{

				Mage::log('sandeep filterpaymentmethods',null,'filterpaymentmethods.log');
				//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
				//$user = $observer->getEvent()->getUser();
				//$user->doSomething();
			}
		
}
 