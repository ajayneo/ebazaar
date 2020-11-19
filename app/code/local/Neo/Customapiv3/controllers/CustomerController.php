<?php
class Neo_Customapiv3_CustomerController extends Neo_Customapiv3_Controller_HttpAuthController
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
		$customer_model = Mage::getModel('customapiv3/customer');
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
		$quoteId = $_REQUEST['quoteId'];
		$social_type = $_REQUEST['social_type'];
		$storeId = Mage::app()->getStore();
		$this->getResponse()->setHeader('Content-type','application/json');
		$device_type = $_REQUEST['device_type'];
		$push_id = $_REQUEST['push_id'];

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
					$quoteId = Mage::getModel('customapiv3/customer')->createQuoteAndAssignCustomer($customer_model_eb);
				}

				// get cart total quantity
				$cart_total_quantity = Mage::getModel('customapiv3/customer')->getCartItemQuantity($quoteId);

				$customer_data = array();
				$customer_data['id'] = $customer->getId();
				$customer_data['email'] = $_REQUEST['email'];
				$customer_data['firstname'] = $customer->getData('firstname');
				$customer_data['lastname'] = $customer->getData('lastname');
				$dob = $customer->getData('dob');
				if(!empty($dob)) {
					$customer_data['dob'] = date('d/m/Y',strtotime($customer->getData('dob')));
				}
				$customer_data['mobile'] = $customer->getData('mobile');
				$customer_data['profile_picture'] = urlencode($customer->getData('profile_picture'));

				// update push id within notification table
				$saved_flag = Mage::getModel('customapiv3/customer')->addPushIdAndDeviceType($device_type, $push_id, $customer->getId());
				echo json_encode(array('status' => 1, 'message'=> 'LoggedIn Successfully', 'token' => $customer->getMd5CustomerId(), 'user' => $customer_data, 'login_type' => 'social', 'quoteId' => $quoteId, 'cart_total_qty' => $cart_total_quantity));
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
							$quoteId = Mage::getModel('customapiv3/customer')->createQuoteAndAssignCustomer($customer_model_eb);
						}

						// get cart total quantity
						$cart_total_quantity = Mage::getModel('customapiv3/customer')->getCartItemQuantity($quoteId);

						$customer_data = array();
						$customer_data['id'] = $customer1->getId();
						$customer_data['email'] = $_REQUEST['email'];
						$customer_data['firstname'] = $customer1->getData('firstname');
						$customer_data['lastname'] = $customer1->getData('lastname');
						$dob = $customer->getData('dob');
						if(!empty($dob)) {
							$customer_data['dob'] = date('d/m/Y',strtotime($customer1->getData('dob')));
						}
						$customer_data['mobile'] = $customer1->getData('mobile');
						$customer_data['profile_picture'] = urlencode($customer1->getData('profile_picture'));
						// update push id within notification table
						$saved_flag = Mage::getModel('customapiv3/customer')->addPushIdAndDeviceType($device_type, $push_id, $customer1->getId());
						echo json_encode(array('status' => 1, 'message'=> 'LoggedIn Successfully', 'token' => $customer1->getMd5CustomerId(), 'user' => $customer_data, 'login_type' => 'social', 'quoteId' => $quoteId, 'cart_total_qty' => $cart_total_quantity));
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
					$quoteId = Mage::getModel('customapiv3/customer')->createQuoteAndAssignCustomer($new_customer);
				}

				// get cart total quantity
				$cart_total_quantity = Mage::getModel('customapiv3/customer')->getCartItemQuantity($quoteId);

				$customer_data = array();
				$customer_data['id'] = $new_customer->getId();
				$customer_data['email'] = $_REQUEST['email'];
				$customer_data['firstname'] = $new_customer->getData('firstname');
				$customer_data['lastname'] = $new_customer->getData('lastname');
				$dob = $new_customer->getData('dob');
				if(!empty($dob)) {
					$customer_data['dob'] = date('d/m/Y',strtotime($new_customer->getData('dob')));
				}
				$customer_data['mobile'] = $new_customer->getData('mobile');
				$customer_data['profile_picture'] = urlencode($new_customer->getData('profile_picture'));

				// update push id within notification table
				$saved_flag = Mage::getModel('customapiv3/customer')->addPushIdAndDeviceType($device_type, $push_id, $new_customer->getId());

				echo json_encode(array('status' => 1, 'message'=> 'LoggedIn Successfully', 'token' => $new_customer->getMd5CustomerId(), 'user' => $customer_data, 'login_type' => 'social', 'quoteId' => $quoteId, 'cart_total_qty' => $cart_total_quantity));
				exit;
			}
		} else {
			try {
				$email = $_POST['email'];
				$password = $_POST['password'];

				$customer = Mage::getModel('customer/customer')->setWebsiteId(1);
				if($customer->authenticate($email, $password)) {
					$customer->loadByEmail($email);
					// load customer model if present
					$quote = Mage::getModel('sales/quote')->loadByCustomer($customer);
					$quoteId = $quote->getId();

					// if customer quote id not present then create quote and assign it to customer
					if(empty($quoteId)){
						$quoteId = Mage::getModel('customapiv3/customer')->createQuoteAndAssignCustomer($customer);
					}

					// get cart total quantity
					$cart_total_quantity = Mage::getModel('customapiv3/customer')->getCartItemQuantity($quoteId);

					$result = array('status' => 1, 'token' =>  $customer->getMd5CustomerId());

					$customer_data = array();
					$customer_data['id'] = $customer->getId();
					$customer_data['email'] = $_REQUEST['email'];
					$customer_data['firstname'] = $customer->getData('firstname');
					$customer_data['lastname'] = $customer->getData('lastname');
					$dob = $customer->getData('dob');
					if(!empty($dob)) {
						$customer_data['dob'] = date('d/m/Y',strtotime($customer->getData('dob')));
					}
					$customer_data['mobile'] = $customer->getData('mobile');
					$customer_data['profile_picture'] = urlencode($customer->getData('profile_picture'));

					// update push id within notification table

					$saved_flag = Mage::getModel('customapiv3/customer')->addPushIdAndDeviceType($device_type, $push_id, $customer->getId());

					echo json_encode(array('status' => 1, 'message'=> 'LoggedIn Successfully','token' => $customer->getMd5CustomerId(), 'user' => $customer_data, 'login_type' => 'normal', 'quoteId' => $quoteId, 'cart_total_qty' => $cart_total_quantity));
					exit;
				}
			} catch(Exception $ex) {
				$this->getResponse()->setBody(json_encode(array('status' => 0, 'message' =>  $ex->getMessage())));
			}
		}
	}

	public function registerAction()
	{
		/*$required_fields = array('firstname', 'lastname', 'email', 'password', 'mobile', 'dob', 'prefix','device_type');
		$posted_fields = array_intersect($required_fields, array_keys($_POST));

		if(count($posted_fields) < count($required_fields)) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide required fields.'));
			exit;
		}*/
		if(!in_array($_REQUEST['prefix'],array('Mr.', 'Ms.')) || $_REQUEST['prefix'] == "") {
			echo json_encode(array('status' => 0, 'message' => 'Prefix must be either Mr. or Ms.'));
			exit;
		}
		if(empty($_REQUEST['firstname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['firstname'])) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid first name.'));
			exit;
		}
		if(empty($_REQUEST['lastname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['lastname'])) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid last name.'));
			exit;
		}
		if(empty($_REQUEST['mobile']) || !preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile'])) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid Mobile.'));
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
		$data['group_id'] = 1;
		$data['website_id'] = 1;
		try {
			$customer = Mage::getModel('customer/customer');
			$customer->addData($data);
			$customer->save();
			$customer->sendNewAccountEmail('registered','',1);
			/*$customerData = Mage::getModel('customapiv3/customer')->getCustomerInfo($customer->getId());*/

			$customer_data = array();
			$customer_data['id'] = $customer->getId();
			$customer_data['firstname'] = $customer->getData('firstname');
			$customer_data['lastname'] = $customer->getData('lastname');
			$dob = $customer->getData('dob');
			if(!empty($dob)) {
				$customer_data['dob'] = date('d/m/Y',strtotime($customer->getData('dob')));
			}
			$customer_data['mobile'] = $customer->getData('mobile');

			// creating the user quote and sending in response
			$quoteId = Mage::getModel('customapiv3/customer')->createQuoteAndAssignCustomer($customer);
			$customer_data['quoteId'] = $quoteId;

			// update push id within notification table
			$saved_flag = Mage::getModel('customapiv3/customer')->addPushIdAndDeviceType($data['device_type'], $data['push_id'], $customer->getId());

			echo json_encode(array('status' => 1, 'message'=> 'User Registered Successfully', 'login_type' => 'normal', 'user' => $customer_data));
			exit;
		} catch(Exception $ex) {
			$this->getResponse()->setBody(json_encode(array('status' => 0, 'message' => $ex->getMessage())));
		}
	}

	public function editProfileAction()
	{
		$customerId = $_REQUEST["userid"];
		$this->validateCustomer($customerId);
		if(!preg_match("/^[a-zA-Z]*$/",$_REQUEST['firstname'])) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid first name.'));
			exit;
		}
		if(!preg_match("/^[a-zA-Z]*$/",$_REQUEST['lastname'])) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid last name.'));
			exit;
		}
		/*if(empty($_REQUEST['dob']) || !preg_match("/^[0-9]{5,10}$/",$_REQUEST['dob'])) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid Telephone.'));
			exit;
		}*/
		if(!preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile'])) {
			if(empty($_REQUEST['mobile'])) {
			} else {
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid Mobile.'));
				exit;
			}

		}
		$data = array();
		$data['firstname'] = $_REQUEST['firstname'];
		$data['lastname'] = $_REQUEST['lastname'];
		$data['company'] = $_REQUEST['company'];
		$data['mobile'] = $_REQUEST['mobile'];
		$data['dob'] = date('Y-m-d', strtotime($_REQUEST['dob']));
		try {
			$customer = Mage::getModel('customer/customer')->load($customerId);
			$customer->addData($data);
			$customer->save();
			$customer_data = array();
			$customer_data['id'] = $customer->getId();
			$customer_data['firstname'] = $customer->getData('firstname');
			$customer_data['lastname'] = $customer->getData('lastname');
			$customer_data['company'] = $customer->getData('company');
			$dob = $customer->getData('dob');
			if(!empty($dob)) {
				$customer_data['dob'] = date('d/m/Y',strtotime($customer->getData('dob')));
			}
			$customer_data['mobile'] = $customer->getData('mobile');
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
		$customerData = Mage::getModel('customapiv3/customer')->getCustomerInfo($customerId);
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
		$status_flag = Mage::getModel('customapiv3/customer')->updateCustomerInfo($customerId, $customerData);
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
				'lastname'   => $_REQUEST['lastname'],
				'country_id' => $_REQUEST['country_id'],
				'region_id'  => $_REQUEST['region'],
				'city'       => $_REQUEST['city'],
				'street'     => array($_REQUEST['street_1'],$_REQUEST['street_2']),
				'telephone'  => $_REQUEST['telephone'],
				'postcode'   => $_REQUEST['postcode'],
				'is_default_billing'  => (bool)$_REQUEST['is_default_billing'],
				'is_default_shipping' => (bool)$_REQUEST['is_default_shipping']
			);
			$address_id = $address->create($customer_id,$newCustomerAddress);
			echo json_encode(array('status' => 1, 'address_id' => $address_id, 'message'=> 'New address added successfully'));
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
      	$addressListArray[] = $temp;
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
		$wishlist_container = Mage::getModel('customapiv3/customer')->getWishlistListing($customerId);
		$wishlist_container_blank_check = array_filter($wishlist_container);

		if (!empty($wishlist_container_blank_check)) {
			echo json_encode(array('status' => 1, 'data' => $wishlist_container));
		} else {
			echo json_encode(array('status' => 0, 'message' => 'Wishlist Empty'));
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

		$customer_model = Mage::getModel('customapiv3/customer');
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
		$customer_model = Mage::getModel('customapiv3/customer');
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
		$customer_id = $_POST['userid'];
		$this->validateCustomer($customer_id);
		$order_id = $_POST['order_id'];

		$customer_model = Mage::getModel('customapiv3/customer');
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
															->addFieldToFilter('status', array('in' => array('2')))
															->setOrder('created_at', 'desc')
															->getData();
			$completeNotificationData = array();
			$temp = array();
			foreach ($NotificationFilteredData as $key => $value) {
				$temp['product_id'] = $value['product_id'];
				$productModel = Mage::getModel('catalog/product')->load($value['product_id']);
				$temp['title'] = $value['title'];
				$completeNotificationData[] = $temp;
			}
			echo json_encode(array('status' => 1, 'data' => $completeNotificationData));
			exit;
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}
}
?>
