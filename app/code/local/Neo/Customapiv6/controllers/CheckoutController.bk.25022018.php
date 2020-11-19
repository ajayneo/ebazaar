<?php
class Neo_Customapiv6_CheckoutController extends Neo_Customapiv6_Controller_HttpAuthController
{

		/**
	   	 * @desc : save billing address and shipping address (only if billing_use_for_shipping_yes is set) in qoute.
	     * @package     Neo_Mobileapi
	     * @author      pradeep sanku
	     */
		public function saveBillingAddressAction() {
			// emailid validation

    
			$quote_id = $_REQUEST['quote_id'];
			$customer_id = $_REQUEST['customer_id'];
			$billingAddressId = $_REQUEST['billingAddressId'];
			$firstname = $_REQUEST['firstName'];
			$lastname = '.';
			$email = $_REQUEST['email'];
			
			$postcode = $_REQUEST['postcode'];
			$password = $_REQUEST['password'];
			$confirm_password = $_REQUEST['confirm_password'];
			$billing_use_for_shipping_yes = $_REQUEST['billing_use_for_shipping_yes'];
			$customer_cform = $_REQUEST['customer_cform'];

			$street1 = $_REQUEST['street'];
			$street2 = $_REQUEST['street1'];
			$company = $_REQUEST['firstName'];

			$street[0] = $street1;
			$street[1] = $street2;

			$street = $_REQUEST['street'];
			$gstin = '';
			if(!empty($_REQUEST['gstin'])){

				$gstVal = preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9]{1}[A-Z]{1}[0-9A-Z]{1}$/", $_REQUEST['gstin']);

				if(!$gstVal){
					echo json_encode(array('status' => 0, 'message' => 'Please enter valid GST IN'));
					exit;
				}
				$gstin = $_REQUEST['gstin'];
			}

			$addressData = array();

			$shippingMethod = "freeshipping_freeshipping";

			$websiteId = Mage::app()->getWebsite()->getId();
			$store = Mage::app()->getStore();

			if($customer_id){
				// if checkout is already login user's
				$customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->load($customer_id);
			}else{
				// if checkout is logged out user's
				$customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($email);
				if($customer->getId()==""){
					if($password == $confirm_password) {

						if(strlen($_REQUEST['password']) < 6) {
							echo json_encode(array('status' => 0, 'message' => 'Your password must be atleast 6 character long.'));
							exit;
						}

						$customer = Mage::getModel('customer/customer');
						$customer->setWebsiteId($websiteId)->setStore($store)
						->setFirstname($firstname)
						->setLastname($lastname)
						->setEmail($email)
						->setPassword($password);
						$customer->save();
						// Set customer, for example guest
						/*$customerAsGuest = array(
						    "firstname" => $firstname,
						    "lastname" => $lastname,
						    "email" => $email,
						    "website_id" => $websiteId,
						    "store_id" => $store->getStoreId(),
						    "mode" => "guest"
						);


					$quoteObj = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);
					$quoteObj->setCustomerEmail($email);
					$quoteObj->setCustomer_is_guest(1);
					$quoteObj->save();*/


					//$quoteObj = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);


					 /*$customer = Mage::getModel("customer/customer")
					 ->getCollection()
					 ->addAttributeToFilter("email",$email)
					 ->getFirstItem();*/


					 // print_r($customer->getData());
					 // echo $customer->getData()['entity_id'];
					 // exit;


					 /*$customer_model_guest1 = Mage::getModel('sales/quote')->load($quote_id);*/
         			//print_r($customer_model_guest1->getData());
         			//exit;

					 /*$customer_model_guest1->setCustomerId($customer->getData()['entity_id']);*/

						/*$customer_model_guest = new Mage_Checkout_Model_Cart_Customer_Api();

						$customer_assign_guest = $customer_model_guest->set($quote_id,$customerAsGuest);*/
					}else{
						echo json_encode(array('status' => 0, 'message' => 'Password and Confirm Password doesnot match'));
						die;
					}
				}else{
					echo json_encode(array('status' => 0, 'message' => 'Email Address is already used'));
					die;
				}
			}

			if (isset($_REQUEST['email'])) {
				$email = $_REQUEST['email'];
			}else{
				$email = $customer->getEmail();
			}

