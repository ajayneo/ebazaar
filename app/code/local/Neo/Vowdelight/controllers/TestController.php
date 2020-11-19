<?php
class Neo_Vowdelight_TestController extends Mage_Core_Controller_Front_Action{
	public function createOrderAction(){
		echo "begin create order -----------------<br/>";
		//Static values required for new order
		$customer_id = 37171; //web.maheshgurav
		$product_id = 10885; //rpcdev
		$qty = 5; //count request
		$oderNumber = 200079123; //
		$method = 'cashondelivery';
		$_order = Mage::getModel('sales/order')->loadByIncrementId($oderNumber);
		
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		// Start New Sales Order Quote
		$quote = Mage::getModel('sales/quote')->setStoreId($store->getId());
		 
		// Set Sales Order Quote Currency
		$quote->setCurrency($order->AdjustmentAmount->currencyID);
		
		$customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->load($customer_id); //static value
		
		// Assign Customer To Sales Order Quote
		$quote->assignCustomer($customer);
		 
		// Configure Notification
		$quote->setSendCconfirmation(1);
		
		//Add product to order quote
		$product=Mage::getModel('catalog/product')->load($product_id); //static value
		// $quote->addProduct($product,new Varien_Object(array('qty'   => $qty))); //static value
		$buyInfo = array('qty'   => $qty);
		$quote->addProduct($product, new Varien_Object($buyInfo))->setOriginalCustomPrice(0); /*after change */

		$billingAddress = $_order->getBillingAddress()->getData();
		$shippingAddress = $_order->getShippingAddress()->getData();

		$billingAddress = $quote->getBillingAddress()->addData($billingAddress);
		$shippingAddress = $quote->getShippingAddress()->addData($shippingAddress);
		 if($shipprice==0){
		     $shipmethod='freeshipping_freeshipping';
		 }
		 
		 // Collect Rates and Set Shipping & Payment Method
		 $shippingAddress->setCollectShippingRates(true)
		                 ->collectShippingRates()
		                 ->setShippingMethod('freeshipping_freeshipping')
		                 ->setPaymentMethod($method);

		 // $shippingAddress->setPaymentMethod($method);
		 
		 // Set Sales Order Payment
		 $quote->getPayment()->importData(array('method' => $method));
		 
		 // Collect Totals & Save Quote
		 $quote->collectTotals()->save();
		 
		 // Create Order From Quote
		 $service = Mage::getModel('sales/service_quote', $quote);
		 $service->submitAll();
		 $increment_id = $service->getOrder()->getRealOrderId();
		 
		 // Resource Clean-Up
		 $quote = $customer = $service = null;
		 
		 // Finished
		 echo $increment_id;
	}

	public function couponAction(){
		echo "<pre>";
		//Mage_SalesRule_Model_Coupon_Massgenerator
		$generator = Mage::getModel('salesrule/coupon_massgenerator');
		var_dump($generator);
		//prepare data
		$data = array(
		    'max_probability'   => .25,
		    'max_attempts'      => 1,
		    'uses_per_customer' => 1,
		    'uses_per_coupon'   => 1,
		    'qty'               => 1, //number of coupons to generate
		    'length'            => 14, //length of coupon string
		    'to_date'           => date('Y-m-d'), //ending date of generated promo
		    'prefix'            => 'VOW-DELIGHT',
		    // 'suffix'            => '',
		    // 'dash'              => 0,
		    /**
		     * Possible values include:
		     * Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC
		     * Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHABETICAL
		     * Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_NUMERIC
		     */
		    'format'          => Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC,
		    'rule_id'         => 3628 //the id of the rule you will use as a template
		);
	
	try{
		//validate
		$generator->validateData($data);
		var_dump($generator->getData());
		//generate
		$generator->generatePool();

		$generator->save();

		print_r($generator->getData());
	}catch(Exception $e){
		echo $e->getMessage();
	}

		//get a count of how many were generated successfully if you need:

		$generator->getGeneratedCount();

		$salesRule = Mage::getModel('salesrule/rule')->load($data['rule_id']);
		$collection = Mage::getResourceModel('salesrule/coupon_collection')
		            ->addRuleToFilter($salesRule)
		            ->addGeneratedCouponsFilter();

		print_r($collection->getData());
	}

