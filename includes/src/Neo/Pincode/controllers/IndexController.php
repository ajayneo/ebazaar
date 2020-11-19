<?php
    class Neo_Pincode_IndexController extends Mage_Core_Controller_Front_Action
    {
		public function checkPostcodeAction(){
			$postcode = $this->getRequest()->getPost('postcode');

			$configValue = Mage::getStoreConfig('pincode_options/pincode_check/pincode');
			$pincode_array = array();
			$pincode_array = explode(',',$configValue);

			$response = array();
			if(in_array($postcode,$pincode_array)){
				$response['status'] = 'SUCCESS';
				$response['message'] = $this->__('Delivery available in your location.');
			}else{
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Delivery not available in your location yet.');
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
    } 
?>
