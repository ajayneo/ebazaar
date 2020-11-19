<?php
	class Neo_Superdeals_IndexController extends Mage_Core_Controller_Front_Action {
		
		public function shopbyAction(){
			$this->loadLayout();
			$this->renderLayout();
		}
		
		/*public function redirectcustomAction(){
			if(!Mage::helper('customer')->isLoggedIn()){
				Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('superdeals/index/shopby'));
				$this->_redirect('customer/account/login');
			}
		}*/
	}
?>