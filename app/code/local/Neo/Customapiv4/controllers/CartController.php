<?php
	class Neo_Customapiv4_CartController extends Neo_Customapiv4_Controller_HttpAuthController
	{
		public function addToCartAction()
		{
			if(isset($_REQUEST["quoteId"]))
			{
				$quoteId = $_REQUEST["quoteId"];
			} else {
				$quoteId = NULL;
			}
		    $product_id = $_REQUEST["product_id"];
		    $product_qty = $_REQUEST["qty"];
		    $customerId = $_REQUEST["userid"];
		    $storeId = Mage::app()->getStore()->getStoreId();
		    $dummyAddresses = array(
							array(
								"mode" => "shipping",
								"firstname" => "testLastname",
								"lastname" => "testLastname",
								//"company" => "testCompany",
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
								//"company" => "testCompany",
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
							$quoteId = $quote->getId();

							// if customer quote id not present then create quote and assign it to customer
							if(empty($quoteId)){
								$quote_id = Mage::getModel('customapiv4/customer')->createQuoteAndAssignCustomer($customer_model_eb);
							}
		    		} else {
							$cartmodel = new Mage_Checkout_Model_Cart_Api();
							$quote_id = $cartmodel->create('default');
		    		}

			      $cartmodel = new Mage_Checkout_Model_Cart_Api();
			      $quote_id = $cartmodel->create('default');
			      $arrProducts = array(
			              array(
			                "product_id" => $product_id,
			                "qty" => $product_qty
			              ),
			            );
						$cart_product = new Mage_Checkout_Model_Cart_Product_Api();
						$product_added_flag = $cart_product->add($quote_id, $arrProducts);

						//adding dummy address within quote
						$cart_customer = Mage::getModel('checkout/cart_customer_api');
						$cart_customer->setAddresses($quote_id,$dummyAddresses);
						//end of adding dummy address within quote

			      if($product_added_flag) {
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

		    	// check if product already exist in cart
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
							$cart_customer->setAddresses($quoteId,$dummyAddresses);
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

					    //adding dummy address within quote
							$cart_customer = Mage::getModel('checkout/cart_customer_api');
							$cart_customer->setAddresses($quoteId,$dummyAddresses);
							//end of adding dummy address within quote

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

	    try {
	      $arrProducts = array(
	              array(
	                "product_id" => $product_id,
	                "qty" => $product_qty
	              ),
	            );
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
	    try {
	      $arrProducts = array(
	              array(
	                "product_id" => $product_id
	              ),
	            );
	      $cart_product = new Mage_Checkout_Model_Cart_Product_Api();
	      $product_remove_flag = $cart_product->remove($quoteId, $arrProducts);

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
	    $customer = Mage::getModel('customer/customer')->load($quoteId);
	    try {
			$quote = Mage::getModel('sales/quote')->load($quoteId);
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
	      foreach ($items_array as $key => $value) {
	      	$product = Mage::getModel('catalog/product')->load($value["product_id"]);
	      	$items_created_array[$i]["product_id"] = $value["product_id"];
	      	$items_created_array[$i]["name"] = $value["name"];
	      	$items_created_array[$i]["price"] = number_format($product->getData('price'),2,".","");
	      	$items_created_array[$i]["qty"] = $value["qty"];
			array_push($tax_amount,$value['tax_amount']);
	      	$items_created_array[$i]["thumbnail"] = (string)Mage::helper('catalog/image')->init($product,'image');
					// add instock flag
					$product_qty_eb = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
					$stock_eb = $product->getStockItem();
					if ($stock_eb->getIsInStock()) {
			      if($product_qty_eb == 0) {
				        $items_created_array[$i]['is_instock'] = false;
				    } else {
				        $items_created_array[$i]['is_instock'] = true;
				    }
					} else {
					    $items_created_array[$i]['is_instock'] = false;
					}
	      	//$product->getThumbnail();
	      	$i++;
	      }
	      // adding price summary
	      $cart_summary = array();
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

	      $result = array('quoteId' => $quoteId,'items' => $items_created_array,'summary' => $cart_summary);
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
	    $this->validateCustomer($customerId);
	    $this->validateProduct($productId);
	    $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
	    $product = Mage::getModel('catalog/product')->load($productId);

	    $buyRequest = new Varien_Object(array()); // any possible options that are configurable and you want to save with the product
	    $result = $wishlist->addNewItem($product, $buyRequest);
	    $wishlist_flag = $wishlist->save();

	    //$wishlist_container = Mage::getModel('customapiv4/customer')->getWishlistListing($customerId);

			if ($wishlist_flag) {
			  echo json_encode(array('status' => 1, 'message' => 'Product added to wishlist successfully', 'product_id' => $product->getId()));
			} else {
			  echo json_encode(array('status' => 0, 'message' => 'Product not added to wishlist.', 'data' => array()));
			}
	    die;
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

					echo json_encode(array('status' => 1, 'data' => $items_created_result_array));
			}catch(Exception $e){
				echo json_encode(array('status' => 0, 'message' => 'Please try again later.'));
			}
	    die;
	  }

	}
?>
