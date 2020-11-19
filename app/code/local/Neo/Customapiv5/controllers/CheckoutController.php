<?php
	class Neo_Customapiv5_CheckoutController extends Neo_Customapiv5_Controller_HttpAuthController
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
			$lastname = $_REQUEST['lastname'];
			$email = $_REQUEST['email'];
			$street = $_REQUEST['street'];
			$postcode = $_REQUEST['postcode'];
			$password = $_REQUEST['password'];
			$confirm_password = $_REQUEST['confirm_password'];
			$billing_use_for_shipping_yes = $_REQUEST['billing_use_for_shipping_yes'];
			$customer_cform = $_REQUEST['customer_cform'];

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
				}catch(Exception $e){
					echo json_encode(array('status' => 0, 'message' => 'Customer address doesnt exists'.$e->getMessage()));
					die;
				}
			}else{
				$required = array("firstName","lastname","street","postcode","telephone","country_id","city","region_id","mobile");
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
													        "fax" => $_REQUEST['mobile'],
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
													        "fax" => $_REQUEST['mobile'],
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
			$lastname = $_REQUEST['lastname'];
			$email = $_REQUEST['email'];
			$street = $_REQUEST['street'];
			$postcode = $_REQUEST['postcode'];

			$addressData = array();

			$shippingMethod = "freeshipping_freeshipping";

			$websiteId = Mage::app()->getWebsite()->getId();
			$store = Mage::app()->getStore();

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
				}catch(Exception $e){
					echo json_encode(array('status' => 0, 'message' => 'Customer address doesnt exists'.$e->getMessage()));
					die;
				}
			}else{
				$required = array("firstName","lastname","street","postcode","telephone","country_id","city","region_id","mobile");
				$required_err['error'] = array();
				foreach ($required as $requiredField) {
				    if (!isset($_REQUEST[$requiredField]) || $_REQUEST[$requiredField] == "") {
				        $required_err['error'][] = $requiredField . " is required.";
				    }
				}

				if(empty($required_err['error'])){
					$addressData['firstname'] = $_REQUEST['firstName'];
					$addressData['lastname'] = $_REQUEST['lastname'];
					$addressData['email'] = $_REQUEST['email'];
					$addressData['street'] = $_REQUEST['street'];
					$addressData['city'] = $_REQUEST['city'];
					$addressData['postcode'] = $_REQUEST['postcode'];
					$addressData['telephone'] = $_REQUEST['telephone'];
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
		public function getAvailablePaymentMethodsAction(){
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
		}

		/**
	   	 * @desc : set SelectedPaymentMethod in the quote object
	     * @package     Neo_Mobileapi
	     * @author      pradeep sanku
	     */
		public function setSelectedPaymentMethodsAction(){
			$quote_id = $_REQUEST['quote_id'];
			$paymentData = array();
			$paymentData['method'] = $_REQUEST['payment_method'];
			try{
				$cart_payment = new Mage_Checkout_Model_Cart_Payment_Api();
				$result = $cart_payment->setPaymentMethod($quote_id,$paymentData);
				if($result){
            $storeId = Mage::app()->getStore()->getStoreId();
            $cart_product = new Mage_Checkout_Model_Cart_Api();
            $cart_product_info = $cart_product->info( $quote_id, $storeId);
            //echo "<pre>"; print_r($cart_product_info); exit;
            $cart_product_info_blank_check = array_filter($cart_product_info);
            //removing unnecessary data

            //creating the items array
            $items_created_result_array = array();
            $items_array = $cart_product_info["items"];
            //echo "<pre>"; print_r($items_array); exit;
            foreach ($items_array as $key => $value) {
              $product = Mage::getModel('catalog/product')->load($value["product_id"]);
              $items_created_array["product_id"] = $value["product_id"];
              $items_created_array["name"] = $value["name"];
              $items_created_array["price"] = number_format($product->getData('price'),2,".","");
              $items_created_array["qty"] = $value["qty"];

              $items_created_array["thumbnail"] = (string)Mage::helper('catalog/image')->init($product,'image');
              $items_created_result_array["items"][] = $items_created_array;
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
		      	$cart_summary["tax_amount"] = $cart_product_info["cod_tax_amount"];
		      	//if((int)$cart_product_info["cod_fee"]){
	      		$cart_summary["advance_amount"] = $cart_product_info["cod_fee"];
		      	//}
		      	if($cart_summary["grand_total"] > 50000) {
							$cart_summary["available_amount"] = $cart_summary["grand_total"] - 50000;
						} else {
							$cart_summary["available_amount"] = 0;
						}
		      	$items_created_result_array["summary"] = $cart_summary;

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
			//$_REQUEST['repcode'] = 'tete';
			//$_REQUEST['aff_customer_name'] = 'PraSan';
			//$_REQUEST['aff_customer_email'] = 'pradeepsanku@gmail.com';
			//$_REQUEST['aff_customer_mobile'] = '9833206826';
			$store = Mage::app()->getStore();

			// echo json_encode(array('status' => 0, 'message' => 'Order is not able to create '.$quote_id." ".$app_order));
			// die;

			try{
				$createOrder = new Mage_Checkout_Model_Cart_Api();
				$order_id = $createOrder->createOrder($quote_id);

				// echo '<pre>';
				// print_r($order_id);
				// exit;


				if($order_id){

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
              "total"=>$order_sms->getBaseSubtotal(),
              "grand_total"=>$order_sms->getGrandTotal(),
              "tax" => $order_sms->getTaxAmount(),
              "shipping" => $order_sms->getShippingAmount(),
              "city" => $order_sms->getShippingAddress()->getData('city'),
              "state" => $order_sms->getShippingAddress()->getRegion(),
              "country_id" =>$order_sms->getShippingAddress()->getData('country_id')
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

					$quote_update = Mage::getModel('sales/quote')->load($quote_id);
				  $quote_update->setIsActive(0);
				  $quote_update->save();

					$result['order_id'] = $order_id;
					echo json_encode(array('status' => 1, 'message' => $order_id.' Order is successfully created.','data' => $result));
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
                $product_qty_eb = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_model)->getQty();
                if($product_qty_eb == 0) {
									echo json_encode(array('status' => 0, 'message' => $product_title_eb." product is Out of Stock. Please remove it so that you can proceed further.",'quoteId'=> $quoteId));
									die;
                }
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

		        echo json_encode(array('status' => 1, 'message' => 'Data retrieved successfully','address_array' => $addressListArray, 'affiliate_status' => $affiliate_status,'repcode' => $repcode, 'quoteId'=> $quoteId, 'billing_options' => $billing_options, 'affiliate_vat_no' => $taxvat));
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
	}
?>