	public function coupon2Action(){
		// Get the rule in question
		$rule = Mage::getModel('salesrule/rule')->load(3628); //21 = ID of coupon in question

		// Define a coupon code generator model instance
		// Look at Mage_SalesRule_Model_Coupon_Massgenerator for options
		$generator = Mage::getModel('salesrule/coupon_massgenerator');

		$parameters = array(
		    'count'=>1,
		    'format'=>'alphanumeric',
		    // 'dash_every_x_characters'=>4,
		    'prefix'=>'VOW-DELIGHT-',
		    'suffix'=>'',
		    'length'=>12
		);

		if( !empty($parameters['format']) ){
		  switch( strtolower($parameters['format']) ){
		    case 'alphanumeric':
		    case 'alphanum':
		      $generator->setFormat( Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC );
		      break;
		    case 'alphabetical':
		    case 'alpha':
		      $generator->setFormat( Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHABETICAL );
		      break;
		    case 'numeric':
		    case 'num':
		      $generator->setFormat( Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_NUMERIC );
		      break;
		  }
		}

		$generator->setDash( !empty($parameters['dash_every_x_characters'])? (int) $parameters['dash_every_x_characters'] : 0);
		$generator->setLength( !empty($parameters['length'])? (int) $parameters['length'] : 6);
		$generator->setPrefix( !empty($parameters['prefix'])? $parameters['prefix'] : '');
		$generator->setSuffix( !empty($parameters['suffix'])? $parameters['suffix'] : '');
		
		// $generator->generatePool();

		$generator->save();
		
		// $collection = Mage::getResourceModel('salesrule/coupon_collection')
		//             ->addRuleToFilter($rule)
		//             ->addGeneratedCouponsFilter();

		print_r($collection->getData());
	}

	public function coupon3Action(){
		$coupon = Mage::getModel('salesrule/coupon');

		$productId = 16197;

		// load product
	    /** @var Mage_Catalog_Model_Product $product */
	    $product = Mage::getModel('catalog/product')
	                    ->setStoreId($storeId)
	                    ->load($productId);
	 
	    // set length of coupon code
	    /** @var Mage_SalesRule_Model_Coupon_Codegenerator $generator */
	    $generator = Mage::getModel('salesrule/coupon_codegenerator')
	                        ->setLength(8);
	    /** @var Mage_SalesRule_Model_Rule_Condition_Product $conditionProduct */
	    $conditionProduct = Mage::getModel('salesrule/rule_condition_product')
	                                                ->setType('salesrule/rule_condition_product')
	                                                ->setAttribute('sku')
	                                                ->setOperator('==')
	                                                ->setValue($product->getSku());
	                                                 
	    /** @var Mage_SalesRule_Model_Rule_Condition_Product_Found $conditionProductFound */
	    $conditionProductFound = Mage::getModel('salesrule/rule_condition_product_found')
	                                            ->setConditions(array($conditionProduct));
	    /** @var Mage_SalesRule_Model_Rule_Condition_Combine $condition */
	    $condition = Mage::getModel('salesrule/rule_condition_combine')
	                    ->setConditions(array($conditionProductFound));
	                                                 
	    /** @var Mage_SalesRule_Model_Coupon $coupon */
	    $coupon = Mage::getModel('salesrule/coupon');
	    // try to generate unique coupon code
	    $attempts = 0;
	    do {
	        if ($attempts++ >= 8) {
	            Mage::throwException(Mage::helper('mymodule')->__('Unable to create requested Coupons. Please try again.'));
	        }
	        $code = $generator->generateCode();
	    } while ($coupon->getResource()->exists($code));

	    // echo $code; 

	    $rule = Mage::getModel('salesrule/rule');
	    // print_r($rule);
	    $rule->setName(Mage::helper('vowdelight')->__('Vow'))
        ->setDescription($rule->getName())
        ->setFromDate(date('Y-m-d'))
        ->setCustomerGroupIds(4)
        ->setIsActive(1)
        ->setConditionsSerialized(serialize($condition->asArray()))
        //->setActionsSerialized     
        //->setStopRulesProcessing 
        //->setIsAdvanced                     
        ->setSimpleAction(Mage_SalesRule_Model_Rule::BY_FIXED_ACTION)
        ->setDiscountAmount($product->getFinalPrice())
        ->setDiscountQty(1)
        //->setDiscountStep                     
        ->setStopRulesProcessing(0)
        ->setIsRss(0)
        ->setWebsiteIds(array(1))
        ->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
        ->setConditions($condition)
        ->save();

	}

