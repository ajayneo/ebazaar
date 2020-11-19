<?php
	class Neo_Customer_Model_Customer extends Mage_Customer_Model_Customer
	{
		const XML_PATH_REGISTER_IBM_EMAIL_TEMPLATE = 'customer_ibmaccount_email_template';
		/**
		 * Override because to added emailids to get welcome emails of customers to the eb team
	     * Send corresponding email template
	     * @author Pradeep Sanku
	     * @param string $emailTemplate configuration path of email template
	     * @param string $emailSender configuration path of email identity
	     * @param array $templateParams
	     * @param int|null $storeId
	     * @return Mage_Customer_Model_Customer
	     */
	    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null)
	    { 
	        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
	        $mailer = Mage::getModel('core/email_template_mailer');
	        $emailInfo = Mage::getModel('core/email_info');
	        $emailInfo->addTo($this->getEmail(), $this->getName());
			/* email ids */
			//$bcc = array("mandar@electronicsbazaar.com","akhilesh@electronicsbazaar.com","keval@electronicsbazaar.com","yogesh@electronicsbazaar.com","sandeep.mukherjee@wwindia.com");

			//$bcc = array("yogesh@electronicsbazaar.com");   
			//$emailInfo->addBcc($bcc);  
			//$emailInfo->addBcc($bcc); 
	        $mailer->addEmailInfo($emailInfo);
			
	        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
	        $mailer->setStoreId($storeId);
	        if($template == 'customer_ibmaccount_email_template'){
	        	$mailer->setTemplateId('customer_ibmaccount_email_template');
	        }else{
	        	$mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
	        }
	        $mailer->setTemplateParams($templateParams);
	        $mailer->send();
	        return $this;
	    }
	    public function addressList(){
		$customer_session = Mage::getSingleton('customer/session');
		//if customer session exits
		if(!empty($customer_session)){
			//get customer
			$customer = $customer_session->getCustomer();
			$address_list = array();
			//get customer address
			foreach ($customer->getAddresses() as $key => $address) {
				//echo $key;
				//Zend_Debug::dump($address->debug());
				$street = $address->getStreet();

				$street_str = '';
				foreach ($street as $str_key => $value) {
					$street_str .= $value.", ";
				}
				$sub_address = rtrim($street_str,", ");

				//$sub_address = preg_replace('#\s+#',',',trim($sub_address));

				$city = $address->getCity();
				$state = $address->getRegion();
				$postcocde = $address->getPostcode();

				$address_list[$key] = $sub_address.", ".$city.", ".$state.", ".$postcocde;
			}
		}

		return $address_list;
		}

		/*
		@function: to check user is for ibm group #10
		@Auth: Mahesh Gurav
		@date: 14 Jun 18
		*/
		public function isIbmUser(){
			$ibmsale_cat_name = Mage::getStoreConfig('settings/category/name');
			if(!Mage::getSingleton('customer/session')->isLoggedIn()){
				Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl($ibmsale_cat_name));
		        $url = Mage::getUrl('customer/ibm/login');
				Mage::app()->getFrontController()
				       ->getResponse()
				       ->setRedirect($url);
		    }else{
		        $customer_id = Mage::getSingleton('customer/session')->getId();
		        $customer = Mage::getModel('customer/customer')->load($customer_id);
		        //if user is not ibm then redirect to home page
		        if($customer->getGroupId() !== '10'){
		        	Mage::app()->getFrontController()
				       ->getResponse()
				       ->setRedirect(Mage::getUrl('/'));
		        }
		    }
		}

		/*
		@Function: TO SET COOKIE 
		@Auth: Mahesh Gurav
		@Date: 16 jul 18
		*/
		/*$name= Cookie name
		$value= Cookie Value
		$period= Cookie expire date (by default the period is set as 3600 seconds)
		$path= Cookies path
		$domain= Cookies domain
		$secure= Cookies Security
		$httponly= Http only when yes*/
		
		public function setCookie($name,$value,$period){
			/**
			 * get cookie with a specific name
			 * $name = name of the cookie
			 */
			// echo "check cookie by name ".$name;
			$checkCookie = Mage::getModel('core/cookie')->get($name);
			// var_dump($checkCookie); exit;
			if(!$checkCookie){
				$cookieName = $name;
				/** set cookie */
				Mage::getModel('core/cookie')->set($name, $value, $period);

				$status = false;

				
			}else{
				$data = unserialize($checkCookie); 
				// print_r($data);
				$date_time_of_cookie_set = $data['settime'];

				/**Get cookies life time*/
				// $cookieExpires = Mage::getModel('core/cookie')->getLifetime($cookieName);

				/**Get the cookies path*/
				// $cookiePath = Mage::getModel('core/cookie')->getPath($cookieName);

				/**Get Cookies secure*/
				// $cookieSecure = Mage::getModel('core/cookie')->isSecure($cookieName);

				$status = true;

			}

			return $status;
			
		}

	public function checkpasswordExpire($customer_id){
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		if($customer->getId()){
			$customer_last_updated = $customer->getUpdatedAt(); 
			$current_time = now();
			
			$timediff = strtotime($current_time) - strtotime($customer_last_updated);
			//check time diff is less than 24 hours
			if($timediff > 86400){ 
			    // echo 'more than 24 hours';
			    //reset password and send response false
			    $customer->setPassword($customer->generatePassword());
				$customer->save();
				$customer->sendNewAccountEmail('ibmregistered', '', $customer->getStoreId());
			    $success = false;
			}else
			{
			 	// echo 'less than 24 hours';
			 	//send response true
			 	$success = true;
			}
			
		}

		return $success;
	}

	/**
     * Overridded because to set custom email template for IBM EPP Program
     * Send email with new account related information to IBM customers only
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
            'ibmregistered' => self::XML_PATH_REGISTER_IBM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('customer' => $this, 'back_url' => $backUrl), $storeId);

        return $this;
    }

}
?>