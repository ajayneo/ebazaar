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
                        // Mage::log(print_r($customerData),null,'testcustomer.log',true);

                    //if($customerData['group_id'] == 4){
                        $customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$customerData['mobile']);
 
                        if(count($customerCount) > 0)
                        {
                            $session->setCustomerFormData($this->getRequest()->getPost());
                            $error = Mage::helper('customer')->__('There is already an account with this mobile number.');
                            Mage::throwException($error);
                        }

                        $session_ebpin = Mage::getSingleton('core/session')->getEBPin();
                        

                        if($session_ebpin[$customerData['mobile']] != $customerData['cus_otp'])
                        {
                            $session->setCustomerFormData($this->getRequest()->getPost());
                            
                            $error = Mage::helper('customer')->__('Otp Entered by you is not correct.');
                            Mage::throwException($error);
                        }


                        for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
                            $randomNumber .= mt_rand(0, 9); 
                        }

                        $name = explode(' ', $customerData['firstname']);
                        if($name[1]){
                        }else{
                            $name[1] = $name[0];  
                        }

                        //city region
                        $pincode = 0;
                        $city = '';
                        $region_id = '';
                        
                        if(!empty($customerData['pincode']) && strlen($customerData['pincode']) == 6){
                            $pincode = $customerData['pincode']; 
                            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                            $query = 'SELECT r.region_id, c.city FROM `directory_country_region` as r LEFT JOIN `city_pincodes` as c ON c.state_code = r.code WHERE c.`pincode` ='. $pincode;
                            $result = $connection->fetchRow($query);
                            
                            if(!empty($result)){
                                $city = $result['city'];
                                $region_id = $result['region_id'];
                            }else{
                                $session->setCustomerFormData($this->getRequest()->getPost());
                                $error = Mage::helper('customer')->__('The entered PINCODE is not serviceable.');
                                Mage::throwException($error);
                            }

                        }else{
                            $session->setCustomerFormData($this->getRequest()->getPost());
                            $error = Mage::helper('customer')->__('Please enter a valid PINCODE.');
                            Mage::throwException($error);
                        }

                        $store_name = '';
                        $repcode = '';
                        if(!empty($customerData['firstname'])) {
                            $store_name = $customerData['firstname'];
                            list($repcode) = explode(' ', $store_name, 2);
                            $repcode = preg_replace('/[^A-Za-z0-9\-]/', '', $repcode);
                        }else{
                            $session->setCustomerFormData($this->getRequest()->getPost());
                            $error = Mage::helper('customer')->__('Please enter Store Name.');
                            Mage::throwException($error);
                        }


                        // $customer->setGroupId($customerData['group_id']);
                        $customer->setGroupId(4);
                        $customer->setDeviceType('Website');    

                        $customer->setFirstname($customerData['firstname']);     
                        $customer->setLastname('.');  

                        $customer->setAffiliateStore($store_name); 
                        $customer->setStoreName($store_name); 
                        $customer->setCusCountry('IN');  
                        $customer->setCusState($region_id);  
                        $customer->setCusCity($city);  
                        $customer->setPincode($pincode);  
                        $customer->setMobile($customerData['mobile']);  
                        // $customer->setAffiliateStore($customerData['affiliate_store']);  
                        $customer->setBusinessType($customerData['business_type']);  
                        // $customer->setLandmark($customerData['landmark']);  
                        $customer->setIsaffiliate(1);
                        $customer->setAsmMap($customerData['asm_id']); 
                        $customer->setRepcode('Aff_'.$name[0].'_'.$randomNumber);
                    //} 
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
                    Mage::log("customer save : ".$e->getMessage(),null,'customer_error.log',true);
            }
            $errUrl = $this->_getUrl('*/*', array('_secure' => true)); // changed line
            $this->_redirectError($errUrl);
        }

        /**
     * Login post action
     */
    public function loginPostLenovoAction()
    {
        
        $session = $this->_getSession();

        $login = $this->getRequest()->getPost('login');

        $url = Mage::getBaseUrl().'gadget/';
        $fail_url = Mage::getBaseUrl().'customer/account/login/';
        // exit('syg');
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');

            //code for corporate user store id validate
            if($this->getRequest()->getParam('group') == 'corporate'){

                if(!empty($login['corporate_store_id'])){
                    $customerModel = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($login['username']);
                    $table_store_id = (string) $customerModel->getCorporateStoreId();
                    $request_store_id = (string) $login['corporate_store_id'];
                    if($table_store_id !== $request_store_id){
                        $session->addError($this->__('Corporate Store Id does not match with account.'));
                        // $this->_redirect($fail_url);
                        Mage::app()->getResponse()->setRedirect($fail_url)->sendResponse();
                        return;
                    }
                }else{
                    $session->addError($this->__('Corporate Store Id is required.'));
                        // $this->_redirect($fail_url);
                        Mage::app()->getResponse()->setRedirect($fail_url)->sendResponse();
                    // $this->_redirect('*/*/');
                    return;
                }
            }
            
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    
                    $session->login($login['username'], $login['password']); 
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        Mage::app()->getResponse()->setRedirect($url)->sendResponse();
                    }

                    Mage::app()->getResponse()->setRedirect($url)->sendResponse();
                } catch (Mage_Core_Exception $e) {  
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }  
        }

        // Mage::app()->getResponse()->setRedirect($fail_url)->sendResponse();
        $this->_loginPostRedirect();
    }


    public function loginPostbackupAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    
                    $session->login($login['username'], $login['password']); 
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {  
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }

        $this->_loginPostRedirect();
    }


        /**
         * Login post action
         */
        public function loginPostAction()
        {
            
            $login = $this->getRequest()->getPost('login');
            $session = Mage::getSingleton('customer/session');
        //     var_dump($session->getBeforeAuthUrl());
        // var_dump($session->getAfterAuthUrl());
        // echo $_SERVER['HTTP_REFERER'];
        // echo $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
        // exit('check');
            $customer = Mage::getModel('customer/customer')->setWebsiteId(1);
            try
            {
                if($customer->authenticate($login['username'], $login['password'])) 
                { 
                   $session->setCustomerAsLoggedIn($customer);
                   //Mage::log(Varien_Debug::backtrace(true, true), null, 'backtrace.log');
                   //$message = $this->__('You are now logged in as %s', $customer->getName());
                   //$session->addNotice($message);  
                }
                else
                {
                    throw new Exception ($this->__('Invalid login or password.'));
                }
            }
            catch (Exception $e)
            {
                $session->addError($e->getMessage());
            }

            // $this->_redirect('customer/account');
            $this->_loginPostRedirect();
        }

        /**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl($this->_getHelper('customer')->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                    Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                )) {
                    $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        // Rebuild referer URL to handle the case when SID was changed
                        $referer = $this->_getModel('core/url')
                            ->getRebuiltUrl( $this->_getHelper('core')->urlDecode($referer));
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }else{
                        if ($session->getAfterAuthUrl()) {
                            $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl( $this->_getHelper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() ==  $this->_getHelper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl( $this->_getHelper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        // if (!$session->isLoggedIn()) {
        //     $session->setBeforeAuthUrl( $this->_getHelper('customer')->getLoginUrl());
        // }else{
        //     if(Mage::getSingleton('core/session')->getRedirectFlag() != 'other'){
        //         $session->setBeforeAuthUrl(Mage::getSingleton('core/session')->getRedirectto());
        //         Mage::getSingleton('core/session')->unsRedirectto();
        //     }
        // }
        
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }
    
    public function savepartnerstoreAction(){
        $partner_store = $_POST['affliate'];
        $gstin = $_POST['gstin'];
        //validate gstin
        $result = array();
        if(!empty($gstin)){
            // var_dump(strlen($gstin));
            $str_len = (int) strlen($gstin);
            if($str_len == 15){
                //continue;
            }else{
                $result['status'] = 0;
                $result['message'] = 'GST IN length is invalid';
                echo json_encode($result);
                exit;
            }
        }
        
        if(!empty($partner_store) || !empty($gstin)){



            $customer_id = Mage::getSingleton('customer/session')->getCustomerId();

            //update customer
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            if(!empty($partner_store)){
                $customer->setAffiliateStore($partner_store);
            }

            if(!empty($gstin)){
                $customer->setGstin($gstin);
            }

            $customer->save();


            //update default address
            $customerAddress = Mage::getModel('customer/address');
            if ($defaultShippingId = $customer->getDefaultShipping()){
                $customerAddress->load($defaultShippingId);
                if(!empty($partner_store)){
                    $customerAddress->setCompany($partner_store);
                }

                if(!empty($gstin)){
                    $customerAddress->setGstin($gstin);
                }

                $customerAddress->save(); 
            }
            if ($defaultBillingId = $customer->getDefaultBilling()){
                 $customerAddress->load($defaultBillingId);
                 if(!empty($partner_store)){
                    $customerAddress->setCompany($partner_store);
                }

                if(!empty($gstin)){
                    $customerAddress->setGstin($gstin);
                }
                    $customerAddress->save(); 
            }

            //update quote
            $checkout = Mage::getSingleton('checkout/session')->getQuote();
            $billing_id =  $checkout->getBillingAddress()->getAddressId();
            $shipping_id =  $checkout->getShippingAddress()->getId();
            
            $quote_billing = Mage::getModel('sales/quote_address')->load($billing_id);
            
            if(!$quote_billing->getCompany() && !empty($partner_store)){
                $quote_billing->setCompany($partner_store);
            }

            if(!$quote_billing->getGstin() && !empty($gstin)){
                $quote_billing->setGstin($gstin);
            }

            $quote_billing->save();
            
            $quote_shipping = Mage::getModel('sales/quote_address')->load($shipping_id);
            
            if(!$quote_shipping->getCompany() && !empty($partner_store)){
                $quote_shipping->setCompany($partner_store);
            }

            if(!$quote_shipping->getGstin() && !empty($gstin)){
                $quote_shipping->setGstin($gstin);
            }

            $quote_shipping->save();
            
            $quote_model = Mage::getModel('sales/quote');
            $quote_model->collectTotals();

            $result['status'] = 1;
            $result['message'] = 'Data updated successfully for your address';
            echo json_encode($result);
            exit;
        }
        
        exit;
    }



    public function savegstinAction(){
        try{
            $request = $mobile = $this->getRequest()->getParams();
            $_customer = Mage::getModel('customer/customer');
            if(Mage::getSingleton('customer/session')){
                $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
                
                $_customer->load($customer_id);
                if($_customer->getGstin() && $_customer->getGstin() == $request['gstin']){
                    $result['status'] = 0;
                    $result['message'] = 'Your GST IN '.$request['gstin'].' is already updated ';
                }else if($_customer->getEmail() == $request['email']){
                    $_customer->setGstin($request['gstin']);
                    $_customer->save();
                    $result['status'] = 1;
                    $result['message'] = 'Successfully updated GST IN '.$_customer->getGstin();
                    
                }else{
                    $result['status'] = 0;
                    $result['message'] = 'Please sign in using email id '.$request['email'];
                }
            }else if(!empty($request['email'])){

                $_customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
                $_customer->loadByEmail($request['email']);


                if($_customer->getGstin() && $_customer->getGstin() == $request['gstin']){
                    $result['status'] = 0;
                    $result['message'] = 'Your GST IN '.$request['gstin'].' is already updated ';
                }else if($_customer->getData()){
                    $_customer->setGstin($request['gstin']);
                    $_customer->save();
                    $result['status'] = 1;
                    $result['message'] = 'Successfully updated GST IN '.$_customer->getGstin();
                }else{
                    $result['status'] = 0;
                    $result['message'] = 'Please sign up using email id '.$request['email'];
                }
            }else{
                $result['status'] = 0;
                $result['message'] = 'Unable to update GST IN, please sign up';
            }
        }catch(Exception $e){
                    $result['status'] = 0;
                    $result['message'] = $e->getMessage();
        }
        echo json_encode($result);
            exit;
    }

    //customer login redirect for sell your gadget
    /**
     * Customer login form page
     */
    public function loginAction()
    {
        //Code modified to fix redirect issue on 16 Feb 2018 Mahesh Gurav
        $session = Mage::getSingleton('customer/session');
        //revised to add if condition on getting shublabh module redirect 09 Mar 2018 Mahesh Gurav
        if(!$session->getAfterAuthUrl()){
            $session->setAfterAuthUrl(Mage::helper('core/http')->getHttpReferer());
            $session->setBeforeAuthUrl('');
        }
        //-------------------------------------- 16 Feb 2018
        
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        $sygform = Mage::getSingleton('core/session')->getSygform();
        if (strpos($lastUrl, 'gadget') !== false || !empty($sygform) || strpos($lastUrl, 'loginPostLenovo') !== false) {
            Mage::getSingleton('core/session')->setSyglogin(true);
            $session->setAfterAuthUrl(Mage::getUrl('gadget'));
            $session->setBeforeAuthUrl('');
        }else{
            Mage::getSingleton('core/session')->setSyglogin(false);
        }
        // For shubh labh credit redirection
        // if (strpos($lastUrl, 'shubh-labh') !== false){
        //     Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('suvidha/index/index'));
        // }

        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    /**
     * Customer logout action
     */
    public function logoutAction()
    {
        $group_id = 0;
        if(Mage::getSingleton('customer/session')){
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $group_id = $customer->getGroupId();
            // if($customer->getEmail() == 'web.maheshgurav@gmail.com') echo $group_id; exit;
        }

        $this->_getSession()->logout()
            ->renewSession();
        Mage::getSingleton('core/session')->unsSygform();
        $path = null;
        $domain = null;
        $secure = null;
        $httponly = null;
        Mage::getModel('core/cookie')->delete('syghome', $path, $domain, $secure, $httponly); 
        Mage::getModel('core/cookie')->delete('syg-pincode', $path, $domain, $secure, $httponly);   

        if(in_array($group_id, array(1,6))){
            $this->_redirect('gadget');
        }else{
            $this->_redirect('*/*/logoutSuccess');
        }
    }

    public function createIbmAction(){
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
            $customerData = $this->getRequest()->getPost();
            

            $errors = array();
            //$customerData['email']
            $domain_check = explode("@", $customerData['email']);
            if(!strpos($customerData['email'], "in.ibm.com") || $domain_check[1] !== "in.ibm.com"){
                $session->setCustomerFormData($this->getRequest()->getPost());
                $error = Mage::helper('customer')->__('Your email id is not valid for signup in this EPP program');
                Mage::getSingleton('core/session')->addError($error);
                // Mage::throwException($error);
                $errUrl = $this->_getUrl('customer/ibm/login', array('_secure' => true)); 
                $this->_redirectError($errUrl);
            	$errors['status'] = 0;    
            }

            $customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$customerData['mobile']);

            if(count($customerCount) > 0)
            {
                $session->setCustomerFormData($this->getRequest()->getPost());
                $error = Mage::helper('customer')->__('There is already an account with this mobile number.');
                Mage::getSingleton('core/session')->addError($error);
                // Mage::throwException($error);
                $errUrl = $this->_getUrl('customer/ibm/login', array('_secure' => true)); 
                $this->_redirectError($errUrl);
                $errors['status'] = 0;
            }
            
            $session_ebpin = Mage::getSingleton('core/session')->getEBPin();

            if($session_ebpin[$customerData['mobile']] != $customerData['cus_otp'])
            {
                $session->setCustomerFormData($this->getRequest()->getPost());
                
                $error = Mage::helper('customer')->__('Otp Entered by you is not correct.');
                // Mage::throwException($error);
                Mage::getSingleton('core/session')->addError($error);
                $errUrl = $this->_getUrl('customer/ibm/login', array('_secure' => true)); 
                $this->_redirectError($errUrl);
                $errors['status'] = 0;
            }
            if (empty($errors)) {
            	$passwordLength = 10;
	            $password = $customer->generatePassword($passwordLength); 
	            $_REQUEST['password'] = $password;
	            $_REQUEST['confirmation'] = $password;
                
                for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
                    $randomNumber .= mt_rand(0, 9); 
                }

                $name = explode(' ', $customerData['firstname']);
                if($name[1]){
                
                }else{
                    $name[1] = ".";  
                }

                $customer->setGroupId(10);
                $customer->setDeviceType('Website');    

                $customer->setFirstname($name[0]);     
                $customer->setLastname($name[1]);  
                $customer->setEmail($customerData['email']);  

                
                $customer->setPassword($password);
                $customer->setConfirmation($password);
                $customer->setMobile($customerData['mobile']);  
                $customer->setIsaffiliate(1);
               
                $customer->setRepcode('Aff_'.$name[0].'_'.$randomNumber);
                try{
                    $customer->save();
                    // $this->_dispatchRegisterSuccess($customer);
                    // $this->_successProcessRegistration($customer);
                    // return;
                    Mage::getSingleton('core/session')->addSuccess(
                    $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName()).' We have emailed your password to your email. Please login here.');
                    $customer->sendNewAccountEmail('ibmregistered','',Mage::app()->getStore()->getId());
                    $this->_redirectSuccess($this->_getUrl('customer/ibm/login/', array('_secure' => true)));
                    return;
                }catch(Exception $ex){
                    $error = Mage::helper('customer')->__($ex->getMessage());
                    Mage::throwException($error);
                }
                
                
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
                Mage::log("customer save : ".$e->getMessage(),null,'customer_error.log',true);
        }
        $errUrl = $this->_getUrl('customer/ibm/login', array('_secure' => true)); // changed line
        // echo $errUrl; exit;
        $this->_redirectError($errUrl);
    }

    public function createSygAction(){
         // print_r($_POST); 
        // echo "SYG"; exit;
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
            
                $customerCount = Mage::getModel('customer/customer')->getCollection()
                ->addFieldToFilter('mobile',$customerData['mobile'])
                ->addFieldToFilter('group_id',6);

                if(count($customerCount) > 0)
                {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    $error = Mage::helper('customer')->__('There is already an account with this mobile number.');
                    Mage::throwException($error);
                }

                $session_ebpin = Mage::getSingleton('core/session')->getEBPin();
                

                if($session_ebpin[$customerData['mobile']] != $customerData['cus_otp'])
                {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    
                    $error = Mage::helper('customer')->__('EB PIN entered by you is not correct.');
                    Mage::throwException($error);
                }


                for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
                    $randomNumber .= mt_rand(0, 9); 
                }

                // $name = explode(' ', $customerData['name']);
                $name = explode(' ', $customerData['firstname']);

                if($name[1]){
                
                }else{
                    // $name[1] = $name[0];  
                    $name[1] = '.';  
                }

                if($customerData['group'] == 'corporate'){
                    $store_id = 'EBLEN_'.rand (1000,9999);
                    $valid_store_id = Mage::getModel('customapiv6/customer')->createCorporateStoreId($store_id);
                    $customer->setGroupId(6); //sell your gadget customer group id
                    $customer->setCorporateStoreId($valid_store_id); //sell your gadget customer group id
                }else{
                    $customer->setGroupId(1); //sell your gadget customer group id
                }
                    
                $customer->setFirstname($name[0]);     
                $customer->setLastname($name[1]);  
                $customer->setAffiliateStore($name);  
                $customer->setDeviceType('Website');    
                $customer->setIsaffiliate(0);
                $customer->setRepcode('Aff_'.$name[0].'_'.$randomNumber);
                $customer->save();
                $customer->sendNewAccountEmail('registered','',1);
                $this->_dispatchRegisterSuccess($customer);
                $this->_successProcessRegistration($customer);
                return;
            } else {
                // $this->_addSessionError($errors);
                $this->_addSessionError($errors['message']);
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
