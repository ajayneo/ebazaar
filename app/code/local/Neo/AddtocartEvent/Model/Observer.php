<?php
class Neo_AddtocartEvent_Model_Observer
{

			public function beforeaddtocart(Varien_Event_Observer $observer)
			{
				//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
				//$user = $observer->getEvent()->getUser();
				//$user->doSomething();
				$session =  Mage::getSingleton('customer/session');
				if(!$session->isLoggedIn()){
					//---------- add to cart code starts -------------------
					$base_url = rtrim(Mage::getBaseUrl(),"/");
					if(in_array($_SERVER['HTTP_HOST'], array('150.242.140.40'))){
						$request_uri =  'http://'.$_SERVER['HTTP_HOST'].Mage::app()->getRequest()->getRequestUri();
					}else{
						$request_uri = $base_url.Mage::app()->getRequest()->getRequestUri();
					}
					// echo $request_uri; exit;
					$session->setAfterAuthUrl($request_uri);
					//---------------------- code ends ---------------------
					$redirect_url = Mage::getUrl('customer/account/login/');
	      			Mage::app()->getFrontController()->getResponse()->setRedirect($redirect_url)->sendResponse();
	      			Mage::getSingleton('core/session')->addError('Please login for adding product to cart');
	      			exit;
      			} 				
			}
		
}