			if($billingAddressId){
				try{
					$address = Mage::getModel('customer/address')->load($billingAddressId);
					$addressData['firstname'] = $address->getFirstname();
					$addressData['lastname'] = $address->getLastname();
					$addressData['email'] = $address->getEmail();
					//$addressData['street'] = $address->getStreet();
					$address_data_street_eb = $address->getStreet();
					$addressData['street'] = $address_data_street_eb[0]." ".$address_data_street_eb[1];
					$addressData['city'] = $address->getCity();
					$addressData['postcode'] = $address->getPostcode();
					$addressData['telephone'] = $address->getTelephone();
					$addressData['country_id'] = $address->getCountryId();
					$addressData['region_id'] = $address->getRegionId();
					$addressData['fax'] = $address->getFax();
					if(!empty($_REQUEST['firstName'])){
						$addressData['company'] = $_REQUEST['firstName'];
					}else{
						$addressData['company'] = $address->getCompany();
					}
					
					$quote_billing = Mage::getModel('sales/quote_address')->load($billingAddressId);
					$customerAddress = Mage::getModel('customer/address')->load($billingAddressId);;


					// if(!$customerAddress->getGstin() && !empty($_REQUEST['gstin']) && strlen($_REQUEST['gstin']) == 15){
					// 	$addressData['gstin'] = $_REQUEST['gstin'];
					// 	$customerAddress->setGstin($_REQUEST['gstin']);
					// }
			  //   	if(!$customerAddress->getCompany() && !empty($_REQUEST['firstName'])){
			  //   		$customerAddress->setCompany($_REQUEST['firstName']);
			  //   	}
					if(!empty($_REQUEST['gstin'])){

						$gstVal = preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9]{1}[A-Z]{1}[0-9A-Z]{1}$/", $_REQUEST['gstin']);
						if(!$gstVal){
							echo json_encode(array('status' => 0, 'message' => 'GST Identification Number is not valid. It should be in this 11AAAAA1111Z1A1 format.'));
							exit;
						}

						if(!$customer->getGstin()){
							$customer->setGstin($_REQUEST['gstin']); 
							$customer->save();
						}
						$addressData['gstin'] = $_REQUEST['gstin'];
						$customerAddress->setGstin($_REQUEST['gstin']);
					}

						

			    	
			    	if(!$customerAddress->getCompany() && !empty($_REQUEST['firstName'])){
			    		$customerAddress->setCompany($_REQUEST['firstName']);
			    	}
		    		
		    		$customerAddress->save();
				}catch(Exception $e){
					echo json_encode(array('status' => 0, 'message' => 'Customer address doesnt exists'.$e->getMessage()));
					die;
				}
			}else{
				$required = array("firstName","street","postcode","telephone","country_id","city","region_id","mobile");
				//$required = array("firstName","lastname","street","postcode","telephone","country_id","city","region_id","mobile");
				$required_err['error'] = array();
				foreach ($required as $requiredField) {
					if (!isset($_REQUEST[$requiredField]) || $_REQUEST[$requiredField] == "") {
						$required_err['error'][] = $requiredField . " is required.";
					}
				}

				if(empty($required_err['error'])){
					$addressData['firstname'] = $firstname;
					$addressData['lastname'] = $lastname;
					$addressData['email'] = $email;
					$addressData['street'] = $street;  
					$addressData['city'] = $_REQUEST['city'];
					$addressData['postcode'] = $postcode;
					$addressData['telephone'] = $_REQUEST['telephone'];
					$addressData['company'] = $_REQUEST['firstName'];
					$addressData['gstin'] = $_REQUEST['gstin'];
					if(strtolower($_REQUEST['country_id']) == "india" || $_REQUEST['country_id'] == "IN" ) {
						$addressData['country_id'] = "IN";
					} else{
						echo json_encode(array('status' => 0, 'message' => 'Invalid Country Name'));
						die;
					}
					$addressData['region_id'] = $_REQUEST['region_id'];
					$addressData['fax'] = $_REQUEST['mobile'];
				}else{
					echo json_encode(array('status' => 0, 'message' => $required_err['error'][0]));
					die;
				}
			}

			$customer_id_eb_check = $customer->getId();
			// $gstin = '';
			if(empty($customer_id_eb_check)){
				if(strtolower($_REQUEST['country_id']) == "india" || $_REQUEST['country_id'] == "IN"  || empty($_REQUEST['country_id'])) {
					$country_id_add_eb = "IN";
				} else{
					echo json_encode(array('status' => 0, 'message' => 'Invalid Country Name'));
					die;
				}
				// guest user address save
				if($billing_use_for_shipping_yes){
					$arrAddresses = array(
						array(
							"mode" => "shipping",
							"email" => $email,
							"firstname" => $firstname,
							"lastname" => $lastname,
							"street" => $street,
							"city" => $_REQUEST['city'],
							"region_id" => $_REQUEST['region_id'],
							"postcode" => $postcode,
							"country_id" => $country_id_add_eb,
							"telephone" => $_REQUEST['telephone'],
							"company" => $_REQUEST['firstName'],							
							"fax" => $_REQUEST['mobile'],
							"mobile" => $_REQUEST['mobile'],
							"gstin" => $gstin,
							"is_default_shipping" => 0,
							"is_default_billing" => 0
							),
						array(
							"mode" => "billing",
							"email" => $email,
							"firstname" => $firstname,
							"lastname" => $lastname,
							"street" => $street,
							"city" => $_REQUEST['city'],
							"region_id" => $_REQUEST['region_id'],
							"postcode" => $postcode,
							"country_id" => $country_id_add_eb,
							"telephone" => $_REQUEST['telephone'],
							"company" => $_REQUEST['firstName'],	
							"fax" => $_REQUEST['mobile'],
							"mobile" => $_REQUEST['mobile'],
							"gstin" => $gstin,
							"is_default_shipping" => 0,
							"is_default_billing" => 0
							)
						);
					$customer_model_guest = new Mage_Checkout_Model_Cart_Customer_Api();
					$customer_assign_guest = $customer_model_guest->setAddresses($quote_id,$arrAddresses);
					$cart_shipping = new Mage_Checkout_Model_Cart_Shipping_Api();
					$cart_shipping->setShippingMethod($quote_id,$shippingMethod);



				} else {
					$arrAddresses = array(
						array(
							"mode" => "billing",
							"email" => $email,
							"firstname" => $firstname,
							"lastname" => $lastname,
							"street" => $street,
							"city" => $_REQUEST['city'],
							"region_id" => $_REQUEST['region_id'],
							"postcode" => $postcode,
							"country_id" => $country_id_add_eb,
							"telephone" => $_REQUEST['telephone'],
							"fax" => $_REQUEST['mobile'],
							"mobile" => $_REQUEST['mobile'],
							"gstin" => $gstin,
							"company" => $_REQUEST['firstName'],	
							"is_default_shipping" => 0,
							"is_default_billing" => 0
							)
						);
					$customer_model_guest = new Mage_Checkout_Model_Cart_Customer_Api();
					$customer_assign_guest = $customer_model_guest->setAddresses($quote_id,$arrAddresses);
					$cart_shipping = new Mage_Checkout_Model_Cart_Shipping_Api();
					$cart_shipping->setShippingMethod($quote_id,$shippingMethod);
				}
				echo json_encode(array('status' => 1, 'message' => 'Billing address has been saved successfully'));
			} else {
				// loggedin user address save
				try{
					$quoteObj = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);
					$quoteObj->assignCustomer($customer);
					$quoteObj->setSendCconfirmation(1);
					if(!empty($customer_cform)){
						$quoteObj->setCustomerCform($customer_cform);
					}

					if($billing_use_for_shipping_yes){
						if(empty($billingAddressId)){
							$customAddress = Mage::getModel('customer/address');
									//$customAddress = new Mage_Customer_Model_Address();
							$customAddress->setData($addressData)
							->setCustomerId($customer->getId())
							->setIsDefaultBilling('0')
							->setIsDefaultShipping('0')
							->setSaveInAddressBook('1');
							$customAddress->save();
						}
						$billingAddress = $quoteObj->getBillingAddress()->addData($addressData);
						$shippingAddress = $quoteObj->getShippingAddress()->addData($addressData);
						$shippingAddress->setCollectShippingRates(true)->collectShippingRates();
						$quoteObj->collectTotals();
						$quoteObj->save();

						$cart_shipping = new Mage_Checkout_Model_Cart_Shipping_Api();
						$cart_shipping->setShippingMethod($quote_id,$shippingMethod);
					}else{
						if(empty($billingAddressId)){
							$customAddress = Mage::getModel('customer/address');
									//$customAddress = new Mage_Customer_Model_Address();
							$customAddress->setData($addressData)
							->setCustomerId($customer->getId())
							->setIsDefaultBilling('0')
							->setIsDefaultShipping('0')
							->setSaveInAddressBook('1');
							$customAddress->save();
						}
						$billingAddress = $quoteObj->getBillingAddress()->addData($addressData);
						$quoteObj->collectTotals();
						$quoteObj->save();
					}
					echo json_encode(array('status' => 1, 'message' => 'Billing address has been saved successfully'));
				}catch(Exception $e){
					echo json_encode(array('status' => 0, 'message' => 'Billing address has not saved'.$e->getMessage()));
					die;
				}
			}

		}

		/**
	   	 * @desc : save shipping address (only if billing_use_for_shipping_yes is not set) in qoute.
	     * @package     Neo_Mobileapi
	     * @author      pradeep sanku
	     */
		public function saveShippingAddressAction(){

			$quote_id = $_REQUEST['quote_id'];
			$customer_id = $_REQUEST['customer_id'];
			$shippingAddressId = $_REQUEST['shippingAddressId'];
			// $firstname = $_REQUEST['firstName'];
			// $lastname = $_REQUEST['lastname'];
			// $street = $_REQUEST['street'];
			// $city = $_REQUEST['city'];
			// $postcode = $_REQUEST['postcode'];
			// $countryId = $_REQUEST['countryId'];
			// $region_id = $_REQUEST['region_id'];
			// $mobile= $_REQUEST['mobile'];
			$firstname = $_REQUEST['firstName'];
			$lastname = '.';
			$email = $_REQUEST['email'];
			//$street = $_REQUEST['street'];
			$postcode = $_REQUEST['postcode'];

			$street1 = $_REQUEST['street'];
			$street2 = $_REQUEST['street1'];
			$company = $_REQUEST['firstName'];

			$street[0] = $street1;
			$street[1] = $street2; 

			$street = $_REQUEST['street'];

			$addressData = array();

			$shippingMethod = "freeshipping_freeshipping";

			$websiteId = Mage::app()->getWebsite()->getId();
			$store = Mage::app()->getStore();
			$gstin = '';
			if(!empty($_REQUEST['gstin'])){
			$gstVal = preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9]{1}[A-Z]{1}[0-9A-Z]{1}$/", $_REQUEST['gstin']);

			if(!$gstVal || empty($_REQUEST['gstin'])){
				echo json_encode(array('status' => 0, 'message' => 'Please enter valid GST IN'));
				exit;
			}

			// if(!empty($_REQUEST['gstin']) && $gstVal){
			
				$gstin = $_REQUEST['gstin'];
			}

			if($shippingAddressId){
				try{
					$address = Mage::getModel('customer/address')->load($shippingAddressId);
					$addressData['firstname'] = $address->getFirstname();
					$addressData['lastname'] = $address->getLastname();
					$addressData['email'] = $address->getEmail();
					//$addressData['street'] = $address->getStreet();
					$address_data_street_eb = $address->getStreet();
					$addressData['street'] = $address_data_street_eb[0]." ".$address_data_street_eb[1];
					//Mage::log($addressData['street'],null,'app.log');
					$addressData['city'] = $address->getCity();
					$addressData['postcode'] = $address->getPostcode();
					$addressData['telephone'] = $address->getTelephone();
					$addressData['country_id'] = $address->getCountryId();
					$addressData['region_id'] = $address->getRegionId();
					$addressData['fax'] = $address->getFax();
					$addressData['mobile'] = $address->getMobile();
					$addressData['company'] = $address->getCompany();
					$addressData['gstin'] = $address->getGstin();
				}catch(Exception $e){
					echo json_encode(array('status' => 0, 'message' => 'Customer address doesnt exists'.$e->getMessage()));
					die;
				}
			}else{
				$required = array("firstName","street","postcode","telephone","country_id","city","region_id","mobile");
				//$required = array("firstName","lastname","street","postcode","telephone","country_id","city","region_id","mobile");
				$required_err['error'] = array();
				foreach ($required as $requiredField) {
					if (!isset($_REQUEST[$requiredField]) || $_REQUEST[$requiredField] == "") {
						$required_err['error'][] = $requiredField . " is required.";
					}
				}

				if(empty($required_err['error'])){
					$addressData['firstname'] = $_REQUEST['firstName'];
					$addressData['lastname'] = '.';
					$addressData['email'] = $_REQUEST['email'];
					$addressData['street'] = $_REQUEST['street'];  
					$addressData['city'] = $_REQUEST['city'];
					$addressData['postcode'] = $_REQUEST['postcode'];
					$addressData['telephone'] = $_REQUEST['telephone'];
					$addressData['company'] = $_REQUEST['firstName'];
					$addressData['gstin'] = $_REQUEST['gstin'];
					if(strtolower($_REQUEST['country_id']) == "india" || $_REQUEST['country_id'] == "IN" ) {
						$addressData['country_id'] = "IN";
					} else{
						echo json_encode(array('status' => 0, 'message' => 'Invalid Country Name'));
						die;
					}
					$addressData['region_id'] = $_REQUEST['region_id'];
					$addressData['fax'] = $_REQUEST['mobile'];
					$addressData['mobile'] = $_REQUEST['mobile'];
					$addressData['gstin'] = $_REQUEST['gstin'];
				}else{
					echo json_encode(array('status' => 0, 'message' => $required_err['error'][0]));
					die;
				}
			}

			$customer_id_eb_check = $customer_id;

			if(empty($customer_id_eb_check)){
				if(strtolower($_REQUEST['country_id']) == "india" || $_REQUEST['country_id'] == "IN" || empty($_REQUEST['country_id']) ) {
					$country_id_add_eb = "IN";
				} else{
					echo json_encode(array('status' => 0, 'message' => 'Invalid Country Name'." ".$_REQUEST['country_id']."--".$shippingAddressId));
					die;
				}

				// guest user address save
				$arrAddresses = array(
					array(
						"mode" => "shipping",
						"email" => $email,
						"firstname" => $firstname,
						"lastname" => $lastname,
						"street" => $street,
						"city" => $_REQUEST['city'],
						"region_id" => $_REQUEST['region_id'],
						"postcode" => $postcode,
						"country_id" => $country_id_add_eb,
						"telephone" => $_REQUEST['telephone'],
						"fax" => $_REQUEST['mobile'],
						"mobile" => $_REQUEST['mobile'],
						"company" => $_REQUEST['firstName'],
						"gstin" => $gstin,
						"is_default_shipping" => 0,
						"is_default_billing" => 0
						)
					);
				$customer_model_guest = new Mage_Checkout_Model_Cart_Customer_Api();
				$customer_assign_guest = $customer_model_guest->setAddresses($quote_id,$arrAddresses);
				$cart_shipping = new Mage_Checkout_Model_Cart_Shipping_Api();
				$cart_shipping->setShippingMethod($quote_id,$shippingMethod);

				echo json_encode(array('status' => 1, 'message' => 'Shipping address has been saved successfully'));
			} else {
				if(strtolower($_REQUEST['country_id']) == "india" || $_REQUEST['country_id'] == "IN" || empty($_REQUEST['country_id'])) {
					$country_id_add_eb = "IN";
				} else{
					echo json_encode(array('status' => 0, 'message' => 'Invalid Country Name'." ".$_REQUEST['country_id']."--".$shippingAddressId));
					die;
				}
				// loggedin user address save
				try{
					if(empty($shippingAddressId)) {
						$customAddress = Mage::getModel('customer/address');
						//$customAddress = new Mage_Customer_Model_Address();
						$customAddress->setData($addressData)
						->setCustomerId($customer_id)
						->setIsDefaultBilling('0')
						->setIsDefaultShipping('0')
						->setSaveInAddressBook('1');
						$customAddress->save();
					}
					$quoteObj = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);
					$shippingAddress = $quoteObj->getShippingAddress()->addData($addressData);
					$shippingAddress->setCollectShippingRates(true)->collectShippingRates();
					$quoteObj->collectTotals();
					$quoteObj->save();

					$cart_shipping = new Mage_Checkout_Model_Cart_Shipping_Api();
					$cart_shipping->setShippingMethod($quote_id,$shippingMethod);

					echo json_encode(array('status' => 1, 'message' => 'Shipping address has been saved successfully'));
				}catch(Exception $e){
					echo json_encode(array('status' => 0, 'message' => 'Shipping address has not saved'.$e->getMessage()));
					die;
				}
			}// end of else loop logged in user
		}

		/**
	   	 * @desc : get Available list of all payment methods
	     * @package     Neo_Mobileapi
	     * @author      pradeep sanku
	     */
		/*public function getAvailablePaymentMethodsAction(){
			$quote_id = $_REQUEST['quote_id'];
			$result = $result_array = array();
			try{
				$payment_api = new Mage_Checkout_Model_Cart_Payment_Api();
				$payment_method = $payment_api->getPaymentMethodsList($quote_id);

				$storeId = Mage::app()->getStore()->getStoreId();
	            $cart_product = new Mage_Checkout_Model_Cart_Api();
	            $cart_product_info = $cart_product->info( $quote_id, $storeId);
	            $cart_product_info_blank_check = array_filter($cart_product_info);

				// added by pradeep sanku
	      // adding price summary
				$cart_summary = array();
		      	$cart_summary["subtotal"] = $cart_product_info["subtotal"];
		      	$cart_summary["grand_total"] = $cart_product_info["grand_total"];
		      	$cart_summary["tax_amount"] = $cart_product_info["cod_tax_amount"];
		      	//if((int)$cart_product_info["cod_fee"]){
		      		$cart_summary["advance_amount"] = $cart_product_info["cod_fee"];
		      	//}
				if($cart_summary["grand_total"] > 50000) {
					$cart_summary["available_amount"] = $cart_summary["grand_total"] - 50000;
				} else {
					$cart_summary["available_amount"] = 0;
				}
		      	$result_array['status'] = '1';
		      	$result_array["payment"] = $payment_method;
				$result_array["summary"] = $cart_summary;
				//$result['data'] = $payment_method;
				$this->getResponse()->setHeader('Content-type', 'application/json', true);
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result_array));
			}catch(Exception $e){
				echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
				die;
			}
		}*/
		public function getTextBetweenTags($tag, $html, $strict=0)
		{
			/*** a new dom object ***/
			$dom = new domDocument;

			/*** load the html into the object ***/
			if($strict==1)
			{
				$dom->loadXML($html);
			}
			else
			{
				$dom->loadHTML($html);
			}

			/*** discard white space ***/
			$dom->preserveWhiteSpace = false;

			/*** the tag by its tag name ***/
			$content = $dom->getElementsByTagname($tag);

			/*** the array to return ***/
			$out = array();
			foreach ($content as $item)
			{
				/*** add node value to the out array ***/
				$temp =array();
				$temp['label'] = $item->nodeValue;
				$temp['value'] = $item->getAttribute('value'); 
				$out[] = $temp;
			}
			/*** return the results ***/
			return $out;
		}

		public function getAvailablePaymentMethodsAction(){
			$quote_id = $_REQUEST['quote_id'];
			$result = $result_array = array();
			try{
				$instructionValue = Mage::getStoreConfig('payment/banktransfer/instructions');
				$checkbanktransferpayment = Mage::getStoreConfig('payment/banktransfer/active');
		    //$digit_only = array(filter_var($instructionValue, FILTER_SANITIZE_NUMBER_INT));
				$option_labels = $this->getTextBetweenTags("option", $instructionValue);
				// Mage::log('label values: '.print_r($label_array, 1), null, 'instructions.log', true);
				$disable_cod = false;
				$disallow_sku_cod = false;
				if($quote_id){
					$_quote = Mage::getModel('sales/quote')->load($quote_id);
					// $items = $_quote->getAllVisibleItems();
					// foreach ($items as $item) {
					// 	if($item->getProduct()->getSku() == 'Mobile1027'){
					// 		$disallow_sku_cod = true;
					// 	}
					// }
					// var_dump($disallow_sku_cod); exit;
					// if($quote_id = 36793){

					$user_id = $_quote->getCustomerId();
					//}
					$grand_total = $_quote->getGrandTotal();
					$cod_maxlimit = 200000;//Mage::getStoreConfig('payment/cashondelivery/cod_maxlimit');
					if($grand_total > $cod_maxlimit){
						$disable_cod = true;
					}
				}


				$payment_api = new Mage_Checkout_Model_Cart_Payment_Api();
				$payment_method = $payment_api->getPaymentMethodsList($quote_id);

				if($disable_cod){
					// $cashondelivery_exists = array_search('cashondelivery', $payment_method);
					// var_dump($cashondelivery_exists);

					foreach ($payment_method as $key => $value) {
						if($value['code'] == 'cashondelivery'){
							$unset_key = $key;
						}
					}
					if($unset_key){
						unset($payment_method[$unset_key]);
						// array_splice($payment_method, $unset_key, 1);
					}

					$payment_method = array_values($payment_method); 
				}
		    // code, titles, cc_type, values(value(7),label(Credit days After 7))
		    // get instructions for banktranfer payment
				$updated_payment_list = array();
				$check_banktransfer_exist = array_search('banktransfer', $payment_method);
				
		    if(empty($check_banktransfer_exist) && $checkbanktransferpayment == 1){
		    	
		    	//Get Credit limit of customer from Navision
		      	//$credit = Mage::getModel('customapiv6/customer')->getCreditLimit($user_id);
		      	$grand_total = (int) $_quote->getGrandTotal();
		      	$credit = Mage::getModel('ebautomation/customer')->getCreditLimit($user_id,$grand_total);  
				
		      	//echo $cart_summary["grand_total"];
		      	
		      	// $message = "Credit Days Payment: This payment method won't be applicable for this transaction.";
		      	/*if($credit['total_credit'] == 1 || empty($credit['total_credit']) || $credit == NULL){
		      		$banktransfer = FALSE;
		      	}
		      	else if( $credit['total_credit'] == 0){
		      		$banktransfer = TRUE;
		      	}
		      	else if( $grand_total > $credit['balance_credit'] ){
		      		$banktransfer = FALSE;
		      	}
		      	else if($credit['overdue_credit'] > 0){
		      		$banktransfer = FALSE;
		      	}*/

		        $banktransfer = FALSE;
		      	if($credit['allow'] == 1){
		          	$banktransfer = TRUE;
		         }

		        $message = '';
		      	if($banktransfer == FALSE){
		      		if($credit['balance_credit'] == 1){
			   			
			   			$message = "Credit Days Payment: This payment method is not applicable for this transaction. ".$credit['message'];
			   		}else{
			   			$message = "Credit Days Payment: This payment method is not applicable for this transaction. ";
			   		}
		      		
		      	}

				//if(empty($check_banktransfer_exist)){
					$updated_payment_list = $payment_method;
					$temp = array();
					$temp["code"] = "banktransfer";
					$temp["title"] = "Credit Days Payment";
					$temp["credit_limit"] = $message;
					$temp["cc_types"] = null;
					$temp["op_values"] = null;
		     //get instructions and push it to array
					foreach ($option_labels as $key => $value) {
						$temp1 = array();
						$temp1["value"] = $value["value"];
						$temp1["label"] = $value["label"];
						$temp["op_values"][] =$temp1;
					}
					$updated_payment_list[]=$temp;
				} else {
					
					//payment set other than credit limit
					//Get Credit limit of customer from Navision
					$grand_total = (int) $_quote->getGrandTotal();
		      		$credit = Mage::getModel('ebautomation/customer')->getCreditLimit($user_id,$grand_total); 
					$message = '';
			      	
			        $banktransfer = FALSE;
			      	if($credit['allow'] == 1){
			          	$banktransfer = TRUE;
			        }

			      	if($banktransfer == FALSE){
			      		if($credit['balance_credit'] == 1){
				   			$message = "Credit Days Payment: This payment method is not applicable for this transaction. ".$credit['message'];
				   		}else{
				   			$message = "Credit Days Payment: This payment method is not applicable for this transaction. ";
				   		}
			      	}
					foreach ($payment_method as $key => $value) {
						if($value["code"] == "banktransfer"){
							$temp = array();
							$temp["code"] = "banktransfer";
							$temp["title"] = "Credit Days Payment";
							$temp["credit_limit"] = $message;
							$temp["cc_types"] = null;
							$temp["op_values"] = null;
		       				//get instructions and push it to array
							foreach ($option_labels as $key => $value) {
								$temp1 = array();
								$temp1["value"] = $value["value"];
								$temp1["label"] = $value["label"];
								$temp["op_values"][] =$temp1;
							}
							$updated_payment_list[]=$temp;
						} else {
							$updated_payment_list[] = $payment_method[$key];
						}
					}
				}

				$paytm = Mage::getStoreConfig('payment/paytm_cc/active');

				if($paytm == 1){
					$temp2["cc_types"] = NULL;
					$temp2["code"] = 'paytm_cc';
					$temp2["title"] = 'Paytm';
				}

				$updated_payment_list[] = $temp2;

				//code added by jp for order replacement added in payment list.
				$replace = Mage::getStoreConfig('payment/replace/active');

				if($replace == 1)
				{
					$jp["code"] = 'replace';
					$jp["title"] = 'Order Replacement';
					$jp["cc_types"] = null;
				}

				$updated_payment_list[] = $jp;				

				// if($quote_id == 145939){
				// 	echo "<pre>";
				// 	print_r($updated_payment_list);
		  //   		exit;
		  //   	}

				$storeId = Mage::app()->getStore()->getStoreId();
				$cart_product = new Mage_Checkout_Model_Cart_Api();
				$cart_product_info = $cart_product->info( $quote_id, $storeId);
				$cart_product_info_blank_check = array_filter($cart_product_info);

		    // added by pradeep sanku
		       // adding price summary
				$cart_summary = array();
				$cart_summary["subtotal"] = $cart_product_info["subtotal"];
				$cart_summary["grand_total"] = $cart_product_info["grand_total"];
				$cart_summary["tax_amount"] = $cart_product_info["cod_tax_amount"];
		         //if((int)$cart_product_info["cod_fee"]){
				$cart_summary["advance_amount"] = $cart_product_info["cod_fee"];
		         //}
				if($cart_summary["grand_total"] > 50000) {
					$cart_summary["available_amount"] = $cart_summary["grand_total"] - 50000;
				} else {
					$cart_summary["available_amount"] = 0;
				}
				$result_array['status'] = '1';
				$result_array['feedback_status'] = '1';
				$result_array["payment"] = $updated_payment_list;
				$result_array["summary"] = $cart_summary;
		    //$result['data'] = $payment_method;
				$this->getResponse()->setHeader('Content-type', 'application/json', true);
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result_array));
			}catch(Exception $e){
				echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
				die;
			}
		}

		/**
	   	 * @desc : set SelectedPaymentMethod in the quote object
	     * @package     Neo_Mobileapi
	     * @author      pradeep sanku
	     */
		public function setSelectedPaymentMethodsAction(){

			//Mage::log($_REQUEST,null,'setSelectedPaymentMethodsAction.log');     
            $pastOrderId = ''; // line added by jp.
			$quote_id = $_REQUEST['quote_id'];
			$paymentData = array();
			$paymentMathod = $paymentData['method'] = $_REQUEST['payment_method'];
			$pastOrderId = $_REQUEST['past_order']; // line added by jp.
			try{
				// creating customer session
				Mage::getSingleton('customer/session')->loginById($_REQUEST['userid']);

                //code for check replace order by jp.
                if($_REQUEST['payment_method'] == 'replace' && $_REQUEST['userid'] != '')
                {	
                   $customer = Mage::getModel('customer/customer')->load($_REQUEST['userid']);
                   if($customer->getId() != '' && $pastOrderId != '')
                   {				
					$orderCollection = Mage::getResourceModel('sales/order_collection')
					->addFieldToSelect('increment_id') 
					->addFieldToFilter('customer_id', $customer->getId())->getData();
					if(!empty($orderCollection) && count($orderCollection) > 0)
					{	$orderIdList = array();				
						foreach($orderCollection as $key=>$value)
						{  //print_r($value); exit;
                           $orderIdList[] = $value['increment_id'];
						}
						if(!in_array(trim($pastOrderId),$orderIdList)){										 
						 echo json_encode(array('status' => 0, 'message' => 'Order No provided does not exist or does not belongs to you.'));exit;					
						}
				    }
				   }
                }

				$cart_payment = new Mage_Checkout_Model_Cart_Payment_Api();
				$result = $cart_payment->setPaymentMethod($quote_id,$paymentData);
				if($result){
					$storeId = Mage::app()->getStore()->getStoreId();
					$cart_product = new Mage_Checkout_Model_Cart_Api();
					$cart_product_info = $cart_product->info( $quote_id, $storeId);
					// if($quote_id == 176429){
		   //          echo "<pre>"; print_r($cart_product_info); exit;
					// }
					$quote = Mage::getModel('sales/quote')->load($quote_id);
					$cart_product_info_blank_check = array_filter($cart_product_info);
		            //removing unnecessary data
		            //creating the items array
					$items_created_result_array = array();
					$items_array = $cart_product_info["items"];
		            //echo "<pre>"; print_r($items_array); exit;
		             $total_tax = 0;
		             $discountTotal = 0;
					foreach ($items_array as $key => $value) {
						$product = Mage::getModel('catalog/product')->load($value["product_id"]);
						$items_created_array["product_id"] = $value["product_id"];
						$items_created_array["name"] = $value["name"];
						$items_created_array["price"] = number_format($product->getData('price'),2,".","");
						$items_created_array["qty"] = $value["qty"];

						$items_created_array["thumbnail"] = (string)Mage::helper('catalog/image')->init($product,'image');
						$items_created_result_array["items"][] = $items_created_array;
						$total_tax += $value['tax_amount'];
						$discountTotal += $value['discount_amount'];
		              //$product->getThumbnail();
					}
					
					$items_created_result_array["payment"] = $cart_product_info["payment"];
		            // added by deepakd just in case to avoid code break within bill desk api
					$items_created_result_array["payment"]["additional_information"] = array(""=>"");

		            // added by pradeep sanku
		            // adding price summary
					$cart_summary = array();
					$cart_summary["subtotal"] = $cart_product_info["subtotal"];
					$cart_summary["grand_total"] = $cart_product_info["grand_total"];
					// $cart_summary["tax_amount"] = $cart_product_info["cod_tax_amount"];
					$cart_summary["tax_amount"] = $total_tax;
			      	//if((int)$cart_product_info["cod_fee"]){
					$cart_summary["advance_amount"] = $cart_product_info["cod_fee"];
			      	//}
					if($cart_summary["grand_total"] > 50000) {
						$cart_summary["available_amount"] = $cart_summary["grand_total"] - 50000;
					} else {
						$cart_summary["available_amount"] = 0;
					}
					$cart_summary['subtotal_label'] = Mage::helper('sales')->__('Subtotal:');
			      	// $cart_summary['grandtotal_label'] = Mage::helper('sales')->__('Grand Total:');
			      	$payment_not_array = array('cashondelivery','replace','banktransfer');
			      	if(!in_array($paymentData['method'], $payment_not_array)){
			      		$cart_summary['grandtotal_label'] = Mage::helper('sales')->__('Grand Total(With 1% Prepaid Payment discount):');
			      	}else{
			      		$cart_summary['grandtotal_label'] = Mage::helper('sales')->__('Grand Total:');
			      	}
			      	
			      	$cart_summary['tax_label'] = Mage::helper('sales')->__('GST:');
			      	//applied rules
					$applied_rules = $quote->getAppliedRuleIds();
					if(!empty($applied_rules)){
						$appliedRuleIds = explode(",",$applied_rules);
						$rules =  Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('rule_id' , array('in' => $appliedRuleIds));
						$discount_names = '';
						foreach ($rules as $rule) {
						    //do something with $rule
						    // print_r($rule);
						    // echo $rule->getName();
						    $discount_names .= $rule->getName().'+';
						}

						$discount_names = trim($discount_names,'+'); 
						$cart_summary['discount_amount'] = (int) $discountTotal;
						if($discount_names  !== ''){
							$cart_summary['discount_label'] = 'DISCOUNT('.$discount_names.')';
						}else{
							$cart_summary['discount_label'] = 'DISCOUNT';
						}
					}
					$items_created_result_array["summary"] = $cart_summary;
						
					//To get approx delivery of product
					$quoteShippingAddress = Mage::getModel('sales/quote_address')->getCollection()
									->addFieldToSelect('postcode')
									->addFieldToFilter('address_type','shipping')
									->addFieldToFilter('quote_id',$_REQUEST['quote_id'])->getData();
					$pincode = $quoteShippingAddress[0]['postcode'];
					$response = Mage::getModel('operations/serviceablepincodes')->getProductDeliveryOnDetailsPage($pincode,$paymentMathod);
					$items_created_result_array["delivery_status"] = $response['status'];
					$items_created_result_array["approx_delivery"] = $response['message'];
					

					echo json_encode(array('status' => 1, 'message' => 'Payment Method has been successfully added to quote', 'data' => $items_created_result_array));
					die;
				}else{
					echo json_encode(array('status' => 0, 'message' => 'Payment Method has not been successfully added to quote'));
					die;
				}
			}catch(Exception $e){
				echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
				die;
			}
		}

		/**
	   	 * @desc : add coupon code in the quote object
	     * @package     Neo_Mobileapi
	     * @author      pradeep sanku
	     */
		public function addCouponCodeAction()
		{
			$quote_id = $_REQUEST['quote_id'];
			$couponCode = $_REQUEST['couponCode'];
			try {
				$coupon_api = new Mage_Checkout_Model_Cart_Coupon_Api();

				$coupon_added_flag = $coupon_api->add($quote_id,$couponCode);
				$quote = Mage::getModel('sales/quote')->load($quote_id);
				$storeId = Mage::app()->getStore()->getStoreId();
				$cart_product = new Mage_Checkout_Model_Cart_Api();
				$cart_product_info = $cart_product->info( $quote_id, $storeId);

				$cart_product_info_blank_check = array_filter($cart_product_info);
        		//removing unnecessary data, creating the items array
				$items_created_result_array = array();
				$items_array = $cart_product_info["items"];
				$tax_amount = array();

				foreach ($items_array as $key => $value) {
					$product = Mage::getModel('catalog/product')->load($value["product_id"]);
					$items_created_array["product_id"] = $value["product_id"];
					$items_created_array["name"] = $value["name"];
					$items_created_array["price"] = number_format($product->getData('price'),2,".","");
					$items_created_array["qty"] = $value["qty"];
					array_push($tax_amount,$value['tax_amount']);
					$items_created_array["thumbnail"] = (string)Mage::helper('catalog/image')->init($product,'image');
          			//$items_created_result_array["items"][] = $items_created_array;
				}
				$items_created_result_array["payment"] = $cart_product_info["payment"];

        		// adding price summary
				$cart_summary = array();
				$cart_summary["subtotal"] = (double)$quote->getSubtotal();
				$cart_summary["grand_total"] = (double)$quote->getGrandTotal();
				$cart_summary["tax_amount"] = array_sum($tax_amount);
  				//if((int)$cart_product_info["cod_fee"]){
				$cart_summary["advance_amount"] = $cart_product_info["cod_fee"];
  				//}
				if($cart_summary["grand_total"] > 50000) {
					$cart_summary["available_amount"] = $cart_summary["grand_total"] - 50000;
				} else {
					$cart_summary["available_amount"] = 0;
				}

				$cart_summary['subtotal_label'] = Mage::helper('sales')->__('Subtotal:');
		      	$cart_summary['grandtotal_label'] = Mage::helper('sales')->__('Grand Total:');
		      	$cart_summary['tax_label'] = Mage::helper('sales')->__('GST:');

				$items_created_result_array["summary"] = $cart_summary;
					

				if($coupon_added_flag){
					echo json_encode(array('status' => 1, 'message' => 'Coupon applyed to cart successfully.', 'data' => $items_created_result_array));
					die;
				} else {
					echo json_encode(array('status' => 0, 'message' => 'Coupon was not applyed to cart'));
					die;
				}
			}catch(Exception $e){
				echo json_encode(array('status' => 0, 'message' => 'Coupon was not applyed to cart '.$e->getMessage()));
				die;
			}
		}

	/**
 	 * @desc : remove coupon code applied to cart
   * @package     Neo_Mobileapi
   * @author      deepak deshmukh
   */
	public function removeCouponCodeAction()
	{
		$quote_id = $_REQUEST['quote_id'];
		$storeId = Mage::app()->getStore()->getStoreId();
		try {
			$coupon_api = new Mage_Checkout_Model_Cart_Coupon_Api();
			$coupon_removed_flag = $coupon_api->remove($quote_id,$storeId);
			$quote = Mage::getModel('sales/quote')->load($quote_id);

			$cart_product = new Mage_Checkout_Model_Cart_Api();
			$cart_product_info = $cart_product->info( $quote_id, $storeId);

			$cart_product_info_blank_check = array_filter($cart_product_info);
        //removing unnecessary data, creating the items array
			$items_created_result_array = array();
			$items_array = $cart_product_info["items"];
			$tax_amount = array();

			foreach ($items_array as $key => $value) {
				$product = Mage::getModel('catalog/product')->load($value["product_id"]);
				$items_created_array["product_id"] = $value["product_id"];
				$items_created_array["name"] = $value["name"];
				$items_created_array["price"] = number_format($product->getData('price'),2,".","");
				$items_created_array["qty"] = $value["qty"];
				array_push($tax_amount,$value['tax_amount']);
				$items_created_array["thumbnail"] = (string)Mage::helper('catalog/image')->init($product,'image');
          //$items_created_result_array["items"][] = $items_created_array;
			}

			$items_created_result_array["payment"] = $cart_product_info["payment"];

        // adding price summary
			$cart_summary = array();
			$cart_summary["subtotal"] = (double)$quote->getSubtotal();
			$cart_summary["grand_total"] = (double)$quote->getGrandTotal();
			$cart_summary["tax_amount"] = array_sum($tax_amount);
      	//if((int)$cart_product_info["cod_fee"]){
			$cart_summary["advance_amount"] = $cart_product_info["cod_fee"];
      	//}
			if($cart_summary["grand_total"] > 50000) {
				$cart_summary["available_amount"] = $cart_summary["grand_total"] - 50000;
			} else {
				$cart_summary["available_amount"] = 0;
			}

			$cart_summary['subtotal_label'] = Mage::helper('sales')->__('Subtotal:');
	      	$cart_summary['grandtotal_label'] = Mage::helper('sales')->__('Grand Total:');
	      	$cart_summary['tax_label'] = Mage::helper('sales')->__('GST:');
	      	
			$items_created_result_array["summary"] = $cart_summary;

			if($coupon_removed_flag){
				echo json_encode(array('status' => 1, 'message' => 'Coupon Removed from cart successfully.', 'data' => $items_created_result_array));
				die;
			} else {
				echo json_encode(array('status' => 0, 'message' => 'Coupon was not removed from cart'));
				die;
			}
		}catch(Exception $e){
			echo json_encode(array('status' => 0, 'message' => 'Coupon was not removed from cart '.$e->getMessage()));
			die;
		}
	}


		/**
	   	 * @desc : place order
	     * @package     Neo_Mobileapi
	     * @author      pradeep sanku
	     */
		public function createOrderAction(){
			$quote_id = $_REQUEST['quote_id'];
			$app_order = $_REQUEST['app_order']; //1

			$quote = Mage::getModel('sales/quote')->load($quote_id );

			$paymentcode = $quote->getPayment()->getMethodInstance()->getCode();
			if($paymentcode == 'banktransfer'){
				$grand_total = (int) $quote->getGrandTotal();
	      		$credit = Mage::getModel('ebautomation/customer')->getCreditLimit($_REQUEST['userid'],$grand_total); 
				if($credit['allow'] == 0){
		          	echo json_encode(array('status' => $credit['allow'], 'message' => $credit['message']));
					exit;
		        }
			}
			

	      	

			$quoteShippingAddress = Mage::getModel('sales/quote_address')->getCollection()
									->addFieldToSelect('postcode')
									->addFieldToFilter('address_type','shipping')
									->addFieldToFilter('quote_id',$_REQUEST['quote_id'])->getData();
			$pincode = $quoteShippingAddress[0]['postcode'];
			$response = Mage::getModel('operations/serviceablepincodes')->getProductDeliveryOnDetailsPage($pincode,$paymentcode);
			$result["delivery_status"] = $response['status'];
			$result["approx_delivery"] = $response['message'];

			if($response['status'] == 0 ){
				$status = 0;
				echo json_encode(array('status' => $response['status'], 'message' => $result["approx_delivery"]));
				exit;
			}

			
			$store = Mage::app()->getStore();

			
			try{
				
				$checkbanktransferpayment = Mage::getStoreConfig('payment/banktransfer/active');
				$createOrder = new Mage_Checkout_Model_Cart_Api();
				$order_id = $createOrder->createOrder($quote_id);


				if($order_id){
					$quote_update = Mage::getModel('sales/quote')->load($quote_id);
					$quote_update->setIsActive(0);
					$quote_update->save();
					if($paymentcode == 'banktransfer' && $checkbanktransferpayment == 1)
					//if($paymentcode == 'banktransfer')
					{ 
						$order = Mage::getModel('sales/order')->loadByIncrementId($order_id); 
						$delivery_days_value = $_REQUEST['field'];
						$post_data = array();
						$post_data['order_id'] = $order->getId();
						$post_data['order_num'] = $order->getIncrementId();
						$post_data['delivery'] = $delivery_days_value;


						$model = Mage::getModel("banktransferdelivery/delivery")
						->addData($post_data)
						->save();
					}elseif($paymentcode == 'custompayment'){
						$order = Mage::getModel('sales/order')->loadByIncrementId($order_id); 
						$paymentcus = Mage::getModel('sales/order_payment')->load($order->getPayment()->getEntityId());

						$paymentcus->setApUtrNo($_REQUEST['ap_utr_no']);  
						$paymentcus->setApCheckNo('');
						$paymentcus->setApBankName($_REQUEST['ap_bank_name']);    
						$paymentcus->save();


					}elseif($paymentcode == 'replace'){ 
						$order = Mage::getModel('sales/order')->loadByIncrementId($order_id); 
						$paymentcus = Mage::getModel('sales/order_payment')->load($order->getPayment()->getEntityId());
						$paymentcus->setOrderNo($_REQUEST['past_order']);     
						$paymentcus->save(); 

					}elseif($paymentcode == 'cashondelivery') {

						//setting order status to cod verification pending
						$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
				 		$order->setStatus("codverified");
          				$order->save();
					}

					// for affiliates code
					if(isset($_REQUEST['repcode'])) {
						$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
						$quote = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);

						$args = array('order' => $order,'quote' =>$quote);
						$event = new Varien_Event($args);
						$observer = new Varien_Event_Observer();
						$observer->setData(array('event'=>$event));

						Mage::getModel('neoaffiliate/observer')->checkoutTypeOnepageSaveOrder($observer);
					}
					$order_sms = Mage::getModel('sales/order')->loadByIncrementId($order_id);

					$order_id_sms = $order_sms->getId();
					Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($order_id_sms)));
					$result = array();
          			// returning the ecommerce tracking data
					$ecommerce_tracking_array = array(
						"transaction_id"=>$order_id_sms,
						"store_name"=> Mage::app()->getStore()->getName(),
						"total"=> round($order_sms->getBaseSubtotal(),2),
						"grand_total"=>round($order_sms->getGrandTotal(),2),
						"tax" => $order_sms->getTaxAmount(),
						"shipping" => $order_sms->getShippingAmount(),
						"city" => $order_sms->getShippingAddress()->getData('city'),
						"state" => $order_sms->getShippingAddress()->getRegion(),
						"country_id" =>$order_sms->getShippingAddress()->getData('country_id'),
						"customer_id"=>$order_sms->getCustomerId() 
						);
					foreach ($order_sms->getAllItems() as $_item) {
						$category_ids = Mage::getModel('catalog/product')->load($_item->getProductId())->getCategoryids();
						$category_name = Mage::getModel('catalog/category')->load($category_ids[count($category_ids)-1])->getName();
						$item_array[] = array(
							"sku"=>$_item->getSku(),
							"name"=>$_item->getName(),
							"category_name"=>$category_name,
							"price"=>$_item->getPrice(),
							"qty" => $_item->getQtyOrdered()
							);
					}
					$result['tracking_data'] = array("ecommerce_tracking" => $ecommerce_tracking_array, "products" => $item_array);

					/*$quote_update = Mage::getModel('sales/quote')->load($quote_id);
					$quote_update->setIsActive(0);
					$quote_update->save();*/

					//save ip 
					$order_sms_1 = Mage::getModel('sales/order')->loadByIncrementId($order_id);
					$order_sms_1->setRemoteIp($_REQUEST['remote_ip']);
					$order_sms_1->setAppVersion($_REQUEST['version']);
					$order_sms_1->save();

				 

					$result['order_id'] = $order_id;

					//To get approx delivery of product
					$quoteShippingAddress = Mage::getModel('sales/quote_address')->getCollection()
									->addFieldToSelect('postcode')
									->addFieldToFilter('address_type','shipping')
									->addFieldToFilter('quote_id',$_REQUEST['quote_id'])->getData();
					$pincode = $quoteShippingAddress[0]['postcode'];
					$response = Mage::getModel('operations/serviceablepincodes')->getProductDeliveryOnDetailsPage($pincode,$paymentcode);
					$result["delivery_status"] = $response['status'];
					$result["approx_delivery"] = $response['message'];

					if($response['status'] == 0 ){
						$status = 0;
						echo json_encode(array('status' => $status, 'message' => $result["approx_delivery"]));
						exit;
					}else{
						$status = 1;
						echo json_encode(array('status' => $status, 'message' => $order_id.' Order is successfully created.','data' => $result));
						exit;
					}


					//echo json_encode(array('status' => $status, 'message' => $order_id.' Order is successfully created.','data' => $result));
					  
				}
			}catch(Exception $e){
				echo json_encode(array('status' => 0, 'message' => 'Order is not able to create '.$e->getMessage()));
				die;
			}
		}

		public function getRegionsAction(){

		}

		public function getCountryAction(){
			$result = array();
			$countryList = Mage::getResourceModel('directory/country_collection')
			->loadData()
			->toOptionArray(false);

			$result['status'] = 1;
			$result['data'] = $countryList;
			$this->getResponse()->setHeader('Content-type', 'application/json', true);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}

		public function getCityByPostcodeAction(){
			//$postcode = $_REQUEST['postcode'];

			$response = array();
			$availableflag = false;

			$data = 'Available in this location.';

			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$pincode = $_REQUEST['postcode'];

			$query = 'SELECT entity_id,city,state_code FROM city_pincodes WHERE pincode="'.$pincode.'"';
			$result = $connection->fetchRow($query);
			if(isset($result['entity_id'])){
				$availableflag = true;
			}

			if($availableflag){
				$query_region = 'SELECT region_id,default_name FROM directory_country_region WHERE code="'.$result['state_code'].'"';
				$result_region = $connection->fetchRow($query_region);
				//$response['status'] = $result_region['region_id'].'$$$$'.$result['state_code'].'$$$$'.$result_region['default_name'];
				$response['status'] = $result_region['region_id'];
			}else{
				$response['status'] = 'ERROR';
			}

			/* Return State Id & State Code */
			echo $response['status'];
		}

		public function showQuoteObjAction()
		{
			$quote_id = $_REQUEST['quote_id'];
			$store = Mage::app()->getStore();
			$quoteObj = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);
			//echo '<pre>'; print_r($quoteObj->getBillingAddress()->getData());
			//echo "<pre>"; print_r($quoteObj->getData());
			//echo '<pre>'; print_r($quoteObj->getShippingAddress()->getData());
			//echo $quoteObj->getPayment()->getMethodInstance()->getCode();
			//echo '<pre>'; print_r($quoteObj->getShippingAddress()->getData());
			die;
		}

		public function getInnovitiEMIBanksAction()
		{
			$emiData = Mage::getModel('innoviti/standard')->emiCalculator();
			$bankslist = $result =array();
			$i=0;
			foreach($emiData as $emi) {
				$bankslist[$i]['code'] = $emi['bankCode'];
				$bankslist[$i]['name'] = $emi['name'];
				$i++;
			}
			$result['status'] = '1';
			$result['data'] = $bankslist;
			$this->getResponse()->setHeader('Content-type', 'application/json', true);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}

		public function getInnovitiEMICalculationsBanksAction()
		{
			$quote_id = $_REQUEST['quote_id'];
			$bank_code = $_REQUEST['bank_code'];
			$emiData = Mage::getModel('innoviti/standard')->emiCalculator();
			$result = array();
			$result['status'] = '1';
			$result['data']['bankCode'] = $emiData[$bank_code]['bankCode'];
			$result['data']['name'] = $emiData[$bank_code]['name'];
			// removed the array indexes so that response can be itterated properly in app end
			$total_tenure = array();
			foreach ($emiData[$bank_code]['tenure'] as $key => $value) {
				$temp[] = $value;
				$total_tenure = $temp;
			}
			$result['data']['tenure'] = $total_tenure;

			$this->getResponse()->setHeader('Content-type', 'application/json', true);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}

    /*
    @author : deepakd
    @desc : getting the address listing data and affiliate status
    */
    public function proceedToCheckoutAction()
    {
    	$customerId = $_REQUEST["userid"];
    	$quoteId = $_REQUEST["quoteId"];
    	$quote = Mage::getModel('sales/quote')->load($quoteId);  
		//automatic coupon code app discount 
	    //$quote->setCouponCode('app-discount')->save();   

    	$storeId = Mage::app()->getStore()->getStoreId();
    	try {
    		/* check if the products are out of stock */
    		if(!empty($quoteId)){
    			$cart_product = new Mage_Checkout_Model_Cart_Product_Api();
    			$cart_product_listing = $cart_product->items( $quoteId, $storeId);
    			$cart_product_listing_blank_check = array_filter($cart_product_listing);
    			if(!empty($cart_product_listing_blank_check)){
    				foreach ($cart_product_listing as $key => $value) {
    					$product_id_eb = $value["product_id"];
    					$product_model = Mage::getModel('catalog/product')->load($product_id_eb);
    					$product_title_eb = $product_model->getName();
    					// $product_stock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_model);
    					// $product_qty_eb = $product_stock->getQty();
    					// if($product_qty_eb == 0) {
    					// 	echo json_encode(array('status' => 0, 'message' => $product_title_eb." product is Out of Stock. Please remove it so that you can proceed further.",'quoteId'=> $quoteId));
    					// 	exit;
    					// }
    				}
    			}
    		}
        // if customer is loggedin customer
    		if(!empty($customerId)){
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
    			$temp1["customer_address_id"] = 'new_address';
    			$temp1["street"] = 'New Address';
    			$temp1["complete_address"] = 'New Address';
    			$addressListArray[] = $temp1;
    			$customer = Mage::getModel('customer/customer')->load($customerId);
    			$customer_data = $customer->getData();
    			$affiliate_status = $customer_data["isaffiliate"];
    			$taxvat = $customer_data["taxvat"];

    			$repcode = $customer_data["repcode"];
    			$billing_options = array(array('key'=>'true','label' => 'CST Against Cform'),array('key'=> 'false','label' => 'Full CST'));

    			//echo json_encode(array('status' => 1, 'message' => 'Data retrieved successfully','address_array' => $addressListArray, 'affiliate_status' => $affiliate_status,'repcode' => $repcode, 'quoteId'=> $quoteId, 'billing_options' => $billing_options, 'affiliate_vat_no' => $taxvat));
    			
    			$data_array = array('status' => 1, 'message' => 'Data retrieved successfully','address_array' => $addressListArray, 'affiliate_status' => $affiliate_status,'repcode' => $repcode, 'quoteId'=> $quoteId, 'billing_options' => $billing_options, 'affiliate_vat_no' => null);

    			$data_array['partner_store_label'] = 'Partner Store Name';
    			//$data_array['gstin_label'] = 'GST IN';
    			$data_array['gstin_label'] = 'Fill in GST Identification Number of Shipping Address';
    			//$data_array['gstin_sub_label'] = 'Enter GST IN for your delivery address';
    			$data_array['gstin_sub_label'] = '';

    			echo json_encode($data_array);
    		} else {
        	// if customer is not loggedin customer
    			echo json_encode(array('status' => 1, 'message' => 'Data retrieved successfully','quoteId'=> $quoteId));
    		}

    	} catch (Exception $e) {
    		echo json_encode(array('status'=> 0, 'message' => $e->getMessage(),'quoteId'=> $quoteId));
    	}
    	die;
    }

    /*
    @author : deepakd
    @desc : getting the country and region from pincode
    */
    public function getCountryRegionCityFromPincodeAction()
    {
    	$pincode = $_REQUEST["pincode"];
    	$response = array();
    	$availableflag = false;

    	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

    	$query = 'SELECT entity_id,state_code,city FROM city_pincodes WHERE pincode="'.$pincode.'"';
    	$result = $connection->fetchRow($query);
    	if(isset($result['entity_id'])){
    		$availableflag = true;
    	}

    	if($availableflag){
    		$query_region = 'SELECT region_id,default_name FROM directory_country_region WHERE code="'.$result['state_code'].'"';
    		$result_region = $connection->fetchRow($query_region);
    		$temp["region_id"] = $result_region['region_id'];
    		if(!empty($result['state_code'])){
    			$exploded_values = explode("-", $result['state_code']);
    			$country_code = $exploded_values[0];
    		}
    		$temp["country_code"] = $country_code;
    		$temp["default_name"] = $result_region['default_name'];
    		$temp["city"] = $result['city'];
    		echo json_encode(array('status' => 1, 'message' => 'Data retrieved successfully','address_data' => $temp));
        //$response['status'] = $result_region['region_id'].'$$$$'.$result['state_code'].'$$$$'.$result_region['default_name'];
    	}else{
    		echo json_encode(array('status'=> 0, 'message' => 'No data found'));
    	}
    	die;
    }
    /*
    @author : deepakd
    @desc : get the ecommerce tracking response in case of billdesk payment method
    */
    public function getEcommerceTrackingAndroidIosAction()
    {
    	$customerId = $_REQUEST["userid"];
    	$customerEmail = $_REQUEST["email"];
    	if(empty($customerId)) {
    		$orderCollection = Mage::getModel("sales/order")->getCollection()
    		->addFieldToFilter('customer_email',$customerEmail)
    		->addFieldToFilter('customer_is_guest',1)
    		->setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC)
    		->addFieldToSelect('*');

    		$numberOfOrders = $orderCollection->count();
    		$newestOrder = $orderCollection->getFirstItem();
    		$order_sms = Mage::getModel('sales/order')->loadByIncrementId($newestOrder->getData('increment_id'));
    		$order_id_sms = $order_sms->getId();
				// returning the ecommerce tracking data
    		$ecommerce_tracking_array = array(
    			"transaction_id"=>$order_id_sms,
    			"store_name"=> Mage::app()->getStore()->getName(),
    			"total"=>$order_sms->getBaseSubtotal(),
    			"grand_total"=>$order_sms->getGrandTotal(),
    			"tax" => $order_sms->getTaxAmount(),
    			"shipping" => $order_sms->getShippingAmount(),
    			"city" => $order_sms->getShippingAddress()->getData('city'),
    			"state" => $order_sms->getShippingAddress()->getRegion(),
    			"country_id" =>$order_sms->getShippingAddress()->getData('country_id')
    			);
    		foreach($order_sms->getAllItems() as $_item) {
    			$category_ids = Mage::getModel('catalog/product')->load($_item->getProductId())->getCategoryids();
    			$category_name = Mage::getModel('catalog/category')->load($category_ids[count($category_ids)-1])->getName();
    			$item_array[] = array(
    				"sku"=>$_item->getSku(),
    				"name"=>$_item->getName(),
    				"category_name"=>$category_name,
    				"price"=>$_item->getPrice(),
    				"qty" => $_item->getQtyOrdered()
    				);
    		}
    		$result['tracking_data'] = array("ecommerce_tracking" => $ecommerce_tracking_array, "products" => $item_array);
    		$result['order_id'] = $order_id_sms;
    	} else {
    		$result = array();
				/*$orders = Mage::getResourceModel('sales/order_collection')
				->addFieldToSelect('*')
				->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
				->setOrder('created_at', 'desc');*/
				$orderCollection = Mage::getModel('sales/order')->getCollection()
				->addFilter('customer_id', $customerId)
				->setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC);
				$numberOfOrders = $orderCollection->count();
				$newestOrder = $orderCollection->getFirstItem();
				$order_sms = Mage::getModel('sales/order')->loadByIncrementId($newestOrder->getData('increment_id'));
				$order_id_sms = $order_sms->getId();
				// returning the ecommerce tracking data
				$ecommerce_tracking_array = array(
					"transaction_id"=>$order_id_sms,
					"store_name"=> Mage::app()->getStore()->getName(),
					"total"=>$order_sms->getBaseSubtotal(),
					"grand_total"=>$order_sms->getGrandTotal(),
					"tax" => $order_sms->getTaxAmount(),
					"shipping" => $order_sms->getShippingAmount(),
					"city" => $order_sms->getShippingAddress()->getData('city'),
					"state" => $order_sms->getShippingAddress()->getRegion(),
					"country_id" =>$order_sms->getShippingAddress()->getData('country_id')
					);
				foreach($order_sms->getAllItems() as $_item) {
					$category_ids = Mage::getModel('catalog/product')->load($_item->getProductId())->getCategoryids();
					$category_name = Mage::getModel('catalog/category')->load($category_ids[count($category_ids)-1])->getName();
					$item_array[] = array(
						"sku"=>$_item->getSku(),
						"name"=>$_item->getName(),
						"category_name"=>$category_name,
						"price"=>$_item->getPrice(),
						"qty" => $_item->getQtyOrdered()
						);
				}
				$result['tracking_data'] = array("ecommerce_tracking" => $ecommerce_tracking_array, "products" => $item_array);
				$result['order_id'] = $order_id_sms;

			}
			echo json_encode(array('status' => 1, 'message' => 'Order is successfully created','data' => $result));
			die;
		}

    /*
    @author : deepakd
    @desc : get the ecommerce tracking response in case of billdesk payment method
    */
    public function getEcommerceTrackingAndroidIosBillDeskAction()
    {
    	$orderid = $_REQUEST["order_id"];
    	$result = array();
			/*$orders = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
			->setOrder('created_at', 'desc');*/
			$order_sms = Mage::getModel('sales/order')->load($orderid);
			$order_id_sms = $order_sms->getId();
			// returning the ecommerce tracking data
			$ecommerce_tracking_array = array(
				"transaction_id"=>$order_id_sms,
				"store_name"=> Mage::app()->getStore()->getName(),
				"total"=>$order_sms->getBaseSubtotal(),
				"grand_total"=>$order_sms->getGrandTotal(),
				"tax" => $order_sms->getTaxAmount(),
				"shipping" => $order_sms->getShippingAmount(),
				"city" => $order_sms->getShippingAddress()->getData('city'),
				"state" => $order_sms->getShippingAddress()->getRegion(),
				"country_id" =>$order_sms->getShippingAddress()->getData('country_id')
				);
			foreach($order_sms->getAllItems() as $_item) {
				$category_ids = Mage::getModel('catalog/product')->load($_item->getProductId())->getCategoryids();
				$category_name = Mage::getModel('catalog/category')->load($category_ids[count($category_ids)-1])->getName();
				$item_array[] = array(
					"sku"=>$_item->getSku(),
					"name"=>$_item->getName(),
					"category_name"=>$category_name,
					"price"=>$_item->getPrice(),
					"qty" => $_item->getQtyOrdered()
					);
			}
			$result['tracking_data'] = array("ecommerce_tracking" => $ecommerce_tracking_array, "products" => $item_array);
			$result['order_id'] = $order_id_sms;

			echo json_encode(array('status' => 1, 'message' => 'Order is successfully created','data' => $result));
			die;
		}
    /**
    *@desc : get the order_id from quote_id
    */
    public function getIncrementIdFromQuoteIdAction()
    {
    	try {
    		$quote_id = $_POST["quote_id"];

    		$order_collection = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter("quote_id",$quote_id)->addAttributeToSelect("increment_id");
    		$order_entity_id_array = $order_collection->getData();
    		$increment_id = $order_entity_id_array[0]["increment_id"];
    		$result = array();

    		$order_sms = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
    		$order_id_sms = $order_sms->getId();
				// returning the ecommerce tracking data
    		$ecommerce_tracking_array = array(
    			"transaction_id"=>$order_id_sms,
    			"store_name"=> Mage::app()->getStore()->getName(),
    			"total"=>$order_sms->getBaseSubtotal(),
    			"grand_total"=>$order_sms->getGrandTotal(),
    			"tax" => $order_sms->getTaxAmount(),
    			"shipping" => $order_sms->getShippingAmount(),
    			"city" => $order_sms->getShippingAddress()->getData('city'),
    			"state" => $order_sms->getShippingAddress()->getRegion(),
    			"country_id" =>$order_sms->getShippingAddress()->getData('country_id')
    			);
    		foreach($order_sms->getAllItems() as $_item) {
    			$category_ids = Mage::getModel('catalog/product')->load($_item->getProductId())->getCategoryids();
    			$category_name = Mage::getModel('catalog/category')->load($category_ids[count($category_ids)-1])->getName();
    			$item_array[] = array(
    				"sku"=>$_item->getSku(),
    				"name"=>$_item->getName(),
    				"category_name"=>$category_name,
    				"price"=>$_item->getPrice(),
    				"qty" => $_item->getQtyOrdered()
    				);
    		}
    		$result['tracking_data'] = array("ecommerce_tracking" => $ecommerce_tracking_array, "products" => $item_array);
    		$result['order_id'] = $order_id_sms;
    		$result['increment_id'] = $increment_id;

    		echo json_encode(array('status' => 1, 'message' => 'Order is successfully created','data' => $result));
        //echo json_encode(array('status' => 1, 'increment_id' => $increment_id));
    	} catch(Exception $ex) {
    		echo json_encode(array('status' => 0, 'message' => "Transaction issue"));
    	}
    	exit;
    }

    /**
    *@desc : create order on payzapp response
    */
    public function createOrderPayZappAction()
    {
    	try {
    		$quote_id = $_POST["quote_id"];
    		$tran_id = $_POST["tran_id"];
    		$dataPickUpCode = $_POST["dataPickUpCode"];
    		$createOrder = new Mage_Checkout_Model_Cart_Api();
    		$order_id = $createOrder->createOrder($quote_id);
    		if($order_id) {
    			$order_sms = Mage::getModel('sales/order')->loadByIncrementId($order_id);
    			$order_id_sms = $order_sms->getId();
    			Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($order_id_sms)));

          // update quote set as inactive
    			$quote_update = Mage::getModel('sales/quote')->load($quote_id);
    			$quote_update->setIsActive(0);
    			$quote_update->save();

    			$order_collection = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter("quote_id",$quote_id)->addAttributeToSelect("increment_id");
    			$order_entity_id_array = $order_collection->getData();
    			$increment_id = $order_entity_id_array[0]["increment_id"];
    			$result = array();

    			$order_sms = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
    			$order_id_sms = $order_sms->getId();
    			$order_sms->addStatusHistoryComment('Payzapp Transaction Id:'.$tran_id." - ".'Payzapp DataPickUpCode:'.$dataPickUpCode);
    			$order_sms->setStatus("payzapppending");
    			$order_sms->save();
					// returning the ecommerce tracking data
    			$ecommerce_tracking_array = array(
    				"transaction_id"=>$order_id_sms,
    				"store_name"=> Mage::app()->getStore()->getName(),
    				"total"=>$order_sms->getBaseSubtotal(),
    				"grand_total"=>$order_sms->getGrandTotal(),
    				"tax" => $order_sms->getTaxAmount(),
    				"shipping" => $order_sms->getShippingAmount(),
    				"city" => $order_sms->getShippingAddress()->getData('city'),
    				"state" => $order_sms->getShippingAddress()->getRegion(),
    				"country_id" =>$order_sms->getShippingAddress()->getData('country_id')
    				);
    			foreach($order_sms->getAllItems() as $_item) {
    				$category_ids = Mage::getModel('catalog/product')->load($_item->getProductId())->getCategoryids();
    				$category_name = Mage::getModel('catalog/category')->load($category_ids[count($category_ids)-1])->getName();
    				$item_array[] = array(
    					"sku"=>$_item->getSku(),
    					"name"=>$_item->getName(),
    					"category_name"=>$category_name,
    					"price"=>$_item->getPrice(),
    					"qty" => $_item->getQtyOrdered()
    					);
    			}
    			$result['tracking_data'] = array("ecommerce_tracking" => $ecommerce_tracking_array, "products" => $item_array);
    			$result['order_id'] = $order_id_sms;
    			$result['increment_id'] = $increment_id;

    			echo json_encode(array('status' => 1, 'message' => 'Order is successfully created','data' => $result));
    		} else {
    			echo json_encode(array('status' => 1, 'message' => 'Order not created'));
    		}

    	} catch(Exception $ex) {
    		echo json_encode(array('status' => 0, 'message' => "Transaction issue"));
    	}
    	exit;
    }


