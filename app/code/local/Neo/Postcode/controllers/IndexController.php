<?php
class Neo_Postcode_IndexController extends Mage_Core_Controller_Front_Action{
    
	public function checkPostcodeAction(){
		$postcode = $this->getRequest()->getPost('postcode');
		$loadpostcode = Mage::getModel('postcode/postcode')->getCollection()->addFieldToFilter('status',1)->addFieldToSelect('postcode')->getData();
		$postcode_array = array();
		foreach($loadpostcode as $pincode){
			$postcode_array[] = $pincode['postcode'];
		}
		echo "<pre>"; print_r($postcode_array); exit;
		$response = array();
		if(in_array($postcode,$postcode_array)){
			$response['status'] = 'SUCCESS';
			$response['message'] = $this->__('Can be Delivered');
		}else{
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Cannot be Delivered');
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
	}
    
}