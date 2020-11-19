<?php
class Neo_Customapiv6_CartController extends Neo_Customapiv6_Controller_HttpAuthController{

		public function addToCartAction(){
			$websiteId = Mage::app()->getWebsite()->getId();
			$store = Mage::app()->getStore();

			$product_id = $_REQUEST["product_id"];
			$product_qty = $_REQUEST["qty"];
			$customerId = $_REQUEST["userid"];
			$wishlistId = $_REQUEST['wishlist_item_id'];

			

			if(isset($_REQUEST["quoteId"])){
				$quoteId = $_REQUEST["quoteId"];
			} else {
				$quoteId = NULL;
			}
			$quoteId = NULL;
			$storeId = Mage::app()->getStore()->getStoreId();
			if($product_id){
				$_product = Mage::getResourceModel('catalog/product_collection')
								->addIdFilter($product_id)
								->addFinalPrice()
								->getFirstItem();

				$finalPrices = $_product->getFinalPrice();
				// echo "<pre>"; print_r($_product->getData()); echo "</pre>"; exit;
				if($finalPrices == 0){
					echo json_encode(array('status' => 0, 'message' => 'Sorry! This Product is not available to purchase for now.'));
					exit;
				}

				$model=Mage::getModel('cataloginventory/stock_item');
				$model->getResource()->loadByProductId($model, $product_id);
				$data=$model->setOrigData();
				
				if($product_qty < $data->getMinSaleQty() ){
					$product_qty = $data->getMinSaleQty();
				}

				

				$stock_qty = (int) $data->getQty();
				if($product_qty > $stock_qty){
					echo json_encode(array('status' => 0, 'message' => 'Product quantity is not available'));
					exit;
				}

				if($data->getIsInStock() == 0){
					echo json_encode(array('status' => 0, 'message' => 'Product is out of stock, can not be added to cart'));
					exit;
				}
			}
			
			if(!empty($customerId)) {
				// get customer model
				$customer_model_eb = Mage::getModel('customer/customer')->load($customerId);

				if($product_id == '22990' && $customer_model_eb->getGroupId() !== '10' && !strpos($customer_model_eb->getEmail(),'in.ibm.com')){
					echo json_encode(array('status' => 0, 'message' => 'Your registered account is not valid to add to cart this product'));
					die;	
				}
			}else{
				echo json_encode(array('status' => 0, 'message' => 'Please sign in for adding product to cart'));
				die;
			}

			//after all validations done check for offers and apply
			$featured_products = Mage::getModel('customapiv6/cart')->getOfferProduct();
	    	$free_product = Mage::getModel('customapiv6/cart')->getFreeProduct();
	    	$free_sku = Mage::getModel('customapiv6/cart')->getFreeSku();
	    	$free_rule_id = (int) Mage::getModel('customapiv6/cart')->getFreeRule();
			$allow_free = false;
			if(in_array($product_id, $featured_products)){
	    		$allow_free = true;
	    	} 

						
			if(empty($quoteId) || $quoteId == "" || $quoteId == "null" || $quoteId == "NULL"){
				try
				{
					// load customer model if present
					$quote = Mage::getModel('sales/quote')->loadByCustomer($customer_model_eb);

					$billing_id = $customer_model_eb->getDefaultBilling();
					$shipping_id = $customer_model_eb->getDefaultShipping();
					$billing = array();
					$shipping = array();
						
					if($billing_id && $shipping_id){
						$address = Mage::getModel('customer/address')->load($billing_id);
						$address2 = Mage::getModel('customer/address')->load($shipping_id);
							
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
					$cart_products = array();

					//check cart info before add product to cart
					$quote_info_api = new Mage_Checkout_Model_Cart_Api();
					$quote_info = $quote_info_api->info($quote_id, $storeId);
					$max_qty = (int) $data->getMaxSaleQty();
					foreach ($quote_info['items'] as $key => $item) {
						if($item['product_id'] == $product_id){
							$cart_qty = $item['qty'];
							$update_qty = $cart_qty + $product_qty;
							if($update_qty > $stock_qty){
								echo json_encode(array('status' => 0, 'message' => 'Product quantity is not available'));
								exit;
							}

							if($update_qty > $max_qty){
								echo json_encode(array('status' => 0, 'message' => 'The maximum quantity allowed for purchase is '.$max_qty));
								exit;
							}
						}
					}

					$cart_products[] =array("product_id" => $product_id,"qty" => (int) $product_qty);
					$total_qty = $product_qty;
					//add free product if true for new customer quote
					if($allow_free){
						$cart_products[] =array("product_id" => $free_product,"qty" => (int) $product_qty, "price"=>0 );
						$total_qty += $product_qty; 
						$sku = $free_sku;
							$qty = $product_qty;
					      	$rule_id = $free_rule_id;
							Mage::getSingleton('ampromo/registry')->addPromoItem($sku, $qty, $rule_id);
					}


					$cart_product = new Mage_Checkout_Model_Cart_Product_Api();
					$product_added_flag = $cart_product->add($quote_id, $cart_products);

					if($wishlistId){
						Mage::getModel('wishlist/item')->load($wishlistId)->delete();
					}

					if($product_added_flag) {
						if($allow_free){
			    			$sku = $free_sku;
							$qty = $product_qty;
					      	$rule_id = $free_rule_id;
							Mage::getSingleton('ampromo/registry')->addPromoItem($sku, $qty, $rule_id);
			    		}
						echo json_encode(array('status' => 1, 'message' => 'Product added to cart successfully.','quoteId' => $quote_id,'product_id' => $product_id, 'qty' => $total_qty));
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
				$cart_product_already_check = new Mage_Checkout_Model_Cart_Api();
				$cart_product_already_check_listing = $cart_product_already_check->info( $quoteId, $storeId);
				$cart_product_already_check_listing_blank_check = array_filter($cart_product_already_check_listing);
				$is_present_flag = 0;
				$get_qty_for_present_product = 0;
				$get_qty_for_free_product = 0;

				foreach ($cart_product_already_check_listing["items"] as $key => $value) {
					if($value["product_id"] == $product_id) {
						$is_present_flag = 1;
						$get_qty_for_present_product = $value["qty"];
					}

					//check if free product is in cart and get its present qty
					if(in_array($value["product_id"], $featured_products) && $value["product_id"] !== $product_id){
						$get_qty_for_free_product += $value["qty"];
					}
				}

				if( $is_present_flag == 1 && $get_qty_for_present_product != 0) {
					$updated_qty = $get_qty_for_present_product+$product_qty;
					// $arrProducts = array(
					// 	array(
					// 		"product_id" => $product_id,
					// 		"qty" => $updated_qty
					// 	),
					// );
					$cart_products = array();
					$cart_products[] = array("product_id"=>$product_id, "qty"=>$updated_qty);
					//check for free product offer on this product
					if(in_array($product_id, $featured_products)){
						$get_qty_for_free_product += $updated_qty;	
						$cart_products[] = array("product_id"=>$free_product, "qty"=>$get_qty_for_free_product, "price"=>0);	
					}


					$cart_product_update = new Mage_Checkout_Model_Cart_Product_Api();
					// $product_added_flag = $cart_product_update->update($quoteId, $arrProducts);
					$product_added_flag = $cart_product_update->update($quoteId, $cart_products);

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
					// $arrProducts = array(
					// 	array(
					// 		"product_id" => $product_id,
					// 		"qty" => $product_qty
					// 	),
					// );
					$cart_products = array();
					$cart_products[] =array("product_id" => $product_id,"qty" => (int) $product_qty);

					//add free product if true for new customer quote
					if($allow_free){
						$free_product = Mage::getModel('customapiv6/cart')->getFreeProduct();
						$cart_products[] =array("product_id" => $free_product,"qty" => (int) $product_qty);
					}
					$cart_product = new Mage_Checkout_Model_Cart_Product_Api();
					// $product_added_flag = $cart_product->add($quoteId, $arrProducts, $storeId);
					$product_added_flag = $cart_product->add($quoteId, $cart_products, $storeId);
					if($wishlistId){
						Mage::getModel('wishlist/item')->load($wishlistId)->delete();
					}
					
					$cart_customer = Mage::getModel('checkout/cart_customer_api');

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
			$allow_free = false;
			$featured_products = Mage::getModel('customapiv6/cart')->getOfferProduct();
	    	$free_product = Mage::getModel('customapiv6/cart')->getFreeProduct();
			if(in_array($product_id, $featured_products)){
	    		$allow_free = true;
	    	}
			try {
				if($product_id){
					$model=Mage::getModel('cataloginventory/stock_item');
					$model->getResource()->loadByProductId($model, $product_id);
					$data=$model->setOrigData();
					$stock_qty = (int) $data->getQty();
					if($product_qty < $data->getMinSaleQty()){
						echo json_encode(array('status' => 0, 'message' => 'Product qty can not be less than '.$data->getMinSaleQty()));
						exit;
					}

					if($product_qty > $data->getMaxSaleQty()){
						echo json_encode(array('status' => 0, 'message' => 'Product qty can not be more than '.$data->getMaxSaleQty()));
						exit;
					}					

					if($product_qty > $stock_qty){
						echo json_encode(array('status' => 0, 'message' => 'Product qty can not be greater than the available qty. The available product qty is '.$stock_qty));
						exit;
					}
				}

				$cart_product_quantity_promo = new Mage_Checkout_Model_Cart_Api();
				$cart_product_quantity_promo_info = $cart_product_quantity_promo->info( $quoteId, $storeId);

				//sum of all unchanged featured qty
				$free_product_qty = 0;
				$free_product_in_cart = false;
				foreach ($cart_product_quantity_promo_info['items'] as $key => $item){
					if($item['product_id'] == $free_product){
						$free_product_in_cart = true;
					}
					
					if(in_array($item['product_id'], $featured_products) && $item['product_id'] !== $product_id){
						$free_product_qty += $item['qty'];  
					}
				}

				// $arrProducts = array(
				// 	array(
				// 		"product_id" => $product_id,
				// 		"qty" => $product_qty
				// 	),
				// );

				if($allow_free && $free_product_in_cart ){
			    	// $free_product_qty =  intval($product_qty/$_stock['min_sale_qty']);
			    	$arrProducts = array(
		              array(
		                "product_id" => $product_id,
		                "qty" => $product_qty
		              ),
		              array(
		                "product_id" => $free_product,
		                "qty" => $free_product_qty + $product_qty
		              )
		            );

			    }else if($product_id == $free_product){
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
				if((int)$cart_product_quantity_info["items_qty"] >= 0){
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
			$featured_products = Mage::getModel('customapiv6/cart')->getOfferProduct();
	    	$free_product = Mage::getModel('customapiv6/cart')->getFreeProduct();
	    	$free_sku = Mage::getModel('customapiv6/cart')->getFreeSku();
			$allow_free = false;
			if(in_array($product_id, $featured_products)){
	    		$allow_free = true;
	    	} 
			try {
				$cart_product_quantity = new Mage_Checkout_Model_Cart_Api();
				$cart_product_quantity_info = $cart_product_quantity->info( $quoteId, $storeId);
				$items = $cart_product_quantity_info['items'];
				$products_to_delete = array();
				$quote_item_exists = false;
				//changes for free products start
				foreach ($items as $key => $item) {
					if($item['product_id'] == $product_id){
						$quote_item_exists = true;
					}

					//qty to delete from free products is equals to parent product qty
					if(in_array($item['product_id'], $featured_products) && $_REQUEST["product_id"] == $item['product_id']){
						$qty_to_delete = $item['qty'];
					}else if(!empty($item['product_id']) && $item['product_id'] == $free_product){
						$free_pro_qty = $item['qty'];
					}
				}

			
				$cart_total_qty = (int) $cart_product_quantity_info["items_qty"]; 
				if(!$quote_item_exists){
					if($cart_total_qty == 0){
						$message_failed = "Cart is empty";
					}else{
						$message_failed = "Cart does not contain this Product";
					}

					echo json_encode(array('status' => 0, 'message' => $message_failed,'quoteId' => $quoteId,'product_id' => $product_id, 'cart_total_qty' => $cart_total_qty));
					exit();
				}

				if($free_pro_qty && $qty_to_delete){
					if(($free_pro_qty-$qty_to_delete) > 0){
						$update_free_product_qty = $free_pro_qty-$qty_to_delete;
						$arrProducts_to_update = array(
			              array(
			                "product_id" => $free_product,
			                "qty" => $update_free_product_qty
			              ),
			            );

						$cart_product_update = new Mage_Checkout_Model_Cart_Product_Api();
					    $product_added_flag = $cart_product_update->update($quoteId, $arrProducts_to_update);
						Mage::log('remove product quote_id = '.$quoteId." promo qty settle = ".$update_free_product_qty,null,'promo_product.log',true);
					}else{

						foreach ($items as $key => $item) {
							if($item["product_id"] == $free_product){
								$products_to_delete[] = array("product_id" => (int) $free_product);
								Mage::getSingleton('ampromo/registry')->deleteProduct($free_sku);
							}
						}
			            
					}	
				}
				
				if($product_id == $free_product){
					Mage::getSingleton('ampromo/registry')->deleteProduct($free_sku);
				}
				//changes for free products end



				$products_to_delete[] = array("product_id" => (int) $product_id);


				$cart_product = new Mage_Checkout_Model_Cart_Product_Api();
				$product_remove_flag = $cart_product->remove($quoteId, $products_to_delete);

				// get cart total quantity
				//$cart_product_quantity = new Mage_Checkout_Model_Cart_Api();
				$cart_product_quantity_info = $cart_product_quantity->info( $quoteId, $storeId);
				if((int)$cart_product_quantity_info["items_qty"] >= 0){
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
	      	// if($_REQUEST['user_id'] == 29080){
	      	// 	echo "<pre>";
	      	// 	print_r($value);
	      	// 	exit;
	      	// }


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