	public function trycouponAction(){
		//coupon is VOW-DELIGHT-D58RA-WMNQ9-WZ
		//$quote->setCouponCode($couponCode); 

		try{
			$session = Mage::getSingleton('core/session');
			$vow_mobile_order_flag = 'Yes';
			$session = $session->setData('vow_mobile_order_flag',$vow_mobile_order_flag);
			$method = 'cashondelivery';
			$oderNumber = 200079129;
			$_order = Mage::getModel('sales/order')->loadByIncrementId($oderNumber);
			
			$websiteId = Mage::app()->getWebsite()->getId();
			$store = Mage::app()->getStore();
			// Start New Sales Order Quote
			$quote = Mage::getModel('sales/quote')->setStoreId($store->getId());
			 
			// Set Sales Order Quote Currency
			$quote->setCurrency($order->AdjustmentAmount->currencyID);
			$customer_id = 37171;
			$customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->load($customer_id); //static value
			
			// Assign Customer To Sales Order Quote
			$quote->assignCustomer($customer);
			 
			// Configure Notification
			// $quote->setSendCconfirmation(1);
			$quote->setSendConfirmation(1);			
			
			//Add product to order quote
			$product_id = 16197;
			$qty = 1;
			$product = Mage::getModel('catalog/product')->load($product_id); //static value
			// echo $product->getSku(); exit;
			$buyInfo = array('qty'   => $qty);
			$quote->addProduct($product,new Varien_Object($buyInfo)); //static value
			// $quote->addProduct($product, new Varien_Object($buyInfo))->setOriginalCustomPrice(0); /*after change */

			//apply coupon
			$couponCode = 'VOW-DELIGHT-D58RA-WMNQ9-WZ';
			$quote->setCouponCode($couponCode)->save();
			// $quote->collectTotals()->save();

			$billingAddress = $_order->getBillingAddress()->getData();
			$shippingAddress = $_order->getShippingAddress()->getData();

			$billingAddress = $quote->getBillingAddress()->addData($billingAddress);
			$shippingAddress = $quote->getShippingAddress()->addData($shippingAddress);
			 if($shipprice==0){
			     $shipmethod='freeshipping_freeshipping';
			 }
			 
			 // Collect Rates and Set Shipping & Payment Method
			 $shippingAddress->setCollectShippingRates(true)
			                 ->collectShippingRates()
			                 ->setShippingMethod('freeshipping_freeshipping')
			                 ->setPaymentMethod($method);

			 $shippingAddress->setPaymentMethod($method);
			 
			 // Set Sales Order Payment
			 $quote->getPayment()->importData(array('method' => $method));
  
			 // Collect Totals & Save Quote
			 $quote->collectTotals()->save();
			 
			 // print_r($quote->getData()); exit;

			 // Create Order From Quote
			 $service = Mage::getModel('sales/service_quote', $quote);
			 $service->submitAll();
			 $increment_id = $service->getOrder()->getRealOrderId();
			 
			 // Resource Clean-Up
			 $quote = $customer = $service = null;
			 $session->unsetData('vow_mobile_order_flag');
		 
			 // Finished
			 echo  $increment_id;
		}catch(Exception $e){
			//error
			echo $e->getMessage();exit;
			return $e->getMessage();
		}
	}

