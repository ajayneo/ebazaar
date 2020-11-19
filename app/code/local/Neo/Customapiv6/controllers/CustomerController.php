<?php
class Neo_Customapiv6_CustomerController extends Neo_Customapiv6_Controller_HttpAuthController
{
	const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    const XML_PATH_ENABLED          = 'contacts/contacts/enabled';

    /**
	 * @desc : contact us
	 * @author : bhargav rupapara
	 */

	public function contactUsHtmlAction()
	{
		$html = '<div style="min-width: 320px;">
	<div style="margin: 0px auto; border: 1px solid #dedede; width: 95%;">

		<ul style=" display: block; list-style: outside none none; margin: 0; padding:10px;">
			<li style="clear: both; display: block; margin: 0 0 15px; width: 100%;overflow: hidden;">
				<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
					<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-call.png" alt="call">
				</div>
				<div style="width: 80%; float: left;">
					<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Contact Number</label>
					<span style="font-size: 15px; line-height: 18px; color:#313131">1800-266-4000<br>Monday - Friday, 9:30am - 6:30pm IST.</span>
				</div>
			</li>

			<li style="clear: both; display: block; margin: 0 0 15px; width: 100%; overflow: hidden;">
				<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
					<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-email.png" alt="Email">
				</div>
				<div style="width: 80%; float: left;">
					<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Email</label>
					<span style="font-size: 15px; line-height: 18px; color:#313131"><a href="mailto:support@electronicsbazaar.com">support@electronicsbazaar.com</a></span>
				</div>
			</li>



			<li style="clear: both; display: block; margin: 0px; width: 100%; overflow: hidden;">
				<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
					<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-pin.png" alt="location">
				</div>
				<div style="width: 80%; float: left;">
					<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Address</label>
					<span style="font-size: 15px; line-height: 18px; color:#313131">ElectronicsBazaar.com <br> 415, Hubtown Solaris,N.S.Phadke Marg,Near East West Flyover Bridge,Andheri East, Mumbai,Maharastra - 400069</span>.
				</div>
			</li>
		</ul>
	</div>
</div>';
		echo $html;
	}
	/**
	 * @desc : contact us
	 * @author : bhargav rupapara
	 */
	public function contactUsAction()
	{
		try {
			$name = $_REQUEST['name'];
			$email = $_REQUEST['email'];
			$mobile = $_REQUEST['mobile'];
			$comment = $_REQUEST['comment'];

			if(!preg_match("/^[a-zA-Z ]*$/",$_REQUEST['name'])) {
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid name.'));
				exit;
			}

			if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid email.'));
				exit;
			}

			if(!preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile'])) {
					echo json_encode(array('status' => 0, 'message' => 'Please provide valid mobile.'));
					exit;
			}

			if(empty($comment)) {
				echo json_encode(array('status' => 0, 'message' => 'Please provide comment.'));
				exit;
			}

			$postObject = new Varien_Object();
			$postObject->setData($post);

			$mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->setDesignConfig(array('area' => 'frontend'))
				->setReplyTo($email)
				->sendTransactional(
					Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
					Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
					Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
					null,
					array('data' => $postObject)
				);

			if (!$mailTemplate->getSentSuccess()) {
				throw new Exception();
			}

			$message = 'Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.';
			echo json_encode(array('status' => 1, 'message' => $message));
			exit;
		} catch(Exception $ex) {
			$message = 'Unable to submit your request. Please, try again later';
			echo json_encode(array('status' => 0, 'message' => $message));
			exit;
		}
	}

	/**
	   	 * @desc : track order
	     * @author : bhargav rupapara
	     */
	public function trackOrderAction()
	{
		$order = Mage::getModel('sales/order')->load($_REQUEST['order_id']);
		$order_id = $order->getId();
		$customer_model = Mage::getModel('customapiv6/customer');
		if($order_id != $_REQUEST['order_id']) {
			echo json_encode(array('status' => 0, 'message' => 'invalid order id'));
			exit;
		} else {
			$data = $customer_model->trackOrder($order_id);
			echo json_encode(array('status' => 1, 'data' => $data));
			exit;
		}
	}
	/**
	   	 * @desc : forgot passoword
	     * @author : bhargav rupapara
	     */
	public function forgotPasswordAction()
	{
		$email = $_REQUEST['email'];
		$customer = Mage::getModel('customer/customer')
			->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
			->loadByEmail($email);
		$customer_email = $customer->getEmail();
		if($customer_email == '') {
			echo json_encode(array('message' => 'Invalid Email', 'status' => 0));
			exit;
		}

		if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
            	echo json_encode(array('message' => 'Invalid email address.', 'status' => 0));
				exit;
            }

            /** @var $customer Mage_Customer_Model_Customer */

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken =  Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                	echo json_encode(array('message' => $exception->getMessage(), 'status' => 0));
					exit;
                }
			}
			$arr['status'] = 1;
			$arr['message'] = "Please check your email. You will receive an email with a link to reset your password.";
		} else {
			$arr['status'] = 0;
			$arr['message'] = 'Invalid email address.';
        }
		echo json_encode($arr);
	}

	public function loginAction()
	{ 
		// Mage::log($_REQUEST,null,'sandeep_ios.log');        
		$quoteId = $_REQUEST['quoteId'];
		$social_type = $_REQUEST['social_type'];
		$storeId = Mage::app()->getStore();
		$this->getResponse()->setHeader('Content-type','application/json');
		$device_type = $_REQUEST['device_type'];
		$push_id = $_REQUEST['push_id'];
		$corporate_store_id = $_REQUEST['corporate_store_id'];



		//$customer = Mage::getModel("customer/customer"); 
		//$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
		//$customer->loadByEmail($_REQUEST['email']);

		

		//if($customer->getGroupId() != 4)
		//{
		//	echo json_encode(array('status' => 2, 'message' => "Please Login as Affiliate user"));
		//	exit;
		//}
		//if social connect
		if(isset($social_type) && in_array($social_type, array('fb','g'))) {
			$social_login_id = $_REQUEST['social_login_id'];

			$new_customer_data = array();

			if(empty($social_login_id)) {
				echo json_encode(array('status' => 0, 'message' => "Please provide {$social_type} login id"));
				exit;
			}

			$social_id_field = '';
			if($social_type == 'fb') {
				$social_id_field = 'inchoo_socialconnect_fid';
			} else if($social_type == 'g') {
				$social_id_field = 'inchoo_socialconnect_gid';
			}

			if(!filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL)) {
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid Email.'));
				exit;
			} else {
				$email = $new_customer_data['email'] = $_REQUEST['email'];
				$new_customer_data['md5_customer_id'] = md5($_REQUEST['email']);
			}

			$profile_img = $_REQUEST['profile_picture'];
			$customer = Mage::getModel('customer/customer')
				->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter($social_id_field,$social_login_id)
				->addAttributeToFilter('email',$email)->getFirstItem();

			$customer_id = $customer->getId();


			if($customer_id > 0) {

				// get customer model
				$customer_model_eb = Mage::getModel('customer/customer')->load($customer->getId());

				// load customer model if present
				$quote = Mage::getModel('sales/quote')->loadByCustomer($customer_model_eb);
				$quoteId = $quote->getId();

				// if customer quote id not present then create quote and assign it to customer
				if(empty($quoteId)){
					$quoteId = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($customer_model_eb);
				}

				// get cart total quantity
				$cart_total_quantity = Mage::getModel('customapiv6/customer')->getCartItemQuantity($quoteId);

				$customer_data = array();
				$customer_data['id'] = $customer->getId();
				$customer_data['email'] = $_REQUEST['email'];
				$customer_data['firstname'] = $customer->getData('firstname');
				$customer_data['lastname'] = $customer->getData('lastname');
				$customer_data['taxvat'] = $customer->getData('taxvat');
				$customer_data['gstin'] = $customer->getData('gstin');


				$pin = (int) $customer->getData('pincode');

				if(strlen($pin) != 6){
					$customer_data['pincode'] = '';
				}else{
					$customer_data['pincode'] = $customer->getData('pincode');
				}
				


				$customer_data['store_name'] = $customer->getData('store_name');

				$dob = $customer->getData('dob');
				if(!empty($dob)) {
					$customer_data['dob'] = date('d/m/Y',strtotime($customer->getData('dob')));
				}
				$customer_data['mobile'] = $customer->getData('mobile');
				$customer_data['profile_picture'] = urlencode($customer->getData('profile_picture'));

				// update push id within notification table
				$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($device_type, $push_id, $customer->getId());
				echo json_encode(array('status' => 1, 'message'=> 'LoggedIn Successfully', 'token' => $customer->getMd5CustomerId(), 'user' => $customer_data, 'login_type' => 'social', 'quoteId' => $quoteId, 'cart_total_qty' => $cart_total_quantity,'customer_id'=>$customer->getGroupId()));
				exit;
			} else {
				try {

					if(empty($_REQUEST['firstname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['firstname'])) {
						echo json_encode(array('status' => 0, 'message' => 'Please provide valid first name.'));
						exit;
					} else {
						$new_customer_data['firstname'] = $_REQUEST['firstname'];
					}
					if(empty($_REQUEST['lastname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['lastname'])) {
						echo json_encode(array('status' => 0, 'message' => 'Please provide valid last name.'));
						exit;
					} else {
						$new_customer_data['lastname'] = $_REQUEST['lastname'];
					}

					$new_customer_data[$social_id_field] = $social_login_id;
					$new_customer_data['device_type'] = $_POST['device_type'];
					$new_customer_data['push_id'] = $_POST['push_id'];
					$new_customer_data['profile_picture'] = $_POST['profile_picture'];
					$new_customer_data['mobile'] = $_POST['mobile'];
					$new_customer = Mage::getModel('customer/customer');
					$new_customer->addData($new_customer_data);
					$new_customer->save();
				} catch(Exception $ex) {
					$customer1 = Mage::getModel('customer/customer')
						->getCollection()
						->addAttributeToSelect('*')
						//->addAttributeToFilter($social_id_field,$social_login_id)
						->addAttributeToFilter('email',$email)->getFirstItem();

					$customer_id1 = $customer1->getId();
					if($customer_id1 > 0) {
						// get customer model
						$customer_model_eb = Mage::getModel('customer/customer')->load($customer1->getId());

						// load customer model if present
						$quote = Mage::getModel('sales/quote')->loadByCustomer($customer_model_eb);
						$quoteId = $quote->getId();

						// if customer quote id not present then create quote and assign it to customer
						if(empty($quoteId)){
							$quoteId = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($customer_model_eb);
						}

						// get cart total quantity
						$cart_total_quantity = Mage::getModel('customapiv6/customer')->getCartItemQuantity($quoteId);

						$customer_data = array();
						$customer_data['id'] = $customer1->getId();
						$customer_data['email'] = $_REQUEST['email'];
						$customer_data['firstname'] = $customer1->getData('firstname');
						$customer_data['lastname'] = $customer1->getData('lastname');
						$customer_data['taxvat'] = $customer1->getData('taxvat');
						$customer_data['gstin'] = $customer1->getData('gstin');
						$pin = (int) $customer->getData('pincode');

						if(strlen($pin) != 6){
							$customer_data['pincode'] = '';
						}else{
							$customer_data['pincode'] = $customer->getData('pincode');
						}
						$customer_data['store_name'] = $customer->getData('store_name');

						$dob = $customer->getData('dob');
						if(!empty($dob)) {
							$customer_data['dob'] = date('d/m/Y',strtotime($customer1->getData('dob')));
						}
						$customer_data['mobile'] = $customer1->getData('mobile');
						$customer_data['profile_picture'] = urlencode($customer1->getData('profile_picture'));
						// update push id within notification table
						$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($device_type, $push_id, $customer1->getId());
						echo json_encode(array('status' => 1, 'message'=> 'Logged In Successfully', 'token' => $customer1->getMd5CustomerId(), 'user' => $customer_data, 'login_type' => 'social', 'quoteId' => $quoteId, 'cart_total_qty' => $cart_total_quantity,'customer_id'=>$customer1->getGroupId()));
						exit;
					} else {
						echo json_encode(array('status' => 0, 'message' =>  $ex->getMessage()));
						exit;
					}
				}

				// load customer model if present
				$quote = Mage::getModel('sales/quote')->loadByCustomer($new_customer);
				$quoteId = $quote->getId();

				// if customer quote id not present then create quote and assign it to customer
				if(empty($quoteId)){
					$quoteId = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($new_customer);
				}

				// get cart total quantity
				$cart_total_quantity = Mage::getModel('customapiv6/customer')->getCartItemQuantity($quoteId);

				$customer_data = array();
				$customer_data['id'] = $new_customer->getId();
				$customer_data['email'] = $_REQUEST['email'];
				$customer_data['firstname'] = $new_customer->getData('firstname');
				$customer_data['lastname'] = $new_customer->getData('lastname');
				$customer_data['gstin'] = $new_customer->getData('gstin');
				$customer_data['taxvat'] = $new_customer->getData('taxvat');
				$pin = (int) $customer->getData('pincode');

				if(strlen($pin) != 6){
					$customer_data['pincode'] = '';
				}else{
					$customer_data['pincode'] = $customer->getData('pincode');
				}
				$customer_data['store_name'] = $customer->getData('store_name');

				$dob = $new_customer->getData('dob');
				if(!empty($dob)) {
					$customer_data['dob'] = date('d/m/Y',strtotime($new_customer->getData('dob')));
				}
				$customer_data['mobile'] = $new_customer->getData('mobile');
				$customer_data['profile_picture'] = urlencode($new_customer->getData('profile_picture'));

				// update push id within notification table
				$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($device_type, $push_id, $new_customer->getId());

				echo json_encode(array('status' => 1, 'message'=> 'LoggedIn Successfully', 'token' => $new_customer->getMd5CustomerId(), 'user' => $customer_data, 'login_type' => 'social', 'quoteId' => $quoteId, 'cart_total_qty' => $cart_total_quantity,'customer_id'=>$new_customer->getGroupId()));
				exit;
			}
		} else {
			//not social connect
			try {
				$email = $_REQUEST['email'];
				$password = $_REQUEST['password'];
				$corporate_store_id = $_REQUEST['corporate_store_id'];

				$customer = Mage::getModel('customer/customer')->setWebsiteId(1);
				if($customer->authenticate($email, $password)) {
					$customer->loadByEmail($email);

					//set corporate store id 15/01/2018 Mahesh Gurav
					if(!empty($corporate_store_id)){

						if($customer->getCorporateStoreId() !== $corporate_store_id){
							echo json_encode(array('status' => 0, 'message' => "Please provide valid corporate store id"));
							exit;
						}
					}



					// load customer model if present
					$quote = Mage::getModel('sales/quote')->loadByCustomer($customer);
					$quoteId = $quote->getId();

					// if customer quote id not present then create quote and assign it to customer
					if(empty($quoteId)){
						$quoteId = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($customer);
					}

					// get cart total quantity
					$cart_total_quantity = Mage::getModel('customapiv6/customer')->getCartItemQuantity($quoteId);

					$result = array('status' => 1, 'token' =>  $customer->getMd5CustomerId());

					$customer_data = array();
					$customer_data['id'] = $customer->getId();
					$customer_data['email'] = $email;
					$customer_data['firstname'] = $customer->getData('firstname');
					$customer_data['lastname'] = $customer->getData('lastname');
					$dob = $customer->getData('dob');
					if(!empty($dob)) {
						$customer_data['dob'] = date('d/m/Y',strtotime($customer->getData('dob')));
					}
					$customer_data['mobile'] = $customer->getData('mobile');
					$customer_data['profile_picture'] = urlencode($customer->getData('profile_picture'));

					$customer_gstin = '';
					if($customer->getData('gstin') !== null){
						$customer_gstin = $customer->getData('gstin');
					}

					$customer_data['gstin'] = $customer_gstin;
					$customer_data['taxvat'] = '';
					if($customer->getData('taxvat') !== null){
						$customer_data['taxvat'] = $customer->getData('taxvat');
					}
					
					$customer_pincode = '';
					if($customer->getData('pincode') !== null){
						$customer_pincode = $customer->getData('pincode');
					}
					$customer_data['pincode'] = $customer_pincode;

					$customer_buisness_type = '';
				    $config    = Mage::getModel('eav/config');
					if($customer->getData('business_type') !== null){

						$business_type = $customer->getData('business_type');
						$storeId   = 0;
					    $business_attr = $config->getAttribute('customer', 'business_type');
					    $business_type_options    = $business_attr->setStoreId($storeId)->getSource()->getAllOptions();
					    foreach ($business_type_options as $key => $options) {
					    	if($options['value'] == $business_type){
					    		$customer_buisness_type = $options['label'];
					    	}
					    }
					}	

				    $customer_referred_by = '';
				    if($customer->getData('asm_map') !== null){
						$referred_by = $customer->getData('asm_map');
					    $referred_attr = $config->getAttribute('customer', 'asm_map');
					    $referred_by_options    = $referred_attr->setStoreId($storeId)->getSource()->getAllOptions();
					    foreach ($referred_by_options as $key => $options) {
					    	if($options['value'] == $referred_by){
					    		$customer_referred_by = $options['label'];
					    	}
					    }
				    }

				    $customer_data['business_type'] = $customer_buisness_type;
				    $customer_data['referred_by'] = $customer_referred_by;
				    $customer_data['is_arm'] = 1;
				    //corpoarate user changes
					$customer_data['is_corporate'] = '0';
					$customer_data['is_affiliate'] = '0';
					
					if($customer->getData('group_id') == 4){
						$customer_data['is_affiliate'] = '1';				
					}else if($customer->getData('group_id') == 6){
						//for live corporate store id is 6
						$customer_data['is_corporate'] = '0'; //Commented by JP for show corporat customer as general.
					}
					
					$customer_data['corporate_store_id'] = $customer->getData('corporate_store_id');

					$is_retailer = $customer->getData('zone') !=  '' ? '1' : '0';
					$customer_data['is_retailer'] = $is_retailer;	
									    

					// update push id within notification table
					if(!empty($_REQUEST['version'])){
						$device_fcm = Mage::getModel('customapiv6/customer')->addFcmIdVersion($device_type, $push_id, $customer->getId(), $_REQUEST['version']);
					}else{
						$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($device_type, $push_id, $customer->getId());
					}	

					$is_ibm = 'No';

					$check_domain = explode("@", $email);
					$domain_for_signup = "in.ibm.com";
					if($check_domain[1] == $domain_for_signup){
						$is_ibm = 'Yes';
					}

					echo json_encode(array('status' => 1, 'message'=> 'LoggedIn Successfully','token' => $customer->getMd5CustomerId(), 'user' => $customer_data, 'login_type' => 'normal', 'quoteId' => $quoteId, 'cart_total_qty' => $cart_total_quantity,'customer_id'=>$customer->getGroupId(), 'is_ibm'=>$is_ibm));
					exit;
				}
			} catch(Exception $ex) {
				$this->getResponse()->setBody(json_encode(array('status' => 0, 'message' =>  $ex->getMessage())));
			}
		}
	}
	
	public function registerAction() 
	{

		$customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$_REQUEST['mobile']);
		$name = explode(' ', $_REQUEST['name']);
  
		$_REQUEST['firstname'] = $name[0];

		if($name[1]){
			$_REQUEST['lastname'] = $name[1];  
		}else{
			$_REQUEST['lastname'] = $name[0];  	
		}     
		

		/*$required_fields = array('firstname', 'lastname', 'email', 'password', 'mobile', 'dob', 'prefix','device_type');
		$posted_fields = array_intersect($required_fields, array_keys($_POST));

		if(count($posted_fields) < count($required_fields)) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide required fields.'));
			exit;
		}*/
		// if(!in_array($_REQUEST['prefix'],array('Mr.', 'Ms.')) || $_REQUEST['prefix'] == "") {
		// 	echo json_encode(array('status' => 0, 'message' => 'Prefix must be either Mr. or Ms.'));
		// 	exit;
		// }
		
		if(empty($_REQUEST['firstname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['firstname'])) { 
			echo json_encode(array('status' => 0, 'message' => 'Please Update Your App.'));
			exit;
		}
		if($_REQUEST['ebpin_user'] != $_REQUEST['ebpin_server']) {
			echo json_encode(array('status' => 0, 'message' => 'Eb Pin is Not Correct.'));
			exit;
		}
		if(empty($_REQUEST['lastname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['lastname'])) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid last name.'));
			exit;
		}
		if(count($customerCount) > 0) { 
			echo json_encode(array('status' => 0, 'message' => 'Mobile Number Already Register.'));
			exit; 
		}
		if(empty($_REQUEST['mobile']) || !preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile']) || count($customerCount) > 0) { 
			echo json_encode(array('status' => 0, 'message' => 'Please provide 10 Digit Mobile Number.'));
			exit; 
		} 
		if(!filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL)) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid Email.'));
			exit;
		}
		if(strlen($_REQUEST['password']) < 6) {
			echo json_encode(array('status' => 0, 'message' => 'Your password must be atleast 6 character long.'));
			exit;
		}
		if(!in_array($_REQUEST['device_type'],array('android', 'iphone'))) {
			echo json_encode(array('status' => 0, 'message' => 'Device type must be android or iphone .'));
			exit;
		}

		for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
			$randomNumber .= mt_rand(0, 9);
		}

		$data = array();
		$data['firstname'] = $_REQUEST['firstname'];
		$data['lastname'] = $_REQUEST['lastname'];
		$data['email'] = $_REQUEST['email'];
		$data['md5_customer_id'] = md5($_REQUEST['email']);
		$data['password'] = $_REQUEST['password'];
		$data['mobile'] = $_REQUEST['mobile'];
		$data['dob'] = date('Y-m-d', strtotime($_REQUEST['dob']));
		$data['prefix'] = $_REQUEST['prefix'];
		$data['device_type'] = $_REQUEST['device_type'];
		$data['push_id'] = $_REQUEST['push_id'];
		$data['group_id'] = 4;    
		$data['website_id'] = 1;

		$data['asm_map'] = $_REQUEST['asm_id']; 
		$data['cus_country'] = 'IN';
		$data['cus_state'] = $_REQUEST['cus_state'];
		$data['cus_city'] = $_REQUEST['cus_city'];
		$data['affiliate_store'] = $_REQUEST['affiliate_store'];
		$data['landmark'] = $_REQUEST['landmark'];
		$data['isaffiliate'] = 1;
		$data['repcode'] = 'Aff_'.$_REQUEST['firstname'].'_'.$randomNumber;

		$data['address'] = $_REQUEST['address'];
		$data['pincode'] = $_REQUEST['pincode']; 
		//$data['repcode'] = $_REQUEST['verification_code'];

 
		try {
			$customer = Mage::getModel('customer/customer');
			$customer->addData($data);
			$customer->save();
			$customer->sendNewAccountEmail('registered','',1);
			/*$customerData = Mage::getModel('customapiv6/customer')->getCustomerInfo($customer->getId());*/

			$customer_data = array();
			$customer_data['id'] = $customer->getId();
			$customer_data['firstname'] = $customer->getData('firstname');
			$customer_data['email'] = $customer->getData('email');
			$customer_data['lastname'] = $customer->getData('lastname');
			$pin = (int) $customer->getData('pincode');

				if(strlen($pin) != 6){
					$customer_data['pincode'] = '';
				}else{
					$customer_data['pincode'] = $customer->getData('pincode');
				}

			$dob = $customer->getData('dob');
			if(!empty($dob)) {
				$customer_data['dob'] = date('d/m/Y',strtotime($customer->getData('dob')));
			}
			$customer_data['mobile'] = $customer->getData('mobile');

			// creating the user quote and sending in response
			$quoteId = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($customer);
			$customer_data['quoteId'] = $quoteId;

			// update push id within notification table
			if(!empty($_REQUEST['version'])){
				$device_fcm = Mage::getModel('customapiv6/customer')->addFcmIdVersion($data['device_type'], $data['push_id'], $customer->getId(), $_REQUEST['version']);
			}else{
				$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($data['device_type'], $data['push_id'], $customer->getId());
			}

			echo json_encode(array('status' => 1, 'message'=> 'User Registered Successfully', 'login_type' => 'normal', 'user' => $customer_data));
			exit;
		} catch(Exception $ex) {
			$this->getResponse()->setBody(json_encode(array('status' => 0, 'message' => $ex->getMessage())));
		}
	}
	//edit customer for below params
	//firstname, email,mobile,business_type,asm_id,pincode,pan_number,gstin
	//Mahesh Gurav
	//05 Dec 2017
	public function editProfileAction()
	{
		$customerId = $_REQUEST["userid"];
		$this->validateCustomer($customerId);
		$data = array();

		//pincode city state country
		if(empty($_REQUEST['pincode'])){
			echo json_encode(array('status' => 0, 'message' => 'Please provide PINCODE'));
				exit;
		}else{
			$pincode = $_REQUEST['pincode'];

			if(strlen($_REQUEST['pincode']) !== 6){
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid PINCODE'));
				exit;	
			}
			$pincode_data = Mage::getModel('operations/serviceablepincodes')->getPincodeData($pincode);

			if(!empty($pincode_data)){
				$data['cus_city'] = $pincode_data['city'];
				$data['cus_state'] = $pincode_data['region_id'];
				$data['pincode'] = $pincode_data['pincode'];
				$data['cus_country'] = $pincode_data['country'];
				$region = $pincode_data['region'];
			}
		}

		//GST IN
		if(!empty($_REQUEST['gstin'])){
			$gstin = $_REQUEST["gstin"];
			$gstVal = preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9]{1}[A-Z]{1}[0-9A-Z]{1}$/", $gstin);

			if(!$gstVal){
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid GST'));
				exit;	
			}
		}
		$data['gstin'] = $_REQUEST['gstin'];
		
		//Mobile Number, Telephone mandatory
		if(!empty($_REQUEST['mobile'])){
			if(!preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile']) || strlen($_REQUEST['mobile']) !== 10) {
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid Mobile.'));
					exit;
			}else{
				$data['mobile'] = $_REQUEST['mobile'];
				$data['cus_telephone'] = $_REQUEST['mobile'];
			}
		}

		//taxvat number non mandatory
		if(!empty($_REQUEST['pan'])){
			$pan = $_REQUEST['pan'];
			$panVal = preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $pan);
			if(!$panVal){
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid PAN'));
				exit;	
			}
			$data['taxvat'] = $_REQUEST['pan'];
		}else{
			$data['taxvat'] = '';
		}
		//store name
		$store_name = '';
		if(!empty($_REQUEST['firstname'])){
			$string_name = preg_replace("/[^ \w\'\s\.\-]+/", "", $_REQUEST['firstname']);
			$data['firstname'] = $string_name;
			$data['store_name'] = $string_name; 
			$data['affiliate_store'] = $string_name;
		}
		
		//asm_id
		if(!empty($_REQUEST['asm_id'])){
			$data['asm_map'] = $_REQUEST['asm_id'];
		}

		//business_type
		if(!empty($_REQUEST['business_type'])){
			$data['business_type'] = $_REQUEST['business_type'];
		}
		
		try {
			$customer = Mage::getModel('customer/customer')->load($customerId);
			$customerCollection = Mage::getModel('customer/customer')->getCollection();
			if($customer->getData('mobile') !== $data['mobile']){
				$mobileData = $customerCollection->addFieldToFilter('mobile',$data['mobile']);
				if($mobileData->getData('mobile')){
					echo json_encode(array('status' => 0, 'message' => 'This mobile number is already being used'));
					exit;
				}

			}

			$customer->addData($data);
			$customer->save();
			$customer_data = array();
		    $config    = Mage::getModel('eav/config');
			//referred by
			if($customer->getData('asm_map') !== null){
				$referred_by = $customer->getData('asm_map');
			    $referred_attr = $config->getAttribute('customer', 'asm_map');
			    $referred_by_options    = $referred_attr->setStoreId($storeId)->getSource()->getAllOptions();
			    foreach ($referred_by_options as $key => $options) {
			    	if($options['value'] == $referred_by){
			    		$customer_referred_by = $options['label'];
			    	}
			    }
		    }
		    //business type
		    $customer_buisness_type = '';
			if($customer->getData('business_type') !== null){
				$business_type = $customer->getData('business_type');
				$storeId   = 0;
			    $business_attr = $config->getAttribute('customer', 'business_type');
			    $business_type_options    = $business_attr->setStoreId($storeId)->getSource()->getAllOptions();
			    foreach ($business_type_options as $key => $options) {
			    	if($options['value'] == $business_type){
			    		$customer_buisness_type = $options['label'];
			    	}
			    }
			}
			$customer_data['id'] = $customer->getId();
			$customer_data['firstname'] = $customer->getData('firstname');
			// $customer_data['lastname'] = $customer->getData('lastname');
			$customer_data['pan_number'] = $customer->getData('taxvat');
			$customer_data['gstin'] = $customer->getData('gstin');
			$customer_data['mobile'] = $customer->getData('mobile');
			$customer_data['email'] = $customer->getData('email');
			$customer_data['pincode'] = $customer->getData('pincode');
			$customer_data['referred_by'] = $customer_referred_by;
			$customer_data['business_type'] = $customer_buisness_type;
			echo json_encode(array('status' => 1, 'message'=> 'Profile Edited Successfully', 'user' => $customer_data));
			exit;
		} catch(Exception $ex) {
			$this->getResponse()->setBody(json_encode(array('status' => 0, 'message' => $ex->getMessage())));
		}
	}

	public function saveEncodedTokenAction()
	{
		/*$customer = Mage::getModel('customer/customer')->load('0682c857e1c2edd47dbe73090610bdca','md5_customer_id');
		echo '<pre>';
		print_r($customer->getData());*/
		$customer_collection = Mage::getModel('customer/customer')->getCollection();
		try{
		foreach($customer_collection as $customer) {
			$customer->setMd5CustomerId(md5($customer->getEmail()));
			$customer->save();
		}
		echo 'done';
		} catch(Exception $ex) {
			print_r($ex->getMessage());
		}
	}
	/*
	* @desc : change password api
	*/
	public function changeCustomerPasswordAction() {
		$storeId = Mage::app()->getStore()->getStoreId();
		$customerId = $_REQUEST["customerId"];
		$old_password = $_REQUEST["old_password"];
		$new_password = $_REQUEST["new_password"];
		$customerData = Mage::getModel('customapiv6/customer')->getCustomerInfo($customerId);
		$email = $customerData["email"];

		$websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
		try {
		     $login_customer_result = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->authenticate($email, $old_password);
		     $validate = 1;
		}
		catch(Exception $ex) {
		     $validate = 0;
		     $result = 'Error : '.$ex->getMessage();
		     echo json_encode(array('status' => 0,'message'=> $result));
		     exit();
		}
		if($validate == 1) {
		     try {
		          $customer = Mage::getModel('customer/customer')->load($customerId);
		          $customer->setPassword($new_password);
		          $customer->save();
		          $result = 'Your Password has been Changed Successfully';
		          echo json_encode(array('status' => 1,'message'=> $result));
		          exit();
		     }
		     catch(Exception $ex) {
		          $result = 'Error : '.$ex->getMessage();
		          echo json_encode(array('status' => 0,'message'=> $result));
		          exit();
		     }
		}	else {
		  echo json_encode(array('status' => 0,'message'=> 'Incorrect Old Password.'));
		  exit();
		}
	}
	/*
	* @desc : change customer account info
	*/
	public function updateCustomerAccountAction()
	{
		$customerId = $_REQUEST["customerId"];
		$status_flag = Mage::getModel('customapiv6/customer')->updateCustomerInfo($customerId, $customerData);
		if($status_flag) {
			echo json_encode(array('status' => 1,'result'=> 'Customer Updated Successfully'));
		} else {
			echo json_encode(array('status' => 0,'result'=> 'Customer Entry not updated'));
		}
    die;
	}
	/*
	* @desc : create address of customer
 	* @param int $customerId
 	* @param array $addressData
	*/
	public function createCustomerAddressAction()
	{
		try {
			$customer_id = $_REQUEST['user_id'];
			$this->validateCustomer($customer_id);
			$address = Mage::getModel('customer/address_api');
			$newCustomerAddress = array(
				'firstname'  => $_REQUEST['firstname'],
				//'lastname'   => $_REQUEST['lastname'],
				'lastname'   => '.',
				'country_id' => $_REQUEST['country_id'],
				'region_id'  => $_REQUEST['region'],
				'city'       => $_REQUEST['city'],
				'street'     => array($_REQUEST['street_1'],$_REQUEST['street_2']),
				'telephone'  => $_REQUEST['telephone'],
				'postcode'   => $_REQUEST['postcode'],
				'mobile'  => $_REQUEST['mobile'],
				'company'  => $_REQUEST['company'],
				'affiliate_store'  => $_REQUEST['company'],
				'gstin'  => $_REQUEST['gstin'],
				'is_default_billing'  => (bool)$_REQUEST['is_default_billing'],
				'is_default_shipping' => (bool)$_REQUEST['is_default_shipping']
			);
			$address_id = $address->create($customer_id,$newCustomerAddress);
			echo json_encode(array('status' => 1, 'address_id' => $address_id, 'message'=> 'New address added successfully '.$_REQUEST['country_id']));
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message'=> $ex->getMessage()));
		}
	}
	/*
	* @desc : update address of customer
 	* @param int $addressId
 	* @param array $addressdata
	*/
	public function updateCustomerAddressAction()
	{
		try {
			$address_id = $_REQUEST['address_id'];
			$this->validateAddress($address_id);
			$address = Mage::getModel('customer/address_api');
			$CustomerAddress = array(
				'firstname'  => $_REQUEST['firstname'],
				'lastname'   => $_REQUEST['lastname'],
				'country_id' => $_REQUEST['country_id'],
				'region_id'  => $_REQUEST['region'],
				'city'       => $_REQUEST['city'],
				'street'     => array($_REQUEST['street_1'],$_REQUEST['street_2']),
				'telephone'  => $_REQUEST['telephone'],
				'mobile'  => $_REQUEST['mobile'],
				'gstin'  => $_REQUEST['gstin'],
				'postcode'   => $_REQUEST['postcode'],
				'is_default_billing'  => (bool)$_REQUEST['is_default_billing'],
				'is_default_shipping' => (bool)$_REQUEST['is_default_billing']
			);
			$address_id = $address->update($address_id,$CustomerAddress);
			echo json_encode(array('status' => 1, 'address_id' => $address_id, 'message'=> 'Address updated successfully'));
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message'=> $ex->getMessage()));
		}
	}
	/*
	* @desc : delete address of customer
 	* @param int $addressId
	*/
	public function deleteCustomerAddressAction()
	{
		$addressId = $_REQUEST["addressId"];
    $storeId = Mage::app()->getStore()->getStoreId();
    try {
      $customermodel = new Mage_Customer_Model_Address_Api();
      $status_flag = $customermodel->delete($addressId);
      if($status_flag){
      	echo json_encode(array('status' => 1,'result'=> 'Address Entry Deleted Successfully'));
      } else {
      	echo json_encode(array('status' => 0,'result'=> 'Address not deleted'));
      }
    } catch (Exception $e) {
      echo json_encode(array('status'=> 0, 'message' => $e->getMessage()));
    }
    die;
	}
	/*
	* @desc : get address list of customer
 	* @param int $customerId
	*/
	public function getCustomerAddressListAction()
	{
		$customerId = $_REQUEST["userid"];
    $storeId = Mage::app()->getStore()->getStoreId();
    try {
      $customermodel = new Mage_Customer_Model_Address_Api();
      $addressList = $customermodel->items($customerId);
      $addressListArray = array();
      foreach ($addressList as $value) {
      	if(!empty($value["street"])) {
      		$complete_address = $value["street"].", ";
      	}
      	if(!empty($value["city"])) {
      		$complete_address .= $value["city"].", ";
      	}
      	if(!empty($value["region"])) {
      		$complete_address .= $value["region"].", ";
      	}
      	if(!empty($value["country_id"])) {
      		$country = "";
      		$country = Mage::getModel('directory/country')->loadByCode($value["country_id"]);
      		$complete_address .= $country->getName().", ";
      	}
      	if(!empty($value["postcode"])) {
      		$complete_address .= $value["postcode"];
      	}
      	$temp = $value;
      	if(strpos($complete_address, "\n")) {
            $temp["complete_address"] = str_replace("\n", ", ", $complete_address);
          } else {
            $temp["complete_address"] = $complete_address;
          }
	if($temp["complete_address"] !== null){
      	   $addressListArray[] = $temp;
	}
        
      }
      echo json_encode(array('status' => 1, 'result' => $addressListArray));
    } catch (Exception $e) {
      echo json_encode(array('status'=> 0, 'message' => $e->getMessage()));
    }
    die;
	}
	/*
	* @desc : get myaccount page listing api
 	* @param int $customerId
	*/
	public function getMyaccountListingAction()
	{
		$customerId = $_REQUEST["customerId"];

		// get customer data
    $result["customer_info"] = $this->getCustomerInfo($customerId);

		/*$orders = Mage::getResourceModel('sales/order_collection')
        ->addFieldToSelect('*')
        ->addFieldToFilter('customer_id', $customerId)
        ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
        ->setOrder('created_at', 'desc');

    //$this->setOrders($orders);
    $i=0;
    $recent_orders = array();
		foreach ($orders as $order):
			$recent_orders[$i]["id"] = $order->getRealOrderId();
			$recent_orders[$i]["grand_total"] = $order->getGrandTotal();
			echo $order->getRealOrderId().'&nbsp;at&nbsp;'.'&nbsp;('.$order->formatPrice($order->getGrandTotal()).')';
		endforeach;
		*/
    //print_r($orders);
    die;

    $storeId = Mage::app()->getStore()->getStoreId();
    try {
      $customermodel = new Mage_Customer_Model_Address_Api();
      $addressList = $customermodel->items($customerId);
      echo json_encode(array('status' => 1, 'result' => $addressList));
    } catch (Exception $e) {
      echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
    }
    die;
	}

	/*
	* @desc : get user wishlist api
 	* @param int $customerId
	*/
	public function getCustomerWishlistAction()
	{
		$customerId = $_REQUEST["userid"];
		$this->validateCustomer($customerId);
		$wishlist_container = Mage::getModel('customapiv6/customer')->getWishlistListing($customerId);
		$wishlist_container_blank_check = array_filter($wishlist_container);

		if (!empty($wishlist_container_blank_check)) {
			echo json_encode(array('status' => 1, 'data' => $wishlist_container, 'share' => 1));
		} else {
			echo json_encode(array('status' => 0, 'message' => 'Wishlist Empty'));
		}
		die;
	}

	public function addAllToCartWishlistAction(){
		$customerId = $_REQUEST["userid"];
		$this->validateCustomer($customerId);

        $customer = Mage::getModel('customer/customer')->load($customerId);
		$wishlist = Mage::getModel('customapiv6/customer')->getWishlistListing($customerId);
		// print_r($wishlist);exit;
		$cart_products = array();
		$outofstock = array();
		$quote_id = $this->getRequest()->getParam('quoteId');
		$message = '';

		$storeId = Mage::app()->getStore()->getStoreId();
		$quote_info_api = new Mage_Checkout_Model_Cart_Api();
      	$quote_info = $quote_info_api->info($quote_id, $storeId);
      	// print_r($quote_info['items']);exit;      	
      	$arrpro = '';
      	foreach ($quote_info['items'] as $key => $value) {
      		$arrpro[$value['product_id']]= $value['qty'] + 1;
      	}
      	// print_r($arrpro);exit;
		foreach ($wishlist as $item)
		{
			if($item['new_launch'] == 1){ /// Coming Soon(New launch products should not be added to cart)
				continue;
			}
			if($item['is_instock'] == 1){
		      	$_product = Mage::getModel('catalog/product')->load($item['product_id']);
				$_stock = $_product->getStockItem()->getData();

				if(array_key_exists($item['product_id'], $arrpro)){
					if($arrpro[$item['product_id']] > $_stock['qty'] ){
						continue;
					}
				}

				$cart_products[] = array("product_id" => $item['product_id'], "qty" => 1);
				Mage::getModel('wishlist/item')->load($item['wishlist_item_id'])->delete();	
				
			}else{
				$outofstock[] = $item['product_id']; 
			}
		}
		try{	
			// print_r($cart_products);exit;

			if(count($cart_products) > 0){
				$cart_product = new Mage_Checkout_Model_Cart_Product_Api();
				$product_added_flag = $cart_product->add($quote_id, $cart_products);
				// get cart total quantity
				$cart_total_quantity = Mage::getModel('customapiv6/customer')->getCartItemQuantity($quote_id);
				$message = "Available products are added to cart successfully.";
			}else{
				$message = "Available products are already moved to cart.";
			}			

			
			echo json_encode(array('status' => 1, 'cart_total_qty' =>$cart_total_quantity, 'message' => $message));

		}
		catch(Exception $e){
			$message = $e->getMessage();
			echo json_encode(array('status' => 0, 'message' => $message));
		}
		die;
	}

	public function validateCustomer($customer_id)
	{
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		$customer_id = $customer->getId();

		if($customer_id < 1) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid customer id'));
			exit;
		}
	}

	public function dashboardAction()
	{
		$customer_id = $_POST['userid'];
		$this->validateCustomer($customer_id);
		$customer = Mage::getModel('customer/customer')->load($customer_id);

		$default_billing_address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());

		$default_shipping_address = Mage::getModel('customer/address')->load($customer->getDefaultShipping());

		$customer_model = Mage::getModel('customapiv6/customer');
		try {
			$result['recent_orders'] = $customer_model->getCustomerOrders($customer_id, 3);
			$result['default_billing_address'] = $default_billing_address->getData();
			$result['default_shipping_address'] = $default_shipping_address->getData();
			echo json_encode(array('status' => 1, 'data' => $result));
			exit;
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}
	/*
  * @desc : Customer Myorders api
  * @params : userid
  */
	public function myOrdersAction()
	{
		$customer_id = $_REQUEST['userid'];
		$page_size = $this->getRequest()->getParam('page_size');
		$page_no = $this->getRequest()->getParam('page_no');

		$this->validateCustomer($customer_id);
		$customer_model = Mage::getModel('customapiv6/customer');
		try {
			$orders = Mage::getModel('sales/order')->getCollection()
									->addFieldToSelect(array('increment_id','grand_total','created_at','state','status'))
									->addFieldToFilter('customer_id',$customer_id)
									->setOrder('created_at','DESC');
			if($orders->count() !== 0){
				$result['count'] = $orders->count();
				$result['recent_orders'] = $customer_model->getCustomerMyOrders($customer_id, $page_no, $page_size);
				echo json_encode(array('status' => 1, 'message' => 'My orders listed successfully', 'data' => $result));
				exit;
			} else {
				echo json_encode(array('status' => 0, 'message' => 'No orders available', 'data' => array('count'=> 0,'recent_orders'=> array())));
				exit;
			}
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}
	public function getOrderDetailsAction()
	{
		$customer_id = $_REQUEST['userid'];
		$this->validateCustomer($customer_id);
		$order_id = $_REQUEST['order_id'];

		if(strlen($order_id) >= 9){
			$increment_id = $order_id;
			$_order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
            if($_order){
				$order_id = $_order->getEntityId();
            }
		}  

		$customer_model = Mage::getModel('customapiv6/customer');
		try {
			$data = $customer_model->getOrderDetails($order_id);
			echo json_encode(array('status' => 1, 'data' => $data));
			exit;
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}
	public function getCountryListAction()
	{
		try {
			$countryList = Mage::getModel('directory/country')->getResourceCollection()->loadByStore()->toOptionArray(false);
			echo json_encode(array('status' => 1, 'countries' => $countryList));
			exit;
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}
	public function getRegionListingAction()
	{
		$customer_id = $_REQUEST['country_id'];
		try {
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$res = $read->fetchAll("SELECT region_id as value, default_name as label FROM  `directory_country_region` WHERE  `country_id` =  '$customer_id'");
			echo json_encode(array('status' => 1, 'region' => $res));
			exit;
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}
	public function getNotificationListAction()
	{
		try {
			$NotificationFilteredData = Mage::getModel('neo_notification/notification')
															->getCollection()
															->addFieldToFilter('status', array('in' => array('2','4')))
															->setOrder('created_at', 'desc')
															->getData();
			$completeNotificationData = array();
			$temp = array();
			foreach ($NotificationFilteredData as $key => $value) {

				if($value['notfication_type'] == 1 ){
            if($value['image_link_type'] == 1 ){
                $_category = Mage::getModel('catalog/category')->load($value['category_id']);
                $notification_data = array(
                    'title' => $value['title'],
                    'type' => 'text',
                    'url_type' => 'category',
                    'category_id' => $value['category_id'],
                    'category_name' => $_category->getName(),
                );
            } elseif ($value['image_link_type'] == 2) {
                $notification_data = array(
                'title' => $value['title'],
                'type' => 'text',
                'url_type' => 'product',
                'product_id' => $value['product_id']);
            }

        } elseif ($value['notfication_type'] == 2) {
            if($value['image_link_type'] == 1 ){
                $_category = Mage::getModel('catalog/category')->load($value['category_id']);
                $notification_data = array(
                    'title' => $value['title'],
                    'type' => 'image',
                    'image_url' => $value['image_name'],
                    'url_type' => 'category',
                    'category_id' => $value['category_id'],
                    'category_name' => $_category->getName(),
                );
            } elseif ($value['image_link_type'] == 2) {
                $notification_data = array(
                    'title' => $value['title'],
                    'type' => 'image',
                    'image_url' => $value['image_name'],
                    'url_type' => 'product',
                    'product_id' => $value['product_id']
                );
            }
        } elseif ($value['notfication_type'] == 3) {
           $notification_data = array(
                'title' => $value['title'],
                'type' => 'link',
                'link_url' => $value['link_url']  
            );
        }
         elseif ($value['notfication_type'] == 5) {
           $notification_data = array(
                'title' => $value['title'],
                'type' => 'link',
                'link_url' => $value['link_url']
            );
        } elseif ($value['notfication_type'] == 4) {
           $notification_data = array(
                'title' => $value['title'],
                'type' => 'app_update',
                'link_url' => $value['link_url']
            );
        } else {
            $notification_data = array();
        }
				//$temp['product_id'] = $value['product_id'];
				//$productModel = Mage::getModel('catalog/product')->load($value['product_id']);
				//$temp['title'] = $value['title'];
				$completeNotificationData[] = $notification_data;
			}
			echo json_encode(array('status' => 1, 'data' => $completeNotificationData));
			exit;
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}

	public function getCustomerDataAction()
	{
		try {

			$toDate = date('Y-m-d'); 

			$collection = Mage::getModel('customer/customer')->getCollection()
						   ->addAttributeToSelect('firstname')
						   ->addAttributeToSelect('lastname')
						   ->addAttributeToSelect('mobile')
						   ->addAttributeToSelect('email');

			$customer = array();
			$i = 0;	   
			foreach ($collection as $item)
			{
				if(strpos($item->getCreatedAt(), $toDate) !== false)    
				{
					$customer[$i]['first_name'] = $item->getFirstname();
					$customer[$i]['last_name'] = $item->getLastname();
					$customer[$i]['mob_no'] = $item->getMobile();
					$customer[$i]['email'] = $item->getEmail();
					$customer[$i]['date_created'] = $item->getCreatedAt();
					$i++;
				}
			}

			echo json_encode(array('status' => 1, 'data' => $customer));
			exit; 
			
		} catch (Exception $e) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}

	}
 
	public function getRegistrationDataAction()
	{	

		$returnRegDataArray = array();

		try {
				$returnRegDataArray['status'] = 1;

			    $arrayGroup = array();
				$groups = Mage::helper('customer')->getGroups()->toOptionArray();
				$i = 0;
				foreach($groups as $group){
					if($group['value']==4){
						$arrayGroup[$i]['group_id']  = $group['value'];
						$arrayGroup[$i]['group_name']  = $group['label'];

						$i++;
					} 
				}

				$returnRegDataArray['customer_group'] = $arrayGroup;


				$read = Mage::getSingleton('core/resource')->getConnection('core_read');
				$res = $read->fetchAll("SELECT region_id as value, default_name as label FROM  `directory_country_region` WHERE  `country_id` =  'IN'");
				
				$arrayState = array();
				$i = 0;
				foreach ($res as $key => $value) {
					if($value['value']== 555 || $value['value']== 520 || $value['value'] >= 485 && $value['value'] <= 519 ){
						$arrayState[$i]['cus_state'] = $value['value'];
						$arrayState[$i]['cus_state_value'] = $value['label'];
						$i++;
					}
				}

				$returnRegDataArray['customer_state'] = $arrayState;


				$arrayRefferedBy = array();
				$obj = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts();
                $referedBYGroups = $obj->getAllOptions();
                $arrayRefferedBy[0]['asm_id'] = '';
            	$arrayRefferedBy[0]['asm_name'] = "Please Select"; 
                $i = 1;
                foreach($referedBYGroups as $refgroup){
                	if($refgroup['value'] > 0){
                		$arrayRefferedBy[$i]['asm_id'] = $refgroup['value'];
                		$arrayRefferedBy[$i]['asm_name'] = $refgroup['label'];
                		$i++;
                	}
                }
                
                $business_type = Mage::getSingleton("eav/config")->getAttribute("customer","business_type");
				$allOptions = $business_type->getSource()->getAllOptions(true, true);
                $j = 0;
                $business = array();
                foreach ($allOptions as $instance) { 
                    $val = $instance['value'];
                    $label = $instance['label'];
                    if(empty($val) && empty($label)){
                        $val = 0;
                        $label = 'Please Select Business Type';
                    }

                    $business[$j]['id'] = $val;
            		$business[$j]['label'] = $label;
            		$j++;
                }            

				$returnRegDataArray['customer_referedby'] = $arrayRefferedBy;
				$returnRegDataArray['business_type'] = $business;

				$json = json_encode($returnRegDataArray);

		        echo $json;
		        exit;
		    } catch (Exception $e) {
		        echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
		      exit;
		    }
	}
	// sending ebpin using sms
	public function generateEbpinAction()
	{
		
		try{
			$mobile = $this->getRequest()->getParam('mobile');
                
	        if(!empty($mobile)){
	          $customer_mobile = $mobile;
	        }else{
	            echo json_encode(array("status" => false, "message" => "Please Enter Mobile number"));
	            exit;
	        }

	        $otp_code = Mage::helper('ebpin')->generateOTP();

	        if ($otp_code)
	        {
	            
	            $uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
                $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
                $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

	            $to=$customer_mobile;
	            $msg='Your Single-use EB PIN is '.$otp_code;
	            $msg=urlencode($msg);
	            $data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

	            $ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
	            curl_setopt($ch, CURLOPT_POST, true);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	            $result=curl_exec($ch);


	            curl_close($ch);
	            
	        }
	        echo json_encode(array("status" => true, "message" => "EB PIN sent to mobile sucessfully.", "ebpin" => $otp_code));
	        exit;
		}catch(Exception $e){
			Mage::log($e->getMessage());
			echo json_encode(array("status" => false, "message" => $e->getMessage()));
	        exit;
		}
	} //sending eb pin function ends


	//from neweb
	public function registernewAction() 
	{
   
		$customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$_REQUEST['mobile']);
		// print_r($customerCount->getData()); exit;
		$gstin = $request['gstin'] = $_REQUEST['gstin'];
		$postcode = $request['postcode'] = $_REQUEST['pincode'];
		$pan = $request['pan'] = $_REQUEST['taxvat'];
		$email = $request['email'] = $_REQUEST['email'];

		$gstVal = preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9]{1}[A-Z]{1}[0-9A-Z]{1}$/", $gstin);
		$postcodeVal = preg_match("/^[0-9]{6}$/", $postcode);
		$panVal = preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $pan);
	   	$emailVal = preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$email);		
	   	if(!empty($gstin) && !empty($pan)){
		
			if(!$gstVal){
				echo json_encode(array('status' => 0, 'message' => 'GST Identification Number is not valid. It should be in this 11AAAAA1111Z1A1 format.'));
				exit;
			}

			if(!$panVal){
				echo json_encode(array('status' => 0, 'message' => 'PAN Number is not valid. It should be in this AAAAA1111A format.'));
				exit;
			}
		}
		if(!$postcodeVal){
			echo json_encode(array('status' => 0, 'message' => 'PIN CODE is not valid. It should be 6 digits format '));
			exit;
		}
		
		if(empty($_REQUEST['name'])) { 
			echo json_encode(array('status' => 0, 'message' => 'Please Enter Your Store Name.'));
			exit;
		}

		if(empty($_REQUEST['cus_city'])) { 
			echo json_encode(array('status' => 0, 'message' => 'Please Enter Valid Pincode.'));
			exit;
		}

		if(empty($_REQUEST['pincode'])) { 
			echo json_encode(array('status' => 0, 'message' => 'Please Enter Valid Pincode.'));
			exit;
		}

		//On SMS resolved on 23nd Mar'18 Mahesh Gurav
		if($_REQUEST['ebpin_user'] != $_REQUEST['ebpin_server']) {
			echo json_encode(array('status' => 0, 'message' => 'Eb Pin is Not Correct.'));
			exit;
		}
		
		if(empty($_REQUEST['asm_id'])) { 
			echo json_encode(array('status' => 0, 'message' => 'Please Select Referred By.'));
			exit;
		}

		if(count($customerCount) > 0) { 
			echo json_encode(array('status' => 0, 'message' => 'Mobile Number Already Register.'));
			exit; 
		}
		if(empty($_REQUEST['mobile']) || !preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile']) || count($customerCount) > 0) { 
			echo json_encode(array('status' => 0, 'message' => 'Please provide 10 Digit Mobile Number.'));
			exit; 
		} 
		if(!filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL)) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid Email.'));
			exit;
		}
		if(strlen($_REQUEST['password']) < 6) {
			echo json_encode(array('status' => 0, 'message' => 'Your password must be atleast 6 character long.'));
			exit;
		}
		if(!in_array($_REQUEST['device_type'],array('android', 'iphone'))) {
			echo json_encode(array('status' => 0, 'message' => 'Device type must be android or iphone .'));
			exit;
		}

		for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
			$randomNumber .= mt_rand(0, 9);
		}

		$data = array();
		$data['firstname'] = $_REQUEST['name'];
		$data['lastname'] = '.';
		$data['email'] = $_REQUEST['email'];
		$data['md5_customer_id'] = md5($_REQUEST['email']);
		$data['password'] = $_REQUEST['password'];
		$data['mobile'] = $_REQUEST['mobile'];
		$data['dob'] = date('Y-m-d', strtotime($_REQUEST['dob']));
		$data['prefix'] = $_REQUEST['prefix'];
		$data['device_type'] = $_REQUEST['device_type'];
		$data['push_id'] = $_REQUEST['push_id'];
		$data['group_id'] = 4;    
		$data['website_id'] = 1;

		$data['asm_map'] = $_REQUEST['asm_id'];
		$data['business_type'] = $_REQUEST['business_type'];
		//changes for customer state
		$pincode = 0;
		$city = '';
		$region_id = '';
		if(!empty($_REQUEST['pincode']) && strlen($_REQUEST['pincode']) == 6){
			$pincode = $_REQUEST['pincode']; 
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

			$query = 'SELECT r.region_id, c.city FROM `directory_country_region` as r LEFT JOIN `city_pincodes` as c ON c.state_code = r.code WHERE c.`pincode` ='. $pincode;
			$result = $connection->fetchRow($query);
			
			if(!empty($result)){
				$city = $result['city'];
				$region_id = $result['region_id'];
			}else{
				echo json_encode(array('status' => 0, 'message' => 'This PINCODE is not serviceable.'));
				exit;
			}

		}else{
			echo json_encode(array('status' => 0, 'message' => 'Please enter a valid PINCODE.'));
			exit;
		}
		$store_name = '';
		$repcode = '';
		if(!empty($_REQUEST['store_name'])) {
			$store_name = $_REQUEST['store_name'];
			list($repcode) = explode(' ', $store_name, 2);
			$repcode = preg_replace('/[^A-Za-z0-9\-]/', '', $repcode);
		}else{
			echo json_encode(array('status' => 0, 'message' => 'Please enter Partner Store Name.'));
			exit;
		} 

		for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
			$randomNumber .= mt_rand(0, 9);
		}

		$get_first_name_str = explode(" ", $_REQUEST['firstname']);
		$firstName = trim($get_first_name_str[0]);
		$repCode = 'Aff_'.$firstName.'_'.$randomNumber;

		$data['cus_country'] = 'IN';
		$data['cus_state'] = $region_id;
		$data['cus_city'] = $city;
		$data['landmark'] = $_REQUEST['landmark'];
		$data['isaffiliate'] = 1;
		$data['repcode'] = $repCode;

		$data['address'] = $_REQUEST['address'];
		$data['pincode'] = $_REQUEST['pincode']; 
		$data['version'] = $_REQUEST['version']; 
		$data['store_name'] = $store_name; 
		$data['affiliate_store'] = $store_name; 
		$data['gstin'] = $_REQUEST['gstin']; 
		$data['taxvat'] = $_REQUEST['taxvat'];  
		$data['business_type'] = $_REQUEST['business_type'];  
		//$data['repcode'] = $_REQUEST['verification_code'];
			// if($_REQUEST['password'] == 9825254281){
			// 	print_r($data); exit;
			// }
 
		try {
			// echo "<pre>";
			// print_r($data);
			// exit;
			$customer = Mage::getModel('customer/customer');
			$customer->addData($data);
			$customer->save();
			$customer->sendNewAccountEmail('registered','',1);
			/*$customerData = Mage::getModel('customapiv6/customer')->getCustomerInfo($customer->getId());*/

			$customer_data = array();
			$customer_data['id'] = $customer->getId();
			$customer_data['firstname'] = $customer->getData('firstname');
			$customer_data['email'] = $customer->getData('email');
			$customer_data['lastname'] = $customer->getData('lastname'); 
			$customer_data['taxvat'] = $customer->getData('taxvat'); 
			$customer_data['gstin'] = $customer->getData('gstin'); 
			$dob = $customer->getData('dob');  
			if(!empty($dob)) {
				$customer_data['dob'] = date('d/m/Y',strtotime($customer->getData('dob')));
			}
			$customer_data['mobile'] = $customer->getData('mobile');

			// creating the user quote and sending in response
			$quoteId = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($customer);
			$customer_data['quoteId'] = $quoteId;
			$customer_data['customer_id'] = $customer->getGroupId();
			$pin = (int) $customer->getData('pincode');

				if(strlen($pin) != 6){
					$customer_data['pincode'] = ''; 
				}else{
					$customer_data['pincode'] = $customer->getData('pincode');
				}

			$customer_data['store_name'] = $customer->getData('store_name');
			//corporate user changes
			$customer_data['is_corporate'] = '0';
			$customer_data['is_affiliate'] = '0';
			if($customer->getGroupId() == 4){
				$customer_data['is_affiliate'] = '1';				
			}else if(in_array($customer->getGroupId(), array(6,7))){
				$customer_data['is_corporate'] = '1';
			}

			//Mahesh Gurav edit on 05 Dec 2017
			//referred_by business type labels 
			$config    = Mage::getModel('eav/config');
			//referred by
			if($customer->getData('asm_map') !== null){
				$referred_by = $customer->getData('asm_map');
			    $referred_attr = $config->getAttribute('customer', 'asm_map');
			    $referred_by_options    = $referred_attr->setStoreId($storeId)->getSource()->getAllOptions();
			    // print_r($referred_by_options);
			    foreach ($referred_by_options as $key => $options) {
			    	if($options['value'] == $referred_by){
			    		$customer_referred_by = $options['label'];
			    	}
			    }
		    }
		    $customer_data['referred_by'] = $customer_referred_by;
		    //business type
		    $customer_buisness_type = '';
			if($customer->getData('business_type') !== null){
				$business_type = $customer->getData('business_type');
				$storeId   = 0;
			    $business_attr = $config->getAttribute('customer', 'business_type');
			    $business_type_options    = $business_attr->setStoreId($storeId)->getSource()->getAllOptions();
			    foreach ($business_type_options as $key => $options) {
			    	if($options['value'] == $business_type){
			    		$customer_buisness_type = $options['label'];
			    	}
			    }
			}
		    $customer_data['business_type'] = $customer_buisness_type;
			

			// update push id within notification table
			if(!empty($_REQUEST['version'])){
				$device_fcm = Mage::getModel('customapiv6/customer')->addFcmIdVersion($data['device_type'], $data['push_id'], $customer->getId(), $_REQUEST['version']);
			}else{
			$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($data['device_type'], $data['push_id'], $customer->getId());
			}

			echo json_encode(array('status' => 1, 'message'=> 'User Registered Successfully', 'login_type' => 'normal','customer_id'=>$customer->getGroupId(), 'user' => $customer_data));
			exit;
		} catch(Exception $ex) {
			$this->getResponse()->setBody(json_encode(array('status' => 0, 'message' => $ex->getMessage())));
		}  
	}


	public function checkUsersMobileNumberExistsAction()
	{

		if(empty($_REQUEST['mobile']) || !preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile'])) { 
			echo json_encode(array('status' => 0, 'message' => 'Please Enter Valid Mobile Number.'));
			exit;
		}

		$customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$_REQUEST['mobile']);

		if(count($customerCount) > 0)
		{
			echo json_encode(array('status' => 0, 'message' => 'User Mobile Already Exists.'));

		}
		else
		{
			echo json_encode(array('status' => 1, 'message' => 'User Mobile Does Not Exists.'));
		}

		exit;
	}

	public function checkUsersEmailExistsAction()
	{
		
		if(!filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL)) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid Email.'));
			exit;
		}

		$customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email',$_REQUEST['email']);

		if(count($customerCount) > 0)
		{
			echo json_encode(array('status' => 0, 'message' => 'User Email Already Exists.'));
		}
		else
		{
			echo json_encode(array('status' => 1, 'message' => 'User Email Does Not Exists.'));
		}

		exit;
	}

	public function cancelAction(){
		//echo $this->getRequest()->getParam('order_id');exit;
		$order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));

		if ($order->getId()) {
            if (Mage::helper('cancelorder/customer')->canCancelApp($order)) { 
                try {
                    $order->cancel();

                    if ($status = Mage::helper('cancelorder/customer')->getCancelStatus($store)) {
                        $order->addStatusHistoryComment('', $status)
                              ->setIsCustomerNotified(1);
                    }

                    $order->save();

                    $order->sendOrderUpdateEmail();

                	echo json_encode(array("status" => 1, "message" => 'Your order has been cancelled.')); 
                	exit;
                } catch (Exception $e) {

                	echo json_encode(array("status" => 0, "message" => 'Cannot cancel your order...'));
                	exit;
                }
            } else {

            	echo json_encode(array("status" => 0, "message" => 'Cannot cancel your order.......'));
	        	exit;

            }

            
        }

        echo json_encode(array("status" => 0, "message" => 'Order Id Does Not Exist'));
	    exit;
	}

	public function gistinAction(){
		$result = array();
		try{
			$request = $mobile = $this->getRequest()->getParams();
				$_customer = Mage::getModel('customer/customer');
			if(!empty($request['user_id'])){
				$_customer->load($request['user_id']);
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

	public function gistindetailsAction(){
		$app_request = $_REQUEST;
		// Mage::log(print_r($app_request),null,'gstinaction.log',true);
		$request = array();
		if(isset($app_request['pan_number'])){
			$request['pan'] = $app_request['pan_number'];
		}
		if(isset($app_request['address_line1'])){
			$request['address_line_1'] = $app_request['address_line1'];
		}
		if(isset($app_request['address_line2'])){
			$request['address_line_2'] = $app_request['address_line2'];
		}
		if(isset($app_request['post_code'])){
			$request['postcode'] = $app_request['post_code'];
		}
		if(isset($app_request['store_name'])){
			$request['partner_store_name'] = $app_request['store_name'];
		}
		if(isset($app_request['mobile'])){
			$request['mobile'] = $app_request['mobile'];
		}
		if(isset($app_request['gstin'])){
			$request['gstin'] = $app_request['gstin'];
		}
		if(isset($app_request['director_name'])){
			$request['director_name'] = $app_request['director_name'];
		}
		if(isset($app_request['contact_name'])){
			$request['contact_name'] = $app_request['contact_name'];
		}
		if(isset($app_request['city'])){
			$request['city'] = $app_request['city'];
		}
		if(isset($app_request['state'])){
			$request['state'] = $app_request['state'];
		}
		if(isset($app_request['country'])){
			$request['country'] = $app_request['country'];
		}
		if(isset($app_request['email'])){
			$request['email'] = $app_request['email'];
		}
		if(isset($app_request['user_id'])){
			$request['user_id'] = $app_request['user_id'];
		}

		$validation = Mage::helper('indiagst')->validateGstin($request);

		// var_dump($validation);

		if(!empty($validation)){
			echo json_encode(array('status'=>0,'message'=>$validation['message']));
			exit;
		}

		$saveData = array();
		$customer_id = '';
		try{
				$model = Mage::getModel('indiagst/indiagst');
			if(!empty($request['email']) && !empty($request['gstin']) && !empty($request['pan'])){
				//get customer id
				$customer = Mage::getModel("customer/customer"); 
		        $customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
		        $customer->loadByEmail($request['email']);
		        // $customer->load($request['user_id']);
				
				if($customer){
					$cutomer_id = $customer->getEntityId();
				}
				$collection = Mage::getResourceModel('indiagst/gstdetails_collection');
				$collection->addFieldToFilter('email',array('eq'=>$request['email']));
				
				if($collection->getData()){
					$message = "GST Details are already updated";
					$status = 0;
				}else if($customer_id == '0'){
					$message = "Please Sign In to Fill GST Details";
					$status = 0;
				}else{
					$saveData['partner_store_name'] = strip_tags($request['partner_store_name']);
					$saveData['director_name'] = strip_tags($request['director_name']);
					$saveData['contact_name'] = strip_tags($request['contact_name']);
					$saveData['address_line_1'] = strip_tags($request['address_line_1']);
					$saveData['address_line_2'] = strip_tags($request['address_line_2']);
					$saveData['country'] = strip_tags($request['country']);
					$saveData['state'] = $request['state'];
					$saveData['postcode'] = $request['postcode'];
					$saveData['city'] = $request['city'];
					$saveData['customer_id'] = $cutomer_id;
					$saveData['email'] = $request['email'];
					$saveData['mobile'] = $request['mobile'];
					$saveData['gstin'] = $gstin = $request['gstin'];
					$saveData['pan'] = $request['pan'];
					$currentTime = Varien_Date::now();
					$saveData['created_time'] = $currentTime;
					$saveData['update_time'] = $currentTime;
					$model->setData($saveData);
					$model->save();
					$message = "GST Details are successfully updated";
					$status = 1;
				}
				
			}else{
				$status = 0;
				$message = 'Invalid Request Please Check All Form Fields';
			}

		}catch(Exception $e){
			$status = 0;
			$message = $e->getMessage();
		}

		echo json_encode(array('status'=>$status,'message'=>$message));
		exit;
	}
	//password reset
	public function resetPasswordOtpAction()
	{
		$mobile = $_REQUEST['mobile'];
		$newpassword = $_REQUEST['newpassword'];
		$customer = Mage::getModel('customer/customer')->getCollection();
		$customer->addFieldToFilter('mobile',array("eq"=>$mobile));
		$customer->getFirstItem();

		$customer_data = $customer->getData();
		$_customer = $customer_data[0];
		// print_r($customer_data[0]); exit;
		$customer_email = $customer_data[0]['email'];
		if($customer_email == '') {
			echo json_encode(array('message' => 'Email ID not found for provided mobile number', 'status' => 0));
			exit;
		}

		if ($mobile) {
            

            /** @var $customer Mage_Customer_Model_Customer */

            if ($_customer['entity_id'] && $_customer['is_active'] == 1) {
     				//reset password here
            	try{
	            	$customerModel = Mage::getModel('customer/customer')->load($_customer['entity_id']);
	        		$customerModel->setPassword($newpassword);
	        		$customerModel->save();

	        	}catch(Exception $e){
	        		echo json_encode(array('message' => 'Error during password reset', 'status' => 0));
						exit;
	        	}
     		}
			$arr['status'] = 1;
			$arr['message'] = "Your password has been reset successfully";
		} else {
			$arr['status'] = 0;
			$arr['message'] = 'Invalid mobile number.';
        }
		echo json_encode($arr);
	}


	public function returnreasonAction(){
		$reason_label = Mage::helper('orderreturn')->getReason();
		$status_label = Mage::helper('orderreturn')->getStatusLabel();
	    $return_label = Mage::helper('orderreturn')->getReturn();

		$status_array = array();
		$i = 0;
		foreach ($status_label as $key => $value) {
			$status_array[$i]['id'] = $key;
			$status_array[$i]['label'] = $value;
			$i++;
		}

		$return_array = array();
		$i = 0;
		foreach ($return_label as $key => $value) {
			$return_array[$i]['id'] = $key;
			$return_array[$i]['label'] = $value;
			$i++;
		}

		$reason_array = array();
		$i = 0;
		foreach ($reason_label as $key => $value) {
			$reason_array[$i]['id'] = $key;
			$reason_array[$i]['label'] = $value;
			$i++;
		}

		$data = array();

		$data['reason'] = $reason_array;
		$data['return'] = $return_array;
		$data['status'] = $status_array;

		// echo json_encode(array('status'=1, 'data'=>$data, 'message'=>''));
		// echo json_encode(array('status'=1, 'message'=>'test'));
		$response['status'] = 1;
		$response['reason'] = $reason_array;
		$response['return'] = $return_array;
		// $response['return_status'] = $status_array;
		$response['message'] = '';
		// var_dump($response);
		echo json_encode($response);
		exit;
	}
	/*
	*@function name: savereturnAction {Orders return request web api}
	*@author: Mahesh Gurav
	*@date: 07/11/2017
	*/
	//http://180.149.246.49/electronicsbazaar/customapiv6/customer/savereturn?account_no=38738&imei=23232242423&beneficiary_name=Cxvsvdv&product_name=Apple%20iPad%20Air%20MH172HN/A%20(Gold,%2064%20GB)&auth_by_customer=1&bank_name=Sdvdv&reason=1&order_number=200056539&return_action=0&userid=29080&bank_ifsc=VDVSDVVD
	public function savereturnAction(){
		$request = $_REQUEST;
		$defective_imei =array();
	      $return_action =array();
	      $status = array();

	      $reason_label = Mage::helper('orderreturn')->getReason();
	      $status_label = Mage::helper('orderreturn')->getStatusLabel();
	      $return_label = Mage::helper('orderreturn')->getReturn();
	      // echo "hi";
		if(!empty($request['order_number']) && !empty($request['imei']) && !empty($request['return_action']) && !empty($request['userid']) && !empty($request['reason'])){
			// echo "in if";
			$increment_id = $request['order_number'];
			$customer_id = $request['userid'];
			$imei_no = $request['imei'];
			$return_action = $request['return_action'];
			$return_reason = $request['reason'];
			$prodName = $request['product_name'];

			//order details
			$_order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
			$order_status = $_order->getStatus();

			$imei_list = Mage::helper('orderreturn')->getInvoicedOrder($increment_id);
			if(!empty($imei_list)){
		        $imei_series = array_keys($imei_list);

				if(!in_array($imei_no, $imei_series)){
					//imei is not for this order
					echo json_encode(array('message' => 'Invalid Serial number', 'status' => 0));
					exit;
				}

	      	}else{
	      		//no imei entry for this order
	      		echo json_encode(array('message' => 'Not Found Serial number', 'status' => 0));
				exit;
	      	}

	      	//prepare data to save
	      	$defective_imei[] = $imei_no;
          	$return_request[$imei_no] = $return_action;
          	$status[$imei_no] = 1;
          	$reason[$imei_no] = $return_reason;

          	//merging request with order
	      	// $increment_id = 200043351;
          	$returnCollection = Mage::getModel("orderreturn/return")->getCollection();
          	$returnCollection->addFieldToFilter('order_number',array('eq'=>$increment_id));
          	$returnCollection->getFirstItem();
          	

          	$old_return = array();
          	$old_reason = array();
          	$old_status = array();
          	$imeis = $imei_no;
          	
          	if(count($returnCollection) > 0){
	          	foreach ($returnCollection as $return) {
	          		$entity_id = $return->getId();
	          		$defective_imei = $return->getCanceledImei(); //cancelled IMEI list
	          		$exisiting_reason = $return->getReason(); //reason json
	          		$exisiting_return_action = $return->getReturnAction(); //return_action json
	          		$exisiting_status = $return->getStatus(); //status json
	          	}
          		// echo $exisiting_return_action; 
          		$old_return = json_decode($exisiting_return_action,1);
          		$new_return = $old_return + $return_request;

          		$old_reason = json_decode($exisiting_reason,1);
          		$new_reason = $old_reason + $reason;

          		$old_status = json_decode($exisiting_status,1);
          		$new_status = $old_status + $status;

          		$imeis = $imei_no.",".$defective_imei;

          	}else{
          		$new_return = $return_request;
          		$new_reason = $reason;
          		$new_status = $status;
          	}
          	// echo $returnCollection->getSelect();
          	$savedata['return_action'] = json_encode($new_return);
	      	$savedata['status'] = json_encode($new_status);
	      	$savedata['reason'] = json_encode($new_reason);
          	$savedata['canceled_imei'] = $imeis;
          	$savedata['pickup_address'] = '';
          	$savedata['order_number'] = $increment_id;

          	$shippingAddress = $_order->getShippingAddress();
          	if($shippingAddress->getData()){
          		$savedata['pickup_address'] = $shippingAddress->getFirstname()." ".$shippingAddress->getLastname().", ";
          		foreach ($shippingAddress->getStreet() as $key => $street) {
          			$savedata['pickup_address'] .= trim($street).", ";
          		}

          		$savedata['pickup_address'] .= $shippingAddress->getCity().", ".$shippingAddress->getRegion().", ".$shippingAddress->getPostcode();
          	}

          	if(!empty($request['bank_name']) && !empty($request['bank_ifsc']) && !empty($request['account_no'])) {
              $banking['customer_id'] = $customer_id;
              $banking['bank_name'] = $request['bank_name'];
              $banking['bank_ifsc'] = $request['bank_ifsc'];
              $banking['account_number'] = $request['account_no'];
              $banking['beneficiary_name'] = (string) trim($request['beneficiary_name']);
              $bankingCollection = Mage::getModel("orderreturn/banking")->getCollection();
              $bankingCollection->addFieldToFilter('customer_id', $customer_id);
              $bankingCollection->addFieldToFilter('bank_name', $banking['bank_name']);
              $bankingCollection->addFieldToFilter('bank_ifsc', $banking['bank_ifsc']);
              $bankingCollection->addFieldToFilter('account_number', $banking['account_number']);
              $bankingCollection->addFieldToFilter('beneficiary_name', $banking['beneficiary_name']);
              $bankData = $bankingCollection->getData();
              if(!empty($bankData)){
                foreach ($bankData as $key => $value) {
                  if($key !== 0){
                    // echo "deleting bank id = ".$value['id'];
                    $delete_bank =  Mage::getModel('orderreturn/return')->load($value['id']);
                    $delete_bank->delete()->save();
                  }else{
                    $bank_id = $value['id'];
                  }
                }

              }else{
                  $bankingModel = Mage::getModel("orderreturn/banking");
                  $bankingModel->addData($banking);
                  $bankingModel->save();
                  $bank_id = $bankingModel->getId();
              }
              // echo $bank_id;
              $savedata['bank_id'] = $bank_id;
            }


          	// echo $table;
          	try{
          		$returnData = Mage::getModel("orderreturn/return")->getCollection()->addFieldToFilter('order_number',$savedata['order_number'])->getFirstItem()->getData();

	          	if($returnData['id']){
	            	$model = Mage::getModel("orderreturn/return")              
	                ->addData($savedata) 
	                ->setId($returnData['id'])
	                ->save();
	          	}else{

	            $model = Mage::getModel("orderreturn/return")              
	                ->addData($savedata)
	                ->save();
	            }

	            $message = 'Your return request is being submitted and will be responded back to you as soon as possible. Thank you for contacting us.';
	            $customerEmail = true;
	            $returnData = $model->getData();
        
	            //send customer email
	            Mage::helper('orderreturn')->sendCustomerEmail($returnData, $customerEmail, $message);

	            echo json_encode(array("status"=>1, "message"=>"Return request sent successfully."));
	            exit;
		      }
		      catch (Exception $e) {
		        echo json_encode(array("status"=>0, "message"=>"Unable to submit your request. Please, try again later"));
	            exit;
		      }

		}

		// echo json_encode(array("status"=>1, "message"=>"Return request sent successfully."));
  //       exit;
	}

	public function gstdetailsAction(){
		$data = array();
		$status = 0;
		if(!empty($_REQUEST['gstin'])){
			$gst_in = $_REQUEST['gstin'];

			$modelGst = Mage::getModel('gstdetails/gstdetails')->getCollection();
			$modelGst->addFieldToFilter('gstin',array('eq'=>$gst_in));
			if(count($modelGst) > 0){
				$gst_details = $modelGst->getData(); 
				// print_r($gst_details);
				$remove =  array('id','customer_id','created_time','update_time');
				foreach ($gst_details as $key=>$val) {
					# code...
					$data = $val;
					$message = 'GST Details Found For GST IN '.$gst_in;
				}

				$status = 1;
			}else{
				$message = 'GST Details Not Found For GST IN '.$gst_in;
			}
		}else{
			$message = 'GST IN Required to get GST Details';
		}
		echo json_encode(array('status'=>$status,'data'=>$data,'message'=>$message));
	}


	//verify customer for all details filled by him 
	//author: Mahesh Gurav
	//date: 02 Dec 2017
	public function verifyAction(){
		$request = $this->getRequest()->getParams();
		$status = 1;
		$message = 'Success';

		echo json_encode(array('status'=>$status,'message'=>$message));	
	}

	//update customer for all account details
	//author: Mahesh Gurav
	//date: 04 Dec 

	public function updateAction(){
		$request = $this->getRequest()->getParams();
		$status = 1;
		$message = 'Success';

		echo json_encode(array('status'=>$status,'message'=>$message));
	}

	//recently viewed for app
	//Mahesh Gurav
	//15 Jan 2018
	public function getRecentlyViewedAction(){

		echo json_encode(array('status'=>0,'data'=>'','message'=>'not found recently viewed products'));
		exit;
		if(!empty($_REQUEST['user_id'])){
			$customer_id = $_REQUEST['user_id'];
			try{
				$model = Mage::getModel('customapiv6/customer')->getRecentlyViewedByCustomer($customer_id);
		        
		 		$data = array();
		 		if(!empty($model) && count($model) > 0){
		 			$ij = 0;
			        foreach ($model as $key => $product) {
			            $specialprice = '';        	
			        	$_product = Mage::getModel('catalog/product')->load($product['product_id']);
			        	$image_url = (string) Mage::helper('catalog/image')->init($_product, 'small_image');
						$price = (int) Mage::helper('tax')->getPrice($_product, $_product->getPrice());
						$data[$ij]['product_id'] = $product['product_id'];
						$data[$ij]['name'] = $_product->getName();
						$data[$ij]['new_launch'] = $_product->getNewLaunch();
						$specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($_product->getSpecialPrice() , $_product->getTaxClassId()));						
						$data[$ij]['price'] = (int) round(Mage::helper('customapiv6')->getPriceWithTax($_product->getPrice() , $_product->getTaxClassId()));
				        $data[$ij]['special_price'] = ($specialprice > 0)?$specialprice:null;
						$data[$ij]['image'] = $image_url;
						$data[$ij]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($_product);
						if(!empty($_REQUEST['user_id'])){
							$data[$ij]['is_favourite'] = Mage::getModel('customapiv6/catalog')->checkIsInWishlist($_REQUEST['user_id'],$_product->getEntityId());
						}else{
							$data[$ij]['is_favourite'] = false;
						}
						$data[$ij]['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($_product->getSku());
						$ij ++;
			        }
			        echo json_encode(array('status'=>1,'data'=>$data,'message'=>'successfully retrieved recently viewed products'));
		 		}else{
		 			echo json_encode(array('status'=>0,'data'=>'','message'=>'not found recently viewed products'));
		 		}
		 		exit;

		 	}catch(Exception $e){
		 		echo json_encode(array('status'=>0,'data'=>'','message'=>$e->getMessage()));
					exit;
		 	}
		}
	}

	//delete address revamp feature on 18th JAN 2018 Mahesh Gurav
	public function deleteAddressAction(){
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['address_id'])){
			try{
				$addressId = $_REQUEST['address_id'];
				$customer_id = $_REQUEST['user_id'];
				$address = Mage::getModel('customer/address')->load($addressId);
				if($address->getEntityId() > 0){
					$address->delete();
					echo json_encode(array('status'=>1,'message'=>'Successfully deleted data for address #'.$addressId));
					exit;
				}else{
					echo json_encode(array('status'=>0,'message'=>'No data for address #'.$addressId));
					exit;	
				}
			}catch(Exception $e){
				echo json_encode(array('status'=>0,'message'=>$e->getMessage()));
				exit; 
			}
		}
	}

	public function sharewishlistAction()
	{        
        $emailids = $this->getRequest()->getParam('emails'); //echo '<pre>', print_r($request);
        $message = nl2br(htmlspecialchars((string) $this->getRequest()->getParam('message')));
        $customerId = $this->getRequest()->getParam('cid');

        if($customerId != '' && !empty($emailids))
        {       
           $emails = explode(",",$emailids); 
		   foreach ($emails as $index => $email) {
			$email = trim($email);
			if (!Zend_Validate::is($email, 'EmailAddress')) {                    
			echo json_encode(array('status'=>0,'message'=>'Please input a valid email address.'));exit;                    
			}                
		}
	        
        try{
	        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
	        $customer = Mage::getModel('customer/customer')->load($customerId);

	            Mage::helper('wishlist')->calculate();            
	            $emailModel = Mage::getModel('core/email_template');
	            $sharingCode = $wishlist->getSharingCode();
	            //this var returning empty
	            //$wishlistBlock = Mage::getSingleton('core/layout')->createBlock('wishlist/share_email_items')->toHtml();
	            Mage::register('wishlist',$wishlist);
				$block = Mage::getSingleton('core/layout')->createBlock('wishlist/share_email_items');
				$wishlistBlock = $block->setData('area','frontend')->toHtml();				
	            
	            foreach ($emails as $email) {
		            $emailModel->sendTransactional(
		                Mage::getStoreConfig('wishlist/email/email_template'),
		                Mage::getStoreConfig('wishlist/email/email_identity'),
		                $email,
		                null,
		                array(
		                    'customer'       => $customer,
		                    'salable'        => $wishlist->isSalable() ? 'yes' : '',
		                    'items'          => $wishlistBlock,
		                    'addAllLink'     => Mage::getUrl('*/shared/allcart', array('code' => $sharingCode)),
		                    'viewOnSiteLink' => Mage::getUrl('*/shared/index', array('code' => $sharingCode)),
		                    'message'        => $message
		                )
		            );
		        }

	            $wishlist->setShared(1);
	            $wishlist->save();

	            Mage::dispatchEvent('wishlist_share', array('wishlist' => $wishlist));
	            echo json_encode(array('status'=>1,'message'=>'Your Wishlist has been shared.'));exit;
		 	}catch(Exception $e){
		 		echo json_encode(array('status'=>0,'message'=>$e->getMessage()));exit;					
		 	}	            
	    }
	    else{
	     echo json_encode(array('status'=>0,'message'=>'Invalid data found.'));exit;	
	    }	    	           
	}

	public function stocklistAction(){
		ini_set('max_execution_time','1000');   
        ini_set('memory_limit', '-1');
         
        //active stock collection
        $collection = Mage::getModel('catalog/product')
         ->getCollection()
         ->addAttributeToSelect('*')
         ->joinField('qty',
             'cataloginventory/stock_item',
             'qty',
             'product_id=entity_id',
             '{{table}}.stock_id=1',
             'left'
         )
         ->addAttributeToFilter('is_in_stock', array('eq' => 1))
         ->addAttributeToFilter('qty', array('gt'=>0));
        Mage::getSingleton('cataloginventory/stock')
            ->addInStockFilterToCollection($collection);
        $collection->getSelect()->order('name ASC')->having('qty>0');

        $group_by_category = array();
        $i = 0;
         foreach ($collection as $product) {
            $categories = $product->getCategoryIds(); 
            if($categories[1]){
                $parent_cat = $categories[1];
            }else{
                $parent_cat = $categories[0];
            }

            $_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($parent_cat);
            // $product_qty = $product->getQty();
            $category_name = trim($_cat->getName());
            $product_name = $product->getName();

            $product_price = (int) round(Mage::helper('tax')->getPrice($product, $product->getFinalPrice()));
            if($product->getSpecialPrice()){
              $product_price = (int) round(Mage::helper('tax')->getPrice($product, $product->getSpecialPrice()));
            }
            $sku = $product->getSku();
            if($product_price !== 0 && strpos($sku, 'SYG') === false && strpos($sku, 'SG') === false && strpos($sku, 'rpcdev') === false && strpos($sku, 'dev') === false && $category_name !== 'Sell Your Gadget' && $category_name !== ''){
                $group_by_category[$category_name][$i]['name'] = $product_name;
                $group_by_category[$category_name][$i]['price'] = $product_price;
                $group_by_category[$category_name][$i]['sku'] = $sku;
                $i++;
            }
         }
        // asort($group_by_category);



        $header = array('SKU','PRODUCT NAME','CATEGORY','PRICE (INR)');
        //set file name same to avoid multiple files
        $filename = "stock.csv";
        $file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;
        //open file to write new data
        $filepath = fopen($file,"w");
        fputcsv($filepath, $header);
        
        foreach ($group_by_category as $category_name => $item) {
          // $product = array_values($item);
          // $sku = $product[0];
        	foreach ($item as $key => $sku) {
        		# code...
          		fputcsv($filepath, array($sku['sku'],$sku['name'],$category_name,$sku['price']));
        	}
        }
        fclose($filepath);

        //code to force download excel file of active stock list
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file)); 
        echo readfile($file);
        exit;
	}		


	/*
	* @date : 16th March 2018
	* @author : Sonali Kosrabe
	* @purpose : To get customer address
	*/
	public function getAddressDataAction(){
		$address_id = $this->getRequest()->getParam('address_id');
		try{
			$customer = Mage::getModel('customer/address')->load($address_id);
			
			echo json_encode(array('status'=>1,'message'=>'Please check customer data','data'=>$customer->getData()));
		}catch(Exception $e){
			echo json_encode(array('status'=>0,'message'=>'Unable to load customer address.'));
		}
	}

    //Enable or disable EBpin otp (Code by JP).
	public function enableEBpinAction(){
		echo json_encode(array('status'=>1));
	}

	// sending ebpin using sms (Code by JP.)
	public function sendOrderEbPinAction()
	{
		
		try{
			$mobile     = $this->getRequest()->getParam('mobile');
			$email      = $this->getRequest()->getParam('email');
			$store_name = $this->getRequest()->getParam('store_name');
                
	        if(!empty($mobile)){
	          $customer_mobile = $mobile;
	        }else{
	            echo json_encode(array("status" => false, "message" => "Please Enter Mobile number"));
	            exit;
	        }

	        if(empty($email) || empty($store_name))
	        {
	            echo json_encode(array("status" => false, "message" => "Email ID or Name not found."));
	            exit;
	        }	        

	        $otp_code = Mage::helper('ebpin')->generateOTP();

	        if ($otp_code)
	        {
	            
	            $uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
            	$passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
            	$feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

	            $to=$customer_mobile;
	            $msg='Your Single-use EB PIN is '.$otp_code;
	            $msg=urlencode($msg);
	            $data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

	            $ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
	            curl_setopt($ch, CURLOPT_POST, true);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	            $result=curl_exec($ch);


	            curl_close($ch);

                    //code for send order email.
                    // Transactional Email Template's ID
                    $templateId = 34;
                 
                    // Set sender information            
                    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
                    $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');        
                    $sender = array('name' => $senderName,
                                'email' => $senderEmail);
                    
                    // Set recepient information
                    $recepientEmail = $email;
                    $recepientName  = $store_name;        
                    
                    // Get Store ID        
                    $storeId = Mage::app()->getStore()->getId();
                 
                    // Set variables that can be used in email template
                    $vars = array('otp' => $otp_code);
                            
                    $translate  = Mage::getSingleton('core/translate');
                 
                    // Send Transactional Email
                    Mage::getModel('core/email_template')
                        ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);	            
	            
	        }
	        echo json_encode(array("status" => true, "message" => "EB PIN sent to you via SMS and Email.", "ebpin" => $otp_code));
	        exit;
		}catch(Exception $e){
			Mage::log($e->getMessage());
			echo json_encode(array("status" => false, "message" => $e->getMessage()));
	        exit;
		}
	} //sending eb pin function ends	

	//IBM sign up function for IBM employees
	//Mahesh Gurav
	//05 Jun 2018
	public function ibmsignupAction() 
	{
   
		$customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$_REQUEST['mobile']);

		$email = $request['email'] = $_REQUEST['email'];
		$domain_for_signup = 'in.ibm.com';

		$check_email_domain = explode("@", $email);
		if($check_email_domain[1] !== $domain_for_signup){
			echo json_encode(array('status' => 0, 'message' => 'Enter email group is not valid for this program signup'));
			exit;
		}

		$emailVal = preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$email);		
	   	
	   	if(empty($_REQUEST['name'])) { 
			echo json_encode(array('status' => 0, 'message' => 'Please Enter Your Store Name.'));
			exit;
		}
		
		//On SMS resolved on 23nd Mar'18 Mahesh Gurav
		if($_REQUEST['ebpin_user'] != $_REQUEST['ebpin_server']) {
			echo json_encode(array('status' => 0, 'message' => 'Eb Pin is Not Correct.'));
			exit;
		}
		
		if(count($customerCount) > 0) { 
			echo json_encode(array('status' => 0, 'message' => 'Mobile Number Already Register.'));
			exit; 
		}
		
		if(empty($_REQUEST['mobile']) || !preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile']) || count($customerCount) > 0) { 
			echo json_encode(array('status' => 0, 'message' => 'Please provide 10 Digit Mobile Number.'));
			exit; 
		} 
		
		if(!filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL)) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid Email.'));
			exit;
		}
		
		// if(strlen($_REQUEST['password']) < 6) {
		// 	echo json_encode(array('status' => 0, 'message' => 'Your password must be atleast 6 character long.'));
		// 	exit;
		// }
		if(!in_array($_REQUEST['device_type'],array('android', 'iphone'))) {
			echo json_encode(array('status' => 0, 'message' => 'Device type must be android or iphone .'));
			exit;
		}

		for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
			$randomNumber .= mt_rand(0, 9);
		}

		$data = array();

		//set IBM group id
		$data['group_id'] = 10;    
		$data['website_id'] = 1;
		$data['cus_country'] = 'IN';
		$data['isaffiliate'] = 1;
		$data['version'] = $_REQUEST['version']; 
		
		$explode_name = explode(" ", $_REQUEST['name']);
		$data['firstname'] = $explode_name[0];
		if($explode_name[1]){
			$data['lastname'] = $explode_name[1];
		}else{
			$data['lastname'] = ".";
		}
		$data['email'] = $_REQUEST['email'];
		$data['md5_customer_id'] = md5($_REQUEST['email']);
		// $data['password'] = $_REQUEST['password'];
		$data['mobile'] = $_REQUEST['mobile'];
		$data['device_type'] = $_REQUEST['device_type'];
		$data['push_id'] = $_REQUEST['push_id'];
		

		//repcode generation
		for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
			$randomNumber .= mt_rand(0, 9);
		}
		$firstName = trim($data['firstname']);
		$repCode = 'Aff_'.$firstName.'_'.$randomNumber;
		
		$data['repcode'] = $repCode;
		$passwordLength = 10;
		try {
			
			$customer = Mage::getModel('customer/customer');
			
			//set auto generated password
			$password = $customer->generatePassword($passwordLength);
			$data['password'] = $password;
			
			$customer->addData($data);
			$customer->save();
			$customer->sendNewAccountEmail('ibmregistered','',1);
			/*$customerData = Mage::getModel('customapiv6/customer')->getCustomerInfo($customer->getId());*/

			$customer_data = array();
			$customer_data['id'] = $customer->getId();
			$customer_data['firstname'] = $customer->getData('firstname');
			$customer_data['email'] = $customer->getData('email');
			$customer_data['lastname'] = $customer->getData('lastname'); 
			$customer_data['mobile'] = $customer->getData('mobile');

			// creating the user quote and sending in response
			$quoteId = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($customer);
			$customer_data['quoteId'] = $quoteId;
			$customer_data['customer_id'] = $customer->getGroupId();
			$customer_data['is_affiliate'] = '1';
			$customer_data['is_ibm'] = 'Yes';

			// update push id within notification table
			if(!empty($_REQUEST['version'])){
				$device_fcm = Mage::getModel('customapiv6/customer')->addFcmIdVersion($data['device_type'], $data['push_id'], $customer->getId(), $_REQUEST['version']);
			}else{
			$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($data['device_type'], $data['push_id'], $customer->getId());
			}

			echo json_encode(array('status' => 1, 'message'=> 'User Registered Successfully', 'login_type' => 'normal','customer_id'=>$customer->getGroupId(), 'user' => $customer_data));
			exit;
		} catch(Exception $ex) {
			$this->getResponse()->setBody(json_encode(array('status' => 0, 'message' => $ex->getMessage())));
		}  
	}
}	
?>