// =>var1: should be either user credentials or default
// =>command:
// Get user card - get_user_cards
// Save user card - save_user_card
// Delete user card - delete_user_card
// Edit user card - edit_user_card
// Offer Key - offer_key
// Check Offer Status- check_offer_status
// Get Ibibo codes - get_merchant_ibibo_codes
// Vas for mobile sdk - vas_for_mobile_sdk


// 1.payment_hash: sha512(key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||SALT)
// 2.vas_for_mobile_sdk_hash: sha512(key|command|var1|salt)
// 3.payment_related_details_for_mobile_sdk_hash: sha512(key|command|var1|salt)
// 4.delete_user_card_hash: sha512(key|command|var1|salt)
// 5.get_user_cards_hash: sha512(key|command|var1|salt)
// 6.edit_user_card_hash: sha512(key|command|var1|salt)
// 7.save_user_card_hash: sha512(key|command|var1|salt)    

    public function payuhashkeyAction(){
    	$salt = 'ql4PQXqB';
    	$merchant_key = 'hyrEt4';
    	$data = $_REQUEST;
    	$hash = array();
    	$data['key'] = $merchant_key;
    	// echo "<pre>";print_r($data);exit;
    	if(!empty($data) && !empty($data['txnid']) && !empty($data['firstname']) && !empty($data['email']) && !empty($data['amount'])){
    		$var1 = $data['user_credentials'];
    		$params = array();
    		//"key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5"
    		$params['key'] = trim($merchant_key);
    		$params['txnid'] = $txnid =trim($data['txnid']);
    		$params['amount'] = $amount = (int) $data['amount'];
    		$params['productinfo'] = $productinfo = trim($data['productinfo']);
    		$params['firstname'] = $firstname = trim($data['firstname']);
    		$params['email'] = $email = trim($data['email']);
    		$params['udf1'] = $udf1 = trim($data['udf1']);
    		$params['udf2'] = $udf2 = trim($data['udf2']);
    		$params['udf3'] = $udf3 = trim($data['udf3']);
    		$params['udf4'] = $udf4 = trim($data['udf4']);
    		$params['udf5'] = $udf5 = trim($data['udf5']);
    		
    		$txnid = $data['txnid'];
    		$data['productinfo'] = 'Product Information';
    		
    		//$hash['payment_hash'] = hash('sha512', $merchant_key.'|'.$txnid.'|'.$data['amount'].'|'.$data['productinfo'].'|'.$data['firstname'].'|'.$data['email'].'|||||||||||'.$salt);

    		$hash['payment_hash'] = $this->get_hash($params,$salt);

    		$hash['vas_for_mobile_sdk_hash'] =strtolower(hash('sha512', $merchant_key.'|vas_for_mobile_sdk|'.$var1.'|'.$salt));
    		
    		$hash['payment_related_details_for_mobile_sdk_hash'] =strtolower(hash('sha512', $merchant_key.'|payment_related_details_for_mobile_sdk|'.$var1.'|'.$salt));
    		
    		$hash['delete_user_card_hash'] =strtolower(hash('sha512', $merchant_key.'|delete_user_card|'.$var1.'|'.$salt));

    		$hash['get_user_cards_hash'] =strtolower(hash('sha512', $merchant_key.'|get_user_cards|'.$var1.'|'.$salt));

    		$hash['edit_user_card_hash'] =strtolower(hash('sha512', $merchant_key.'|edit_user_card|'.$var1.'|'.$salt));

    		$hash['save_user_card_hash'] =strtolower(hash('sha512', $merchant_key.'|save_user_card|'.$var1.'|'.$salt));
    		
    		// $hash['save_user_card_hash'] =strtolower(hash('sha512', $merchant_key.'|save_user_card|'.$var1.'|'.$salt));

    		$new_hash = $this->getHashes($txnid, $amount, $productinfo, $firstname, $email, $var1, $udf1, $udf2, $udf3, $udf4, $udf5,$offerKey,$cardBin);
    		$response = $new_hash;
    		$response['status'] = 0;
    		$response['message'] = 'successfully generated hash';
    		echo json_encode($response);
    		exit;
    	}else{
    	echo json_encode(array('status' => 1, 'data'=>''));
    	exit;
    		
    	}
    }

    public static function get_hash($params,$salt)
	{
		$posted = array ();
		
		if ( ! empty( $params ) ) foreach ( $params as $key => $value )
			$posted[$key] = htmlentities( $value, ENT_QUOTES );
		
		// $hash_sequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
		
		$hash_sequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5";
		
		$hash_vars_seq = explode( '|', $hash_sequence );
		$hash_string = null;
		
		foreach ( $hash_vars_seq as $hash_var ) {
			$hash_string .= isset($posted[$hash_var])?$posted[$hash_var]:'';
			$hash_string .= '|';
		}
		
		$hash_string .= $salt;

		$hash_string = (string) $hash_string;
		// echo $hash_string; exit;
		return strtolower( hash( 'sha512', $hash_string ) );
	}

	function getHashes($txnid, $amount, $productinfo, $firstname, $email, $user_credentials, $udf1, $udf2, $udf3, $udf4, $udf5,$offerKey,$cardBin)
	{
      // $firstname, $email can be "", i.e empty string if needed. Same should be sent to PayU server (in request params) also.
      $key = 'hyrEt4';//'gtKFFx';
      $salt = 'ql4PQXqB';//'eCwWELxi';
  
      $payhash_str = $key . '|' . $this->checkNull($txnid) . '|' .$this->checkNull($amount)  . '|' .$this->checkNull($productinfo)  . '|' . $this->checkNull($firstname) . '|' . $this->checkNull($email) . '|' . $this->checkNull($udf1) . '|' . $this->checkNull($udf2) . '|' . $this->checkNull($udf3) . '|' . $this->checkNull($udf4) . '|' . $this->checkNull($udf5) . '||||||' . $salt;
	      $paymentHash = strtolower(hash('sha512', $payhash_str));
	      $arr['payment_hash'] = $paymentHash;

	      $cmnNameMerchantCodes = 'get_merchant_ibibo_codes';
	      $merchantCodesHash_str = $key . '|' . $cmnNameMerchantCodes . '|'.$user_credentials.'|' . $salt ;
	      $merchantCodesHash = strtolower(hash('sha512', $merchantCodesHash_str));
	      $arr['get_merchant_ibibo_codes_hash'] = $merchantCodesHash;

	      $cmnMobileSdk = 'vas_for_mobile_sdk';
	      $mobileSdk_str = $key . '|' . $cmnMobileSdk . '|'.$user_credentials.'|' . $salt;
	      $mobileSdk = strtolower(hash('sha512', $mobileSdk_str));
	      $arr['vas_for_mobile_sdk_hash'] = $mobileSdk;

	      $cmnPaymentRelatedDetailsForMobileSdk1 = 'payment_related_details_for_mobile_sdk';
	      $detailsForMobileSdk_str1 = $key  . '|' . $cmnPaymentRelatedDetailsForMobileSdk1 . '|'.$user_credentials.'|' . $salt ;
	      $detailsForMobileSdk1 = strtolower(hash('sha512', $detailsForMobileSdk_str1));
	      $arr['payment_related_details_for_mobile_sdk_hash'] = $detailsForMobileSdk1;

	      //used for verifying payment(optional)  
	      $cmnVerifyPayment = 'verify_payment';
	      $verifyPayment_str = $key . '|' . $cmnVerifyPayment . '|'.$txnid .'|' . $salt;
	      $verifyPayment = strtolower(hash('sha512', $verifyPayment_str));
	      $arr['verify_payment_hash'] = $verifyPayment;

      	if($user_credentials != NULL && $user_credentials != '')
      	{
            $cmnNameDeleteCard = 'delete_user_card';
            $deleteHash_str = $key  . '|' . $cmnNameDeleteCard . '|' . $user_credentials . '|' . $salt ;
            $deleteHash = strtolower(hash('sha512', $deleteHash_str));
            $arr['delete_user_card_hash'] = $deleteHash;
            
            $cmnNameGetUserCard = 'get_user_cards';
            $getUserCardHash_str = $key  . '|' . $cmnNameGetUserCard . '|' . $user_credentials . '|' . $salt ;
            $getUserCardHash = strtolower(hash('sha512', $getUserCardHash_str));
            $arr['get_user_cards_hash'] = $getUserCardHash;
            
            $cmnNameEditUserCard = 'edit_user_card';
            $editUserCardHash_str = $key  . '|' . $cmnNameEditUserCard . '|' . $user_credentials . '|' . $salt ;
            $editUserCardHash = strtolower(hash('sha512', $editUserCardHash_str));
            $arr['edit_user_card_hash'] = $editUserCardHash;
            
            $cmnNameSaveUserCard = 'save_user_card';
            $saveUserCardHash_str = $key  . '|' . $cmnNameSaveUserCard . '|' . $user_credentials . '|' . $salt ;
            $saveUserCardHash = strtolower(hash('sha512', $saveUserCardHash_str));
            $arr['save_user_card_hash'] = $saveUserCardHash;
            
            $cmnPaymentRelatedDetailsForMobileSdk = 'payment_related_details_for_mobile_sdk';
            $detailsForMobileSdk_str = $key  . '|' . $cmnPaymentRelatedDetailsForMobileSdk . '|' . $user_credentials . '|' . $salt ;
            $detailsForMobileSdk = strtolower(hash('sha512', $detailsForMobileSdk_str));
            $arr['payment_related_details_for_mobile_sdk_hash'] = $detailsForMobileSdk;
      	}


    	if ($offerKey!=NULL && !empty($offerKey)) {
			$cmnCheckOfferStatus = 'check_offer_status';
            		$checkOfferStatus_str = $key  . '|' . $cmnCheckOfferStatus . '|' . $offerKey . '|' . $salt ;
	            	$checkOfferStatus = strtolower(hash('sha512', $checkOfferStatus_str));
			$arr['check_offer_status_hash']=$checkOfferStatus;
		}


		if ($cardBin!=NULL && !empty($cardBin)) {
			$cmnCheckIsDomestic = 'check_isDomestic';
            		$checkIsDomestic_str = $key  . '|' . $cmnCheckIsDomestic . '|' . $cardBin . '|' . $salt ;
	            	$checkIsDomestic = strtolower(hash('sha512', $checkIsDomestic_str));
			$arr['check_isDomestic_hash']=$checkIsDomestic;
		}
        
      

    	// return array('result'=>$arr);
    	return $arr;
	}

	function checkNull($value) {
			if ($value == null) {
				return '';
			} else {
				return $value;
			}
		}

} ?>
