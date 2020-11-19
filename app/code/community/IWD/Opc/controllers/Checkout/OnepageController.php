<?php
$iwd_av_class = false;

if (! Mage::helper ( 'opc' )->isEnable ()) {
	// check if IWD AddressValidation exists
	$path = Mage::getBaseDir ( 'app' ) . DS . 'code' . DS . 'local' . DS;
	$file = 'IWD/AddressVerification/controllers/OnepageController.php';
	// load IWD OPC class
	if (file_exists ( $path . $file )){		
		// check if IWD AV enabled
		if (Mage::helper ( 'addressverification' )->isAddressVerificationEnabled ()){
			$iwd_av_class = true;
		}
	}
}

if (! $iwd_av_class) {
	require_once Mage::getModuleDir ( 'controllers', 'Mage_Checkout' ) . DS . 'OnepageController.php';
	class IWD_Opc_Checkout_OnepageController extends Mage_Checkout_OnepageController {
		
		/**
		 * Checkout page
		 */
		public function indexAction() {
			$scheme = Mage::app ()->getRequest ()->getScheme ();
			if ($scheme == 'http') {
				$secure = false;
			} else {
				$secure = true;
			}
			
			if (Mage::helper ( 'opc' )->isEnable ()) {
				$this->_redirect ( 'onepage', array (
						'_secure' => $secure 
				) );
				return;
			} else {
				parent::indexAction ();
			}
		}


		/*
		* @author : Sonali Kosrabe
		* @purpose : To get delivery date on product details page
		* @dates : 21-dec-2017
		*/
		public function checkPostcodeAction(){
			$pincode = $this->getRequest()->getPost('postcode');
			$method = $this->getRequest()->getPost('method');
			if($pincode){
			 	$estimatedDelivery = Mage::getModel('operations/serviceablepincodes')->getProductDeliveryOnDetailsPage($pincode);
			 
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($estimatedDelivery));
				return;
			}else{
				$response['status'] = 0;
				$response['message'] = $this->__('Delivery not available in your location yet.');
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;
			}
		}
	}
} else {
	require_once Mage::getModuleDir ( 'controllers', 'IWD_AddressVerification' ) . DS . 'OnepageController.php';
	class IWD_Opc_Checkout_OnepageController extends IWD_AddressVerification_OnepageController {
	}
}
