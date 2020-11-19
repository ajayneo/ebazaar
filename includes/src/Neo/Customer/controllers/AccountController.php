<?php
	require_once 'Mage/Customer/controllers/AccountController.php';
    class Neo_Customer_AccountController extends Mage_Customer_AccountController
    {
    	/**
	     * Create customer account action
		 * overridden for Update redirection to login page itself when their is any error
		 * overridden by pradeep sanku
		 * On 26th March 2015
	     */
	    public function createPostAction()
	    {
	        /** @var $session Mage_Customer_Model_Session */
	        $session = $this->_getSession();
	        if ($session->isLoggedIn()) {
	            $this->_redirect('*/*/');
	            return;
	        }
	        $session->setEscapeMessages(true); // prevent XSS injection in user input
	        if (!$this->getRequest()->isPost()) {
	            $errUrl = $this->_getUrl('*/*', array('_secure' => true)); // changed line
	            $this->_redirectError($errUrl);
	            return;
	        }
	
	        $customer = $this->_getCustomer();
	
	        try {
	            $errors = $this->_getCustomerErrors($customer);
	
	            if (empty($errors)) {
	            	$customerData = $this->getRequest()->getPost();

	            	if($customerData['group_id'] == 4) 
	            	{
	            		for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
		    				$randomNumber .= mt_rand(0, 9);
						}

		            	$customer->setGroupId($customerData['group_id']);
		            	$customer->setAsmMap($customerData['asm_id']); 

		            	$customer->setCusCountry('IN');  
		            	$customer->setCusState($customerData['cus_state']);  
		            	$customer->setCusCity($customerData['cus_city']);  
		            	$customer->setAffiliateStore($customerData['affiliate_store']);  
		            	$customer->setLandmark($customerData['landmark']);  
		            	//$customer->setAffiliateStore('TEST STORE');
		            	$customer->setIsaffiliate(1);
		            	$customer->setRepcode('Aff_'.$customerData['firstname'].'_'.$randomNumber);
	            	} 
	                $customer->save();
	                $this->_dispatchRegisterSuccess($customer);
	                $this->_successProcessRegistration($customer);
	                return;
	            } else {
	                $this->_addSessionError($errors);
	            }
	        } catch (Mage_Core_Exception $e) {
	            $session->setCustomerFormData($this->getRequest()->getPost());
	            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
	                $url = $this->_getUrl('customer/account/forgotpassword');
	                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
	                $session->setEscapeMessages(false);
	            } else {
	                $message = $e->getMessage();
	            }
	            $session->addError($message);
	        } catch (Exception $e) {
	            $session->setCustomerFormData($this->getRequest()->getPost())
	                ->addException($e, $this->__('Cannot save the customer.'));
	        }
	        $errUrl = $this->_getUrl('*/*', array('_secure' => true)); // changed line
	        $this->_redirectError($errUrl);
	    }
    }
?>