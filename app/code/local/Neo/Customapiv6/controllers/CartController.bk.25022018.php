<?php
	class Neo_Customapiv6_CartController extends Neo_Customapiv6_Controller_HttpAuthController
	{
		public function addToCartAction()
		{
			
			$websiteId = Mage::app()->getWebsite()->getId();
    		$store = Mage::app()->getStore();

		    $product_id = $_REQUEST["product_id"];
		    $product_qty = $_REQUEST["qty"];
		    $customerId = $_REQUEST["userid"];
		    $wishlistId = $_REQUEST['wishlist_item_id'];

		    if(isset($_REQUEST["quoteId"]))
			{
			    $customer = Mage::getModel('customer/customer')->load($customerId);

				$quoteId = $_REQUEST["quoteId"];
				
				// $quote = Mage::getModel('sales/quote')->load($quoteId); 
				// $quote->assignCustomer($customer);
				// $quote->setStoreId($store->getId());
				// $quote->collectTotals();
				// $quote->setIsActive(true);
				// $quote->save();
				//automatic coupon code app discount 
			    //$quote->setCouponCode('app-discount')->save(); 


			} else {
				$quoteId = NULL;
			}
			$quoteId = NULL;

		    $storeId = Mage::app()->getStore()->getStoreId();
		    if($product_id){
		    	$_product = Mage::getModel('catalog/product')->load($product_id);

		    	// echo $_product->getSku();
		    	//remove this 22nd Feb 18
				// if($_product->getSku() == 'X-STANDER-RED'){
				// 	echo json_encode(array('status' => 0, 'message' => 'Sorry! This Product is not available to purchase for now.'));
				// 	exit;
				// }

				if($_product->getPrice() == 0){
					echo json_encode(array('status' => 0, 'message' => 'Sorry! This Product is not available to purchase for now.'));
					exit;
				}

		    	// $featured_sku = array('NEWNBK00057','NEWNBK00058','NEWNBK00059','NEWNBK00060','NEWNBK00061','NEWNBK00062','NEWNBK00063','NEWNBK00064','NEWNBK00065','NEWNBK00066','NEWNBK00067','NEWNBK00068','NEWNBK00069','NEWNBK00070','NEWNBK00071','NEWNBK00072','NEWNBK00073','NEWNBK00074','NEWNBK00075','NEWNBK00076','NEWNBK00077','NEWNBK00078','NEWNBK00079','NEWNBK00080','NEWNBK00081','NEWNBK00082','NEWNBK00083','NEWNBK00084','NEWNBK00085','NEWNBK00086','NEWNBK00087','NEWNBK00088','NEWNBK00089','NEWNBK00090','NEWNBK00091','NEWNBK00092','NEWNBK00093','NEWNBK00094','NEWNBK00095','NEWNBK00096','NEWNBK00097','NEWNBK00098','NEWNBK00099','NEWNBK00100','NEWNBK00101','NEWNBK00102','NEWNBK00103','NEWNBK00104','NEWNBK00105','NEWNBK00106','NEWNBK00107','NEWNBK00108','NEWNBK00109','NEWNBK00110','NEWNBK00111','NEWNBK00112','NEWNBK00113','NEWNBK00114','NEWNBK00115','NEWNBK00116','NEWNBK00117','NEWNBK00118','NEWNBK00119','NEWNBK00120','NEWNBK00121','NEWNBK00122','NEWNBK00123','NEWNBK00124','NEWNBK00125','NEWNBK00126','NEWNBK00127','NEWNBK00128','NEWNBK00129','NEWNBK00130','NEWNBK00131','NEWNBK00132','NEWNBK00133','NEWNBK00134','NEWNBK00135','NEWNBK00136','NEWNBK00137','NEWNBK00138','NEWNBK00139','NEWNBK00140','NEWNBK00141','NEWNBK00142','NEWNBK00143','NEWNBK00144','NEWNBK00145','NEWNBK00146','NEWNBK00147','NEWNBK00148','NEWNBK00149','NEWNBK00150','NEWNBK00151','NEWNBK00152','NEWNBK00153','NEWNBK00154','NEWNBK00155','NEWNBK00156','NEWNBK00157','NEWNBK00158','NEWNBK00159');

		    	//remove offer on Dell
		    	$featured_sku = array();

		    	$allow_free = false;
		    	if(in_array($_product->getSku(), $featured_sku)){
		    		// echo " here = ".$_product->getSku();
		    		$allow_free = true;
		    	}
		    	// exit;
		    	$_stock = $_product->getStockItem()->getData();
		    	if($product_qty < $_stock['min_sale_qty'] ){
		    		//echo json_encode(array('status' => 0, 'message' => 'Product qty can not be less than '.$_stock['min_sale_qty']));
		    		//exit;
		    		$product_qty = $_stock['min_sale_qty'];
		    	}
		    	if($_stock['is_in_stock'] == 0){
		    		echo json_encode(array('status' => 0, 'message' => 'Product is out of stock, can not be added to cart'));
		    		exit;
		    	}
		    }
		    
		    $dummyAddresses = array(
							array(
								"mode" => "shipping",
								"firstname" => "testLastname",
								"lastname" => "testLastname",
								"company" => "testCompany",
								"street" => "testStreet",
								"city" => "testCity",
								"region_id" => "553",
								"postcode" => "400028",
								"country_id" => "IN",
								"telephone" => "8787878787",
								"fax" => "0123456789",
								"is_default_shipping" => 0,
								"is_default_billing" => 0
							),
							array(
								"mode" => "billing",
								"firstname" => "testFirstname",
								"lastname" => "testLastname",
								"company" => "testCompany",
								"street" => "testStreet",
								"city" => "testCity",
								"region_id" => "553",
								"postcode" => "400028",
								"country_id" => "IN",
								"telephone" => "8787878787",
								"fax" => "0123456789",
								"is_default_shipping" => 0,
								"is_default_billing" => 0
							)
						);
		    if(empty($quoteId) || $quoteId == "" || $quoteId == "null" || $quoteId == "NULL"){
		    	try {

		    		if(!empty($customerId)) {
		    			// get customer model
							$customer_model_eb = Mage::getModel('customer/customer')->load($customerId);

							// load customer model if present
							$quote = Mage::getModel('sales/quote')->loadByCustomer($customer_model_eb);

							// print_r($customer_model_eb->getData());
							$billing_id = $customer_model_eb->getDefaultBilling();
							$shipping_id = $customer_model_eb->getDefaultShipping();
							$billing = array();
							$shipping = array();
							
							if($billing_id && $shipping_id){
								$address = Mage::getModel('customer/address')->load($billing_id);
								
								$billing = array(
								"mode" => "billing",
								"customer_id" => $customer_model_eb->getEntityId(),
								"email" => $customer_model_eb->getEmail(),
								"firstname" => $address->getFirstname(),
								"lastname" => $address->getLastname(),
								"company" => $address->getCompany(),
								"street" => implode(", ", $address->getStreet()),
								"city" => $address->getCity(),
								"region_id" => $address->getRegionId(),
								"postcode" => $address->getPostcode(),
								"country_id" => $address->getCountryId(),
								"telephone" => $address->getTelephone(),
								"fax" => $address->getFax(),
								"is_default_shipping" => 0,
								"is_default_billing" => 0
								);

								$address2 = Mage::getModel('customer/address')->load($shipping_id);

								$shipping = array(
								"mode" => "shipping",
								"customer_id" => $customer_model_eb->getEntityId(),
								"email" => $customer_model_eb->getEmail(),
								"firstname" => $address2->getFirstname(),
								"lastname" => $address2->getLastname(),
								"company" => $address2->getCompany(),
								"street" => implode(", ", $address2->getStreet()),
								"city" => $address2->getCity(),
								"region_id" => $address2->getRegionId(),
								"postcode" => $address2->getPostcode(),
								"country_id" => $address2->getCountryId(),
								"telephone" => $address2->getTelephone(),
								"fax" => $address2->getFax(),
								"is_default_shipping" => 0,
								"is_default_billing" => 0
								);



							}else{
								$billing = array(
								"mode" => "billing",
								"customer_id" => $customer_model_eb->getEntityId(),
								"email" => $customer_model_eb->getEmail(),
								"firstname" => $customer_model_eb->getFirstname(),
								"lastname" => $customer_model_eb->getLastname(),
								"company" => $customer_model_eb->getCompany(),
								"street" => implode(", ", $customer_model_eb->getStreet()),
								"city" => $customer_model_eb->getCusCity(),
								"region_id" => $customer_model_eb->getCusState(),
								"postcode" => $customer_model_eb->getPincode(),
								"country_id" => $customer_model_eb->getCusCountry(),
								"telephone" => $customer_model_eb->getMobile(),
								"telephone" => $customer_model_eb->getMobile(),
								"fax" => $customer_model_eb->getMobile(),
								"is_default_shipping" => 0,
								"is_default_billing" => 0
								);
								
								$shipping = array(
								"mode" => "shipping",
								"customer_id" => $customer_model_eb->getEntityId(),
								"email" => $customer_model_eb->getEmail(),
								"firstname" => $customer_model_eb->getFirstname(),
								"lastname" => $customer_model_eb->getLastname(),
								"company" => $customer_model_eb->getCompany(),
								"street" => implode(", ", $customer_model_eb->getStreet()),
								"city" => $customer_model_eb->getCusCity(),
								"region_id" => $customer_model_eb->getCusState(),
								"postcode" => $customer_model_eb->getPincode(),
								"country_id" => $customer_model_eb->getCusCountry(),
								"telephone" => $customer_model_eb->getMobile(),
								"telephone" => $customer_model_eb->getMobile(),
								"fax" => $customer_model_eb->getMobile(),
								"is_default_shipping" => 0,
								"is_default_billing" => 0
								);
							}//end of else


								
							$dummyAddresses  = array($billing,$shipping);
							
							if($quote->getEntityId()){
								$quote_id = $quote->getEntityId();

							}else{
							// if customer quote id not present then create quote and assign it to customer
								$quote_id = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($customer_model_eb);
							}

		    		} else {
						echo json_encode(array('status' => 0, 'message' => 'Please sign in for adding product to cart'));
			        	die;

							// $cartmodel = new Mage_Checkout_Model_Cart_Api();
							// $quote_id = $cartmodel->create('default');
		    		}
			      		//$cartmodel = new Mage_Checkout_Model_Cart_Api();
			      		//$quote_id = $cartmodel->create('default');
		    		$cart_products = array();

		    		//check stock availablity
					$_product = Mage::getModel('catalog/product')->load($product_id);
		    		$_stock = $_product->getStockItem()->getData();

			    	if($product_qty < $_stock['min_sale_qty'] ){
			    		$product_qty = $_stock['min_sale_qty'];
			    	}
			    	
			    	$stock_qty = (int) $_stock['qty'];
			    	
			    	if($product_qty > $stock_qty){
			    		echo json_encode(array('status' => 0, 'message' => 'Product quantity is not available'));
			    		exit;
			    	}

			    	if($_stock['is_in_stock'] == 0){
			    		echo json_encode(array('status' => 0, 'message' => 'Product is out of stock, can not be added to cart'));
			    		exit;
			    	}

			    	//check cart info before add product to cart
			      	$quote_info_api = new Mage_Checkout_Model_Cart_Api();
		      		$quote_info = $quote_info_api->info($quote_id, $storeId);
		      		foreach ($quote_info['items'] as $key => $item) {
		      			if( $item['product_id'] == $product_id){

		      				$cart_qty = $item['qty'];

		      				$update_qty = $cart_qty + $product_qty;

		      				if($update_qty > $stock_qty){
					    		echo json_encode(array('status' => 0, 'message' => 'Product quantity is not available'));
					    		exit;
					    	}
		      			}
		      		}

		   //  		$applied_rules = $quote->getAppliedRuleIds();
		   //  		// print_r($quote->getData());
					// if(!empty($applied_rules)){
					// 	$appliedRuleIds = explode(",",$applied_rules);
					// 	$rules =  Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('rule_id' , array('in' => $appliedRuleIds));
					// 	$discount_names = '';
					// 	$simple_action = '';
					// 	foreach ($rules as $rule) {
					// 	    //do something with $rule
					// 	    // print_r($rule->getData());
					// 	    $simple_action =  $rule->getSimpleAction();
					// 	    $sku_list = $rule->getPromoSku();
					// 	    // exit;
						    
					// 	    // echo $rule->getName();
					// 	    $discount_names .= $rule->getName().'+';

					// 	    // print_r($rule);
					// 	}

					// 	if($simple_action == 'ampromo_items'){
					//     	$promo_sku = $rule->getPromoSku();
					//     	$promo_product_id = Mage::getModel("catalog/product")->getIdBySku($promo_sku);
					//     	$cart_products[] = array(
				 //                "product_id" => $promo_product_id,
				 //                "qty" => 1
				 //              );

					    	
					//     }


					// 	$discount_names = trim($discount_names,'+'); 
					// 	$cart_summary['discount_amount'] = (int) $discountTotal;
					// 	if($discount_names  !== ''){
					// 		$cart_summary['discount_label'] = 'DISCOUNT('.$discount_names.')';
					// 	}else{
					// 		$cart_summary['discount_label'] = 'DISCOUNT';
					// 	}
					// }
		    		


		    		$cart_products[] =array("product_id" => $product_id,"qty" => $product_qty);

		    		if($allow_free){
		    			// $coupon_api = new Mage_Checkout_Model_Cart_Coupon_Api();
		    			// $couponCode = 'PROMO2017';
						// $coupon_added_flag = $coupon_api->add($quote_id,$couponCode);
						//X-STANDER-RED free on iphones and dell laptops
		    			//$cart_products[] =array("product_id" => 6073,"qty" => $product_qty, "price"=>0);
		    			$rule_id = 3624; // live promo rule ide 3624
		    			Mage::getSingleton('ampromo/registry')->addPromoItem('X-STANDER-RED', $product_qty, $rule_id);
		    			Mage::log('addtocart product quote_id = '.$quote_id." promo allowed for sku = ".$_product->getSku(),null,'promo_product.log',true);
		    			
		    		}

		    		// print_r($cart_products); exit;
		    		//apply promo
			        //rpc id 10885
			        //gst id 12579

			        //tara 16864
			        //albright 6074

			       //  	if($product_id == 10885){

			    			// $arrProducts = array(
				      //         array(
				      //           "product_id" => 12579,
				      //           "qty" => 1
				      //         ),
				      //         array(
				      //           "product_id" => $product_id,
				      //           "qty" => $product_qty
				      //         )
				      //       );

			       //  	}else{

			    			// $arrProducts = array(
				      //         array(
				      //           "product_id" => $product_id,
				      //           "qty" => $product_qty
				      //         )
				      //       );
			       //  	}

		    		// $arrProducts = array(
				    //           array(
				    //             "product_id" => $product_id,
				    //             "qty" => $product_qty
				    //           )
				    //         );

						$cart_product = new Mage_Checkout_Model_Cart_Product_Api();
						// $product_added_flag = $cart_product->add($quote_id, $arrProducts);
						$product_added_flag = $cart_product->add($quote_id, $cart_products);
						if($wishlistId){
							Mage::getModel('wishlist/item')->load($wishlistId)->delete();
						}
						
						// $new_quote = Mage::getModel('sales/quote')->load($quote_id);
						// // print_r($new_quote->getData()); 
						// $new_quote->collectTotals();

						// exit;
    					// $new_quote->collectTotals();	
						// Mage_SalesRule_Model_Validator::process($quote_id);
						//adding dummy address within quote
						// $cart_customer = Mage::getModel('checkout/cart_customer_api');
						// $cart_customer->setAddresses($quote_id,$dummyAddresses);
						//end of adding dummy address within quote
						// var_dump($cart_product); exit;
			      if($product_added_flag) {
			      	// $cart_product_already_check = new Mage_Checkout_Model_Cart_Api();
			      	// $cart_product_already_check_listing = $cart_product_already_check->info( $quote_id, 1);

			      	// foreach ($$cart_product_already_check_listing["items"] as $key => $item) {
			      	// 	# code...
			      	// 	print_r($item);
			      	// }

			  //     	$_product= Mage::getModel('catalog/product')->load($product_id);

					// $coll = Mage::getResourceModel('salesrule/rule_collection')->load();
					// foreach($coll as $rule){
					//   // $rule->afterLoad(); 
					// 	if ($rule->getConditions()->validate($cart_product))  
					//     {            
					//        echo ':)<pre/>';
					//        // echo $rule->getData('discount_amount');
					//        print_r($rule->getData());
					//        exit;

					//   	}    
					// }
			        if($allow_free){
		    // 			$coupon_api = new Mage_Checkout_Model_Cart_Coupon_Api();
		    // 			$couponCode = 'PROMO2017';
						// $coupon_added_flag = $coupon_api->add($quote_id,$couponCode);
			        	//X-STANDER-RED free on iphones and dell laptops
						$sku = 'X-STANDER-RED';
						$qty = $product_qty;
				      	$rule_id = 3624;
						Mage::getSingleton('ampromo/registry')->addPromoItem($sku, $qty, $rule_id);
		    		}

			        echo json_encode(array('status' => 1, 'message' => 'Product added to cart successfully.','quoteId' => $quote_id,'product_id' => $product_id, 'qty' => $product_qty));
			        die;
			      } else {
			        echo json_encode(array('status' => 0, 'message' => 'Product was not added to cart'));
			        die;
			      }
			    } catch (Exception $e) {
			        echo json_encode(array('status' => 0, 'message' => 'Product was not added to cart '.$e->getMessage()));
			         die;
			    }
		    } else {
		    	//quote is already exists
		    	// check if product already exist in cart
		    	// echo "in else"; exit;

		    	  $cart_product_already_check = new Mage_Checkout_Model_Cart_Api();
			      $cart_product_already_check_listing = $cart_product_already_check->info( $quoteId, $storeId);
			      $cart_product_already_check_listing_blank_check = array_filter($$cart_product_already_check_listing);
			      $is_present_flag = 0;
			      $get_qty_for_present_product = 0;
			      foreach ($cart_product_already_check_listing["items"] as $key => $value) {
			      	if($value["product_id"] == $product_id) {
			      		$is_present_flag = 1;
			      		$get_qty_for_present_product = $value["qty"];
			      	}
			      }

			      if( $is_present_flag == 1 && $get_qty_for_present_product != 0) {
			      	$updated_qty = $get_qty_for_present_product+$product_qty;
			      	$arrProducts = array(
			              array(
			                "product_id" => $product_id,
			                "qty" => $updated_qty
			              ),
			            );
		      	 	$cart_product_update = new Mage_Checkout_Model_Cart_Product_Api();
				      $product_added_flag = $cart_product_update->update($quoteId, $arrProducts);

							//adding dummy address within quote
							$cart_customer = Mage::getModel('checkout/cart_customer_api');
							// $cart_customer->setAddresses($quoteId,$dummyAddresses);
							//end of adding dummy address within quote

				      if($product_added_flag){
				        echo json_encode(array('status' => 1, 'message' => 'Product added to cart successfully.','quoteId' => $quoteId,'product_id' => $product_id, 'qty' => $updated_qty));
				        exit();
				      } else {
				        echo json_encode(array('status' => 0, 'message' => 'Failed to add product'));
				        exit();
				      }
			      } else {
			      	$arrProducts = array(
			              array(
			                "product_id" => $product_id,
			                "qty" => $product_qty
			              ),
			            );

			    		$cart_product = new Mage_Checkout_Model_Cart_Product_Api();
				      $product_added_flag = $cart_product->add($quoteId, $arrProducts, $storeId);


				      if($wishlistId){
							Mage::getModel('wishlist/item')->load($wishlistId)->delete();
						}
					    //adding dummy address within quote
							$cart_customer = Mage::getModel('checkout/cart_customer_api');
							// $cart_customer->setAddresses($quoteId,$dummyAddresses);
							//end of adding dummy address within quote
							// Mage_SalesRule_Model_Validator::process();

				      if($product_added_flag) {
				        echo json_encode(array('status' => 1, 'message' => 'Product added to cart successfully.','quoteId' => $quoteId,'product_id' => $product_id, 'qty' => $product_qty));
				        exit();
				      } else {
				        echo json_encode(array('status' => 0, 'message' => 'Product was not added to cart'));
				        exit();
				      }
			      }
		    }
		}

		/**
	   * update product to cart api
	   * @param : product_id, product_quantity, quoteId
	   * @package : Neo_Mobileapi
	   * @author : deepakd
	   */
	  public function updateToCartAction()
	  {
	    $product_id = $_REQUEST["product_id"];
	    $product_qty = $_REQUEST["qty"];
	    $quoteId = $_REQUEST["quoteId"];
	    $storeId = Mage::app()->getStore()->getStoreId();
	    try {
	    	if($product_id){

	    		//removed condition on 22nd Feb 18  after offer removed on Dell and Albright Mahesh Gurav
	    		// if($product_id == 6073){
			    // 	echo json_encode(array('status' => 0, 'message' => 'Product qty can not be changed for this product '));
			    // 		exit;
			    // }

		    	$_product = Mage::getModel('catalog/product')->load($product_id);

		    	// $featured_sku = array('NEWNBK00057','NEWNBK00058','NEWNBK00059','NEWNBK00060','NEWNBK00061','NEWNBK00062','NEWNBK00063','NEWNBK00064','NEWNBK00065','NEWNBK00066','NEWNBK00067','NEWNBK00068','NEWNBK00069','NEWNBK00070','NEWNBK00071','NEWNBK00072','NEWNBK00073','NEWNBK00074','NEWNBK00075','NEWNBK00076','NEWNBK00077','NEWNBK00078','NEWNBK00079','NEWNBK00080','NEWNBK00081','NEWNBK00082','NEWNBK00083','NEWNBK00084','NEWNBK00085','NEWNBK00086','NEWNBK00087','NEWNBK00088','NEWNBK00089','NEWNBK00090','NEWNBK00091','NEWNBK00092','NEWNBK00093','NEWNBK00094','NEWNBK00095','NEWNBK00096','NEWNBK00097','NEWNBK00098','NEWNBK00099','NEWNBK00100','NEWNBK00101','NEWNBK00102','NEWNBK00103','NEWNBK00104','NEWNBK00105','NEWNBK00106','NEWNBK00107','NEWNBK00108','NEWNBK00109','NEWNBK00110','NEWNBK00111','NEWNBK00112','NEWNBK00113','NEWNBK00114','NEWNBK00115','NEWNBK00116','NEWNBK00117','NEWNBK00118','NEWNBK00119','NEWNBK00120','NEWNBK00121','NEWNBK00122','NEWNBK00123','NEWNBK00124','NEWNBK00125','NEWNBK00126','NEWNBK00127','NEWNBK00128','NEWNBK00129','NEWNBK00130','NEWNBK00131','NEWNBK00132','NEWNBK00133','NEWNBK00134','NEWNBK00135','NEWNBK00136','NEWNBK00137','NEWNBK00138','NEWNBK00139','NEWNBK00140','NEWNBK00141','NEWNBK00142','NEWNBK00143','NEWNBK00144','NEWNBK00145','NEWNBK00146','NEWNBK00147','NEWNBK00148','NEWNBK00149','NEWNBK00150','NEWNBK00151','NEWNBK00152','NEWNBK00153','NEWNBK00154','NEWNBK00155','NEWNBK00156','NEWNBK00157','NEWNBK00158','NEWNBK00159');

		    	//remove free
		    	$featured_sku = array();
		    	$allow_free = false;
		    	if(in_array($_product->getSku(), $featured_sku)){
		    		// echo " here = ".$_product->getSku();
		    		$allow_free = true;
		    	}

				$_stock = $_product->getStockItem()->getData();
		    	if($product_qty < $_stock['min_sale_qty'] ){
		    		echo json_encode(array('status' => 0, 'message' => 'Product qty can not be less than '.$_stock['min_sale_qty']));
		    		exit;
		    	}

		    	if($product_qty > $_stock['qty']){
			    		echo json_encode(array('status' => 0, 'message' => 'Product qty can not be greater than the available qty. The available product qty is '.(int) $_stock['qty']));
		    		exit;
		    	}
		    }
		    //apply promo
			        //rpc id 10885
			        //gst id 12579
		    //tara 16864
			        //albright 6074

		    	$cart_product_quantity_promo = new Mage_Checkout_Model_Cart_Api();
	      		$cart_product_quantity_promo_info = $cart_product_quantity_promo->info( $quoteId, $storeId);
	      		
  					//sum of all unchanged featured qty
	      		$free_product_qty = 0;
  				foreach ($cart_product_quantity_promo_info['items'] as $key => $item) {
	      			if(in_array($item['sku'], $featured_sku) && $item['product_id'] !== $product_id){
	      				$free_product_qty += $item['qty'];  
	      			}
		      	}

		    if($allow_free){
		    	// $free_product_qty =  intval($product_qty/$_stock['min_sale_qty']);
		    	$arrProducts = array(
	              array(
	                "product_id" => $product_id,
	                "qty" => $product_qty
	              ),
	              array(
	                "product_id" => 6073,
	                "qty" => $free_product_qty + $product_qty
	              )
	            );

		    }else if($product_id == 6073){
		    	echo json_encode(array('status' => 0, 'message' => 'Product qty can not be changed for this product '));
		    		exit;
		    }else{
		    	$arrProducts = array(
	              array(
	                "product_id" => $product_id,
	                "qty" => $product_qty
	              ),
	            );
		    }

	      	
	      $cart_product = new Mage_Checkout_Model_Cart_Product_Api();
	      $product_added_flag = $cart_product->update($quoteId, $arrProducts);

	      // get cart total quantity
	     	$cart_product_quantity = new Mage_Checkout_Model_Cart_Api();
	      $cart_product_quantity_info = $cart_product_quantity->info( $quoteId, $storeId);
	      if((int)$cart_product_quantity_info["items_qty"] >= 0)
	      {
	      	$cart_total_quantity = (int)$cart_product_quantity_info["items_qty"];
	      } else {
					$cart_total_quantity = null;
	      }

	      if($product_added_flag){
	        echo json_encode(array('status' => 1, 'message' => 'Cart updated successfully','quoteId' => $quoteId,'product_id' => $product_id, 'qty' => $product_qty, 'cart_total_qty' => $cart_total_quantity));
	      } else {
	        echo json_encode(array('status' => 0, 'message' => 'Failed to update cart'));
	      }
	    } catch (Exception $e) {
	        echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
	    }
	    die;
	  }
	  /**
	   * remove product from cart api
	   * @param : product_id, quoteId
	   * @package : Neo_Mobileapi
	   * @author : deepakd
	   */
	  public function removeFromCartAction()
	  {
	    $product_id = $_REQUEST["product_id"];
	    $quoteId = $_REQUEST["quoteId"];
	    // echo "Test";
	    // exit();
	    try {

	    	//apply promo
			        //rpc id 10885
			        //gst id 12579
	    	//tara 16864
			        //albright 6074

	    	$cart_product_quantity = new Mage_Checkout_Model_Cart_Api();
	      		$cart_product_quantity_info = $cart_product_quantity->info( $quoteId, $storeId);

	      		// print_r($cart_product_quantity_info); 

	      	
	      	// $featured_sku = array('NEWNBK00057','NEWNBK00058','NEWNBK00059','NEWNBK00060','NEWNBK00061','NEWNBK00062','NEWNBK00063','NEWNBK00064','NEWNBK00065','NEWNBK00066','NEWNBK00067','NEWNBK00068','NEWNBK00069','NEWNBK00070','NEWNBK00071','NEWNBK00072','NEWNBK00073','NEWNBK00074','NEWNBK00075','NEWNBK00076','NEWNBK00077','NEWNBK00078','NEWNBK00079','NEWNBK00080','NEWNBK00081','NEWNBK00082','NEWNBK00083','NEWNBK00084','NEWNBK00085','NEWNBK00086','NEWNBK00087','NEWNBK00088','NEWNBK00089','NEWNBK00090','NEWNBK00091','NEWNBK00092','NEWNBK00093','NEWNBK00094','NEWNBK00095','NEWNBK00096','NEWNBK00097','NEWNBK00098','NEWNBK00099','NEWNBK00100','NEWNBK00101','NEWNBK00102','NEWNBK00103','NEWNBK00104','NEWNBK00105','NEWNBK00106','NEWNBK00107','NEWNBK00108','NEWNBK00109','NEWNBK00110','NEWNBK00111','NEWNBK00112','NEWNBK00113','NEWNBK00114','NEWNBK00115','NEWNBK00116','NEWNBK00117','NEWNBK00118','NEWNBK00119','NEWNBK00120','NEWNBK00121','NEWNBK00122','NEWNBK00123','NEWNBK00124','NEWNBK00125','NEWNBK00126','NEWNBK00127','NEWNBK00128','NEWNBK00129','NEWNBK00130','NEWNBK00131','NEWNBK00132','NEWNBK00133','NEWNBK00134','NEWNBK00135','NEWNBK00136','NEWNBK00137','NEWNBK00138','NEWNBK00139','NEWNBK00140','NEWNBK00141','NEWNBK00142','NEWNBK00143','NEWNBK00144','NEWNBK00145','NEWNBK00146','NEWNBK00147','NEWNBK00148','NEWNBK00149','NEWNBK00150','NEWNBK00151','NEWNBK00152','NEWNBK00153','NEWNBK00154','NEWNBK00155','NEWNBK00156','NEWNBK00157','NEWNBK00158','NEWNBK00159');

	      	//remove free
	      	$featured_sku = array();
	      	$items = $cart_product_quantity_info['items'];
			// print_r($items);

			$allow_free = false;

			$_product = Mage::getModel('catalog/product')->load($product_id);
	    	if(in_array($_product->getSku(), $featured_sku)){
	    		// echo " here = ".$_product->getSku();
	    		$allow_free = true;
	    	}

			foreach ($items as $key => $item) {
				# code...
				// $item['qty'];
				// $item['sku'];
				if(in_array($item['sku'], $featured_sku) && $_REQUEST["product_id"] == $item['product_id']){
					// echo "items_to_delete_from_sku free ".$item['qty'];	
					$qty_to_delete = $item['qty'];
				}else if(!empty($item['product_id']) && $item['product_id'] == 6073){
					$free_pro_qty = $item['qty'];
				}

			}

			$products_to_delete = array();
			if($free_pro_qty && $qty_to_delete){
				if(($free_pro_qty-$qty_to_delete) > 0){
					//changes on 30 Dec 2017 Saturday 11:00 AM By Mahesh Gurav
					//X-STANDER-RED update qty to remaining qty after removing the parent product
					$update_free_product_qty = $free_pro_qty-$qty_to_delete;
					$arrProducts_to_update = array(
		              array(
		                "product_id" => 6073,
		                "qty" => $update_free_product_qty
		              ),
		            );

					$cart_product_update = new Mage_Checkout_Model_Cart_Product_Api();
				    $product_added_flag = $cart_product_update->update($quoteId, $arrProducts_to_update);
					Mage::log('remove product quote_id = '.$quoteId." promo qty settle = ".$update_free_product_qty,null,'promo_product.log',true);
				}else{
					//changes on Sat'30th Dec 2017 11:00 AM By Mahesh Gurav
					//free X-STANDER-RED on brand new iphones and brand new dell laptops
					$products_to_delete[] = array("product_id" => 6073);
					Mage::getSingleton('ampromo/registry')->deleteProduct('X-STANDER-RED');
					Mage::log('remove product quote_id = '.$quoteId." promo remove = X-STANDER-RED",null,'promo_product.log',true);
		            
				}	
			}
			

				      		
      		// exit;

	    	

	      	// 	$update_free_product = false;
		      // foreach ($cart_product_quantity_info['items'] as $key => $item) {
		      // 	if($item['product_id'] == 12579){
		      // 		$update_free_product = true;
		      // 	}
		      // }

		      

	      	// $arrProducts = array(
	       //        array(
	       //          "product_id" => $product_id
	       //        ),
	       //      );

	      	$products_to_delete[] = array("product_id" => $product_id);

	      $cart_product = new Mage_Checkout_Model_Cart_Product_Api();
	      // $product_remove_flag = $cart_product->remove($quoteId, $arrProducts);
	      $product_remove_flag = $cart_product->remove($quoteId, $products_to_delete);

	      // get cart total quantity
	     	$cart_product_quantity = new Mage_Checkout_Model_Cart_Api();
	      $cart_product_quantity_info = $cart_product_quantity->info( $quoteId, $storeId);
	      if((int)$cart_product_quantity_info["items_qty"] >= 0)
	      {
	      	$cart_total_quantity = (int)$cart_product_quantity_info["items_qty"];
	      } else {
					$cart_total_quantity = null;
	      }
	      if($product_remove_flag){
	        echo json_encode(array('status' => 1, 'message' => 'Product removed from cart successfully','quoteId' => $quoteId,'product_id' => $product_id, 'cart_total_qty' => $cart_total_quantity));
	      } else {
	        echo json_encode(array('status' => 0, 'message' => 'Failed to remove product from cart','quoteId' => $quoteId,'product_id' => $product_id, 'cart_total_qty' => $cart_total_quantity));
	      }
	    } catch (Exception $e) {
	        echo json_encode(array('status' => 0, 'message' => $e->getMessage(),'quoteId' => $quoteId,'product_id' => $product_id));
	    }
	    die;
	  }

	  /**
	   * get product listing from cart api
	   * @param : product_id, product_quantity, user_id
	   * @package : Neo_Mobileapi
	   * @author : deepakd
	   */
	  public function getCartListingAction()
	  {
	    $quoteId = $_REQUEST["quoteId"];
	    try {
	      $storeId = Mage::app()->getStore()->getStoreId();
	      $cart_product = new Mage_Checkout_Model_Cart_Product_Api();
	      $cart_product_listing = $cart_product->items( $quoteId, $storeId);
	      $cart_product_listing_blank_check = array_filter($cart_product_listing);


	      if (!empty($cart_product_listing_blank_check)) {
	        echo json_encode(array('status' => 1, 'message' => 'Cart listing fetched successfully', 'data' =>$cart_product_listing));
	      } else {
	        echo json_encode(array('status' => 1, 'message' => 'Cart is empty.', 'data' => ''));
	      }
	    } catch (Exception $e) {
	        echo json_encode(array('status' => 0, 'message' => 'Failed to retrieve cart listing '.$e->getMessage(), 'data' => ''));
	    }
	    die;
	  }

	  /**
	   * get cart info api
	   * @param : product_id, product_quantity, user_id
	   * @package : Neo_Mobileapi
	   * @author : deepakd
	   */
	  public function getCartInfoAction()
	  {
	    $quoteId = $_REQUEST["quoteId"];
	    //$customer = Mage::getModel('customer/customer')->load($quoteId);
	    try {
			$quote = Mage::getModel('sales/quote')->load($quoteId);
			//print_r($quote->getData());
	      $storeId = Mage::app()->getStore()->getStoreId();
	      $cart_product = new Mage_Checkout_Model_Cart_Api();
	      $cart_product_info = $cart_product->info( $quoteId, $storeId);
	      //echo "<pre>"; print_r($cart_product_info); exit;
	      $cart_product_info_blank_check = array_filter($cart_product_info['items']);
	      //removing unnecessary data
			if (empty($cart_product_info_blank_check)) {
				echo json_encode(array('status' => 1, 'message' => 'Cart is empty', 'data' => null));
				exit;
			}
	      //creating the items array
	      $items_created_array = array();
	      $items_array = $cart_product_info["items"];
	      $i=0;
	      $tax_amount = array();
	      $discountTotal = 0;
	      foreach ($items_array as $key => $value) {

	      	//Mage_SalesRule_Model_Validator
	      	//process



	      	$product = Mage::getModel('catalog/product')->load($value["product_id"]);
	      	$items_created_array[$i]["product_id"] = $value["product_id"];
	      	$items_created_array[$i]["name"] = $value["name"];
	      	$items_created_array[$i]["category_type_img"] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($value["sku"]);
	     //  	if($product->getSpecialPrice()){
		    //   	$price = (int) round(Mage::helper('tax')->getPrice($product, $product->getSpecialPrice()));
	    	// }else{
		    //   	$price = (int) round(Mage::helper('tax')->getPrice($product, $product->getPrice()));
	     //  	}

	      	// $price = $value["price_incl_tax"];
	      	$price = (int) $value["price_incl_tax"];

	      	if($price == NULL){
	      		$price = 0;
	      	}
	      	$items_created_array[$i]["price"] = $price;
	      	$items_created_array[$i]["qty"] = $value["qty"];
			array_push($tax_amount,$value['tax_amount']);
	      	$items_created_array[$i]["thumbnail"] = (string)Mage::helper('catalog/image')->init($product,'image');
					// add instock flag
					$product_qty_eb = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
					$stock_eb = $product->getStockItem();
					if ($stock_eb->getIsInStock()) {
			      /*if($product_qty_eb == 0) {
				        $items_created_array[$i]['is_instock'] = false;
				    } else {*/
				        $items_created_array[$i]['is_instock'] = true;
				    /*}*/
					} else {
					    $items_created_array[$i]['is_instock'] = false;
					}

			$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($value["product_id"]);
			$items_created_array[$i]['qty_left'] = (int)$stock->getQty();
			if($stock->getuse_config_min_sale_qty())
				$items_created_array[$i]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinQty();
			else
				$items_created_array[$i]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinSaleQty();

			
			$items_created_array[$i]['max_qty'] = (int)$stock->getMaxSaleQty();
			$discountTotal += $value['discount_amount'];
	      	//$product->getThumbnail();
	      	$i++;
	      }
	      // adding price summary
	      $cart_summary = array();
	      $applied_rules = $quote->getAppliedRuleIds();
			if(!empty($applied_rules)){
				$appliedRuleIds = explode(",",$applied_rules);
				$rules =  Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('rule_id' , array('in' => $appliedRuleIds));
				$discount_names = '';
				foreach ($rules as $rule) {
				    //do something with $rule
				    // print_r($rule->getData());
				    // echo $rule->getSimpleAction();
				    // echo $rule->getPromoSku();

				    if($rule->getSimpleAction() == 'ampromo_items'){
				    	$promo_sku = $rule->getPromoSku();
				    }
				    // echo $rule->getName();
				    $discount_names .= $rule->getName().'+';

				    // print_r($rule);
				}

				$discount_names = trim($discount_names,'+'); 
				$cart_summary['discount_amount'] = (int) $discountTotal;
				if($discount_names  !== ''){
					$cart_summary['discount_label'] = 'DISCOUNT('.$discount_names.')';
				}else{
					$cart_summary['discount_label'] = 'DISCOUNT';
				}
			}


	      $cart_summary["subtotal"] = (double)$quote->getSubtotal();
	      $cart_summary["grand_total"] = (double)$quote->getGrandTotal();
	      /*$cart_summary["tax_amount"] = $cart_product_info["cod_tax_amount"];
      	$cart_summary["advance_amount"] = $cart_product_info["cod_fee"];	*/
      	$cart_summary["tax_amount"] = array_sum($tax_amount);
      	if($cart_summary["grand_total"]>50000)
      	{
      		$cart_summary["advance_amount"] = $cart_summary["grand_total"] - 50000;
      	} else {
      		$cart_summary["advance_amount"] = 0;
      	}
      	$cart_summary['subtotal_label'] = Mage::helper('sales')->__('Subtotal:');
      	$cart_summary['grandtotal_label'] = Mage::helper('sales')->__('Grand Total:');
      	$cart_summary['tax_label'] = Mage::helper('sales')->__('GST:');

      	//get user id
      	$user_id = $quote->getCustomerId();
      	//Get Credit limit of customer from Navision
      	//$credit = Mage::getModel('customapiv6/customer')->getCreditLimit($user_id);
      	//echo $user_id,'---'.$cart_summary["grand_total"];
      	$credit = Mage::getModel('ebautomation/customer')->getCreditLimit($user_id,$cart_summary["grand_total"]); 
      	//print_r($credit);
      	//$credit['bounce'] = 0;
   		/*if($credit['balance_credit'] == 1){
   			$credit = null;
   		}*/
		 $result = array('quoteId' => $quoteId,'items' => $items_created_array,'summary' => $cart_summary, 'credit' => $credit);

	     // $result = array('quoteId' => $quoteId,'items' => $items_created_array,'summary' => $cart_summary);
	      if (!empty($cart_product_info_blank_check)) {
	        echo json_encode(array('status' => 1, 'message' => 'Cart info fetched successfully', 'data' =>$result));
	      } else {
	        echo json_encode(array('status' => 1, 'message' => 'Cart is empty.', 'data' => array()));
	      }
	    } catch (Exception $e) {
	        echo json_encode(array('status' => 0, 'message' => 'Failed to retrieve cart info '.$e->getMessage(), 'data' => null));
	    }
	    die;
	  }
	  /**
	   * @desc : add product to wishlist
	   * @package     Neo_Mobileapi
	   * @author      deepakd
	   */
	  public function addToWishlistAction()
	  {
	    $customerId = $_REQUEST['userid'];
	    $productId = $_REQUEST['productId'];
	    if($customerId != '')
	    {	    
		    $this->validateCustomer($customerId);
		    $this->validateProduct($productId);
		    $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
		    $product = Mage::getModel('catalog/product')->load($productId);

		    $buyRequest = new Varien_Object(array()); // any possible options that are configurable and you want to save with the product
		    $result = $wishlist->addNewItem($product, $buyRequest);
		    $wishlist_flag = $wishlist->save();

		    //$wishlist_container = Mage::getModel('customapiv6/customer')->getWishlistListing($customerId);

				if ($wishlist_flag) {
				  echo json_encode(array('status' => 1, 'message' => 'Product added to wishlist successfully', 'product_id' => $product->getId()));
				} else {
				  echo json_encode(array('status' => 0, 'message' => 'Product not added to wishlist.', 'data' => array()));
				}
		    die;
		}
		else
		{
	      echo json_encode(array('status' => 0, 'message' => 'Please login to add to wishlist.'));exit;
		}		    
	  }

	  /**
	   * @desc : delete product to wishlist
	   * @package     Neo_Mobileapi
	   * @author      deepakd
	   */
	  public function removeFromWishlistAction()
	  {
 		$customerId = $_REQUEST['userid'];
	    $productId  = $_REQUEST['product_id'];
	    $this->validateCustomer($customerId);
	    $this->validateProduct($productId);

      try {
      			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
						$sql = "SELECT * FROM wishlist_item WHERE product_id = $productId AND wishlist_id IN (SELECT wishlist_id FROM wishlist WHERE customer_id = $customerId)";
						$rows = $connection->fetchAll($sql);
						$wishlist_item_id = $rows[0]["wishlist_item_id"];
		      	Mage::getModel('wishlist/item')->load($wishlist_item_id)->delete();
            echo json_encode(array('status' => 1, 'message' => 'Product removed from wishlist', 'product_id' => $productId));
        } catch (Mage_Core_Exception $e) {
            echo json_encode(array('status' => 0, 'message' => '
            	An error occurred while deleting the item from wishlist: '.$e->getMessage(),data => array()));
        }
			die;
	  }

	/**
	   * @desc : remove all products to wishlist
	   * @author      bhargav rupapara
	   */

		public function clearWishListAction()
		{
			try {
				$customer_id = $_REQUEST['userid'];
				$this->validateCustomer($customer_id);
				$items = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer_id)->getItemCollection();
				$items->walk('delete');
				echo json_encode(array('status' => 1, 'message' => 'wishlist cleared'));
				exit;
			} catch(Exception $ex) {
				echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
				exit;
			}
		}

	  /*
	  * @desc : get payment methods
	  */
	  public function getPaymentMethodsAction()
	  {
	  	$quoteId = $_REQUEST['quoteId'];
			$storeId = Mage::app()->getStore()->getStoreId();
	    /*$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
	    $product = Mage::getModel('catalog/product')->load($productId);

	    $buyRequest = new Varien_Object(array()); // any possible options that are configurable and you want to save with the product
	    $result = $wishlist->addNewItem($product, $buyRequest);
	    $wishlist->save();
	    echo true;*/
	    die;
	  }

	   /**
	   * get product listing from cart api
	   * @param : quote_id
	   * @package : Neo_Mobileapi
	   * @author : deepakd
	   */
	  public function getCouponListingPageAction()
	  {
	    $quote_id = $_REQUEST["quoteId"];
	    $userId = $_REQUEST["user_id"];
	    try {
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
		          $price = (int) round(Mage::helper('tax')->getPrice($product, $product->getPrice()));
		          // $items_created_array["price"] = number_format($product->getData('price'),2,".","");
		          $items_created_array["price"] = $price;
		          $items_created_array["qty"] = $value["qty"];
							array_push($tax_amount,$value['tax_amount']);
		          $items_created_array["thumbnail"] = (string)Mage::helper('catalog/image')->init($product,'image');
		          //$items_created_result_array["items"][] = $items_created_array;
		        }

		        $items_created_result_array["payment"] = $cart_product_info["payment"];
		        if(!empty($items_created_result_array["payment"]["additional_information"])){
		        	unset($items_created_result_array["payment"]["additional_information"]);
		        }
		        // adding price summary
						$cart_summary = array();
		      	$cart_summary["subtotal"] = (double)$quote->getSubtotal();
		    		$cart_summary["grand_total"] = (double)$quote->getGrandTotal();
		      	$cart_summary["tax_amount"] = array_sum($tax_amount);
		      	//if((int)$cart_product_info["cod_fee"]){
		    		$cart_summary["advance_amount"] = $cart_product_info["cod_fee"];
		      	//}

		    	$cart_summary['subtotal_label'] = Mage::helper('sales')->__('Subtotal:');
		      	$cart_summary['grandtotal_label'] = Mage::helper('sales')->__('Grand Total:');
		      	$cart_summary['tax_label'] = Mage::helper('sales')->__('GST:');
		      		
		      	if($cart_summary["grand_total"] > 50000) {
							$cart_summary["available_amount"] = $cart_summary["grand_total"] - 50000;
						} else {
							$cart_summary["available_amount"] = 0;
						}
      			$items_created_result_array["summary"] = $cart_summary;

					echo json_encode(array('status' => 1, 'data' => $items_created_result_array));
			}catch(Exception $e){
				echo json_encode(array('status' => 0, 'message' => 'Please try again later.'));
			}
	    die;
	  }

	}
?>