	public function genAction(){
		$rule_id = '3632';
		// Define a coupon code generator model instance
	    // Look at Mage_SalesRule_Model_Coupon_Massgenerator for options
	    $generator = Mage::getModel('salesrule/coupon_massgenerator');
	    $generator->setPrefix('Vow-Delight-');
	    $generator->setLength(15);
	    $code = $generator->generateCode();

	     /** @var Mage_SalesRule_Model_Coupon $coupon */
    	$coupon = Mage::getModel('salesrule/coupon');

	    // create coupon
	    $coupon->setId(null)
	        ->setRuleId($rule_id)
	        ->setCode($code)
	        ->setUsageLimit(1)
	        //->setUsagePerCustomer
	        //->setTimesUsed
	        //->setExpirationDate
	        ->setIsPrimary(1)
	        ->setCreatedAt(time())
	        ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED);
	        // ->save();

	        echo $coupon->getSelect();

	}

	public function ReversePickupAction(){
		echo "RVP Begins...............<br>";
		$req_id = 1002;
		$vowResource = Mage::getResourceModel('vowdelight/vowdelight_collection');
		$vowResource->addFieldToFilter('request_id',$req_id);
		$vowOldImei = $vowResource->addFieldToSelect(array('request_id','old_order_no','old_imei_no','sku'));
		$vowCustomer = $vowResource->addFieldToSelect(array('customer_id'));
		
		$test_url =  'http://staging.ecomexpress.in/apiv2/manifest_awb_rev_v2/';
		$testusername = 'ecomexpress';
		$testpassword = 'Ke$3c@4oT5m6h#$';

		foreach ($vowOldImei as $vow) {
			//get order address
			$order_no = $vow->getOldOrderNo();
			$_order = Mage::getModel('sales/order')->loadbyIncrementId($order_no);
			$_address = $_order->getShippingAddress();
			$revpickup_name = $_address->getFirstname().' '.$_address->getLastname();
			$revpickup_address1 = $_address->getStreet();
			$revpickup_city = $_address->getCity();
			$revpickup_pincode = $_address->getPostcode();
			$revpickup_pincode = 111111;
			$revpickup_state = $_address->getRegion();
			$revpickup_mobile = $_address->getTelephone();
			$order_id = $_order->getEntityId();
			$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress($order_id);
			// echo "<pre>";
			// print_r($warehouseAddress);
			// echo "</pre>";
			$seller_name = $warehouseAddress[0];
			$seller_address =  $warehouseAddress[1].', '.$warehouseAddress[2].' ,'.$warehouseAddress[3];
			$seller_pincode = $warehouseAddress[4];			
			$seller_pincode = 111111;			
			$seller_state = $warehouseAddress[5];			
			$seller_mobile1 = $warehouseAddress[7]; //9833538063			
			$seller_mobile2 = $warehouseAddress[8];	//9769492607
			//get product name
			$item_description = 'VOW V105 (Black + Blue)';
			$price = 525;
			$serial_no = $vow->getOldImeiNo();

			$awb_numbers = Mage::getModel('shippinge/ecom')->rvpawb(1);
			$awb_no = $awb_numbers[0];
			//payload
			$rvp_data_live = '{ "AWB_NUMBER": "'.$awb_no.'", 
			"ORDER_NUMBER": "'.$order_no.'", 
			"PRODUCT": "rev", 
			"REVPICKUP_NAME": "'.$revpickup_name.'", 
			"REVPICKUP_ADDRESS1": "'.$revpickup_address1.'", 
			"REVPICKUP_ADDRESS2": "", 
			"REVPICKUP_ADDRESS3": "", 
			"REVPICKUP_CITY": "'.$revpickup_city.'", 
			"REVPICKUP_PINCODE": '.$revpickup_pincode.', 
			"REVPICKUP_STATE": "'.$revpickup_state.'", 
			"REVPICKUP_MOBILE": "'.$revpickup_mobile.'", 
			"REVPICKUP_TELEPHONE": "'.$revpickup_mobile.'", 
			"PIECES": "1", 
			"COLLECTABLE_VALUE": "0", 
			"DECLARED_VALUE": "0", 
			"ACTUAL_WEIGHT": "1", 
			"VOLUMETRIC_WEIGHT": "1", 
			"LENGTH": "1", 
			"BREADTH": "1", 
			"HEIGHT": "1", 
			"VENDOR_ID": "", 
			"DROP_NAME": "'.$seller_name.'", 
			"DROP_ADDRESS_LINE1": "'.$seller_address.'", 
			"DROP_ADDRESS_LINE2": "", 
			"DROP_PINCODE": '.$seller_pincode.', 
			"DROP_MOBILE": "'.$seller_mobile1.'", 
			"ITEM_DESCRIPTION": "'.$item_description.'", 
			"DROP_PHONE": "'.$seller_mobile2.'", 
			"EXTRA_INFORMATION": "", 
			"DG_SHIPMENT": "true", 
			"ADDONSERVICE": ["QC"], 
			 "QC": [{
		            "QCCHECKCODE": "GEN_ITEM_DESC_CHECK",
		            "VALUE": "'.$item_description.'",
		            "DESC": "CHECK"
		        }, {
		            "QCCHECKCODE": "GEN_MOBILE_IMEI_LAST4_INPUT",
		            "VALUE": "'.$serial_no.'",
		            "DESC": "CHECK"
		        }, {
		            "QCCHECKCODE": "GEN_ITEM_DAMAGE_CHECK",
		            "VALUE": "YES",
		            "DESC": "CHECK"
	        }], "ADDITIONAL_INFORMATION": {
            "ITEM_VALUE": "'.$price.'",
            "ITEM_CATEGORY": "ELECTRONICS",
			"SELLER_NAME": "'.$seller_name.'",
			"SELLER_ADDRESS": "'.$seller_address.'",
			"SELLER_STATE": "'.$seller_state.'",
			"SELLER_PINCODE": "'.$seller_pincode.'",
			"SELLER_TIN": "",
			"INVOICE_NUMBER": "",
			"INVOICE_DATE": "",
			"ESUGAM_NUMBER": "",
			"SELLER_GSTIN": "",
			"GST_HSN": "",
			"GST_ERN": "",
			"GST_TAX_NAME": "",
			"GST_TAX_BASE": "",
			"GST_TAX_RATE_CGSTN": "",
			"GST_TAX_RATE_SGSTN": "",
			"GST_TAX_RATE_IGSTN": "",
			"GST_TAX_TOTAL": "",
			"GST_TAX_CGSTN": "",
			"GST_TAX_SGSTN": "",
			"GST_TAX_IGSTN": "",
			"DISCOUNT": ""}}';

			// echo $rvp_data_live.'<br>';

			$payload = '{ "ECOMEXPRESS-OBJECTS": { "SHIPMENT":'.$rvp_data_live.'}}';
			

			$data['username']=$testusername;
			$data['password']=$testpassword;
			$data['json_input'] = $payload;
			
			$ch = curl_init();                    // initiate curl
			curl_setopt($ch, CURLOPT_URL,$test_url);
			curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // define what you want to post
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
			$output = curl_exec($ch); // execute
			$output_array = json_decode($output,1);

			echo "<pre>"; print_r($output);
			// Mage::log($output_array,null,'rvpdata_live.log',true);
			//response object
			$airwaybill_response = $output_array['RESPONSE-OBJECTS']['AIRWAYBILL-OBJECTS']['AIRWAYBILL'];
			
			//shipment object
			$shipments = $output_array['shipments'];
			
			if($airwaybill_response && !$shipments){
				$success = $airwaybill_response['success'];
				$airwaybill_number = $airwaybill_response['airwaybill_number'];
				if($success == 'False'){
					$error_message = $airwaybill_response['error_list']['reason_comment'];
					$response_msg[$i]['serial'] = $serial_no;
					$response_msg[$i]['msg'] = $airwaybill_response['airwaybill_number'];
					$response_msg[$i]['error'] = 1;
					
				}else if($success == 'True'){
					$response_msg[$i]['serial'] = $serial_no;
					$response_msg[$i]['msg'] = $airwaybill_response['airwaybill_number'];
					$response_msg[$i]['error'] = 0;
					//set AWB number in vow delight
					$vow->setReversePayload($payload);
					$vow->setRvpAwbNo($airwaybill_response['airwaybill_number']);
					$vow->save();
				}			
			}

			if($shipments){
				$response_msg[$i]['serial'] = $serial_no;
				$response_msg[$i]['msg'] = $shipments[0]['reason'];
				$response_msg[$i]['error'] = 1;
			}
			$i++;	
		}
	}
} ?>