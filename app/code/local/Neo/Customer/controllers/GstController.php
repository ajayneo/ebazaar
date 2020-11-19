<?php class Neo_Customer_GstController extends Mage_Core_Controller_Front_Action
{

	public function detailsAction(){
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('GST Details'));
        $this->renderLayout();	
	}

	//check password validity
    public function ispassValidAction(){
        $data = array();
        $request = $this->getRequest()->getParams();

        $email = $request['email'];

        //check customer exists or not
        $customer = Mage::getModel('customer/customer');

        $websiteId = Mage::app()->getStore()->getWebsiteId();
	    if ($websiteId) {
	        $customer->setWebsiteId($websiteId);
	    }
	    $customer->loadByEmail($email);
        $message = '';
	    if ($customer->getId()) {
        	/*$name = "COOKIE_PASS_".$customer->getId();
        	$info = array();
        	$info['settime'] = now();
        	$value = serialize($info);
        	$period = '86400';
        	$status = Mage::getModel('customer/customer')->setCookie($name,$value, $period);

        	if(!$status){
        		$customer->setPassword($customer->generatePassword());
				$customer->save();
				$customer->sendNewAccountEmail('confirmed', '', $customer->getStoreId());
        		$message = "Your password is expired. Please check your email for new password";
        		$success = false;
        	}else{
        		$success = true;
        		$message = "";
        	}*/
            $success = Mage::getModel('customer/customer')->checkpasswordExpire($customer->getId());
            if(!$success){
                $message = "Your password is expired. Please check your email for new password";
            }
	    }
        
        $data["success"] = $success;
        $data["message"] = $message;

        $jsonData = json_encode($data);

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

} ?>