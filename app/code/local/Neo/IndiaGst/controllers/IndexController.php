<?php 
class Neo_IndiaGst_IndexController extends Mage_Core_Controller_Front_Action{
	public function IndexAction(){
		$request = $_POST;
		$validation = Mage::helper('indiagst')->validateGstin($request);

		
		if(!empty($validation)){
			Mage::getSingleton('core/session')->addError($validation['message']);
			Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/gst/details'))->sendResponse();
			exit;
		}
		$saveData = array();
		$customer_id = '';
		$address_str = '';
		$gstin = '';
		try{
				$model = Mage::getModel('indiagst/indiagst');
			if(!empty($request['email']) && !empty($request['gstin']) && !empty($request['pan'])){
				//get customer id
				$customer = Mage::getModel("customer/customer"); 
		        $customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
		        $customer->loadByEmail($request['email']);
				
				if($customer){
					$cutomer_id = $customer->getEntityId();
				}
					
				$collection = Mage::getResourceModel('indiagst/gstdetails_collection');
				$collection->addFieldToFilter('email',array('eq'=>$request['email']));
				
				if($collection->getData()){
					$message = "GST Details are already updated";
					$status = 0;
				}else if($customer_id == '0'){
					$message = "Please Sign Up";
					$status = 0;
				}else{
					$saveData['partner_store_name'] = strip_tags($request['partner_store_name']);
					$saveData['director_name'] = strip_tags($request['director_name']);
					$saveData['contact_name'] = strip_tags($request['contact_name']);
					$saveData['address_line_1'] = strip_tags($request['address_line_1']);
					$saveData['address_line_2'] = strip_tags($request['address_line_2']);
					$saveData['country'] = strip_tags($request['country']);
					$saveData['state'] = $request['state'];
					$saveData['mobile'] = $request['mobile'];
					$saveData['postcode'] = $request['postcode'];
					$saveData['city'] = $request['city'];
					$saveData['customer_id'] = $cutomer_id;
					$saveData['email'] = $request['email'];
					$saveData['gstin'] = $gstin = $request['gstin'];
					$saveData['pan'] = $request['pan'];
					$currentTime = Varien_Date::now();
					$saveData['created_time'] = $currentTime;
					$saveData['update_time'] = $currentTime;
					$model->setData($saveData);
					$model->save();

					$address_str = $saveData['address_line_1'];
					if($saveData['address_line_2']){
						$address_str .= ', '.$saveData['address_line_2'];
					}

					if($customer){
						$customer->setGstin($request['gstin']);
						$customer->setPincode($request['postcode']);
						$customer->setState($request['state']);
						$customer->setCity($request['city']);
						$customer->setAddress($address_str);
						$customer->save();

						//update default address
				    	$customerAddress = Mage::getModel('customer/address');
				    	if ($defaultShippingId = $customer->getDefaultShipping()){
						    $customerAddress->load($defaultShippingId);
					    	$customerAddress->setGstin($gstin);
						    $customerAddress->save(); 
						}
						if ($defaultBillingId = $customer->getDefaultBilling()){
						    $customerAddress->load($defaultBillingId);
					    	$customerAddress->setGstin($gstin);
					    	$customerAddress->save(); 
						}
					}

					$message = "GST Details are successfully updated";
					$status = 1;
				}
				
			}

		}catch(Exception $e){
			$status = 0;
			$message = $e->getMessage();
		}

		// echo json_encode(array('status'=>$status,'message'=>$message));
		if($status == 1){
			Mage::getSingleton('core/session')->addSuccess($message);
		}else{
			Mage::getSingleton('core/session')->addError($message);
		}
		Mage::app()->getResponse()->setRedirect(Mage::getUrl('onepage'))->sendResponse();
		exit;
	}

	public function gstinAction(){
		$this->loadLayout();   
        $this->getLayout()->getBlock("head")->setTitle($this->__("GST IN"));
        $this->renderLayout();
	}


}?>