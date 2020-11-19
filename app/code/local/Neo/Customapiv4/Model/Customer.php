<?php
class Neo_Customapiv4_Model_Customer extends Mage_Core_Model_Abstract
{
  /*
  * @desc : create new quote and assign it to customer
  * @author : deepak deshmukh
  */
  public function createQuoteAndAssignCustomer($customer)
  {
    // create new quote
    $cartmodel = new Mage_Checkout_Model_Cart_Api();
    $quoteId = $cartmodel->create('default');

    $quoteObj = Mage::getModel('sales/quote')->load($quoteId);
    // assign quote to customer
    $quoteObj->assignCustomer($customer);
    // set quote as active
    $quoteObj->setIsActive(1);
    $quoteObj->save();
    return $quoteId;
  }

  /*
  * @desc : get cart item total quantity
  * @author : deepak deshmukh
  */
  public function getCartItemQuantity($quoteId)
  {
    $storeId = Mage::app()->getStore()->getStoreId();
    // get cart total quantity
    $cart_product_quantity = new Mage_Checkout_Model_Cart_Api();
    $cart_product_quantity_info = $cart_product_quantity->info( $quoteId, $storeId);
    if((int)$cart_product_quantity_info["items_qty"] >= 0)
    {
      $cart_total_quantity = (int)$cart_product_quantity_info["items_qty"];
    } else {
      $cart_total_quantity = null;
    }
    return $cart_total_quantity;
  }
  /*
  * @desc : return customer info from customer id
  */
  public function getCustomerInfo($customerId)
  {
    $customerData = Mage::getModel('customer/customer')->load($customerId)->getData();
    return $customerData;
  }
  public function updateCustomerInfo($customerId, $customerData)
  {
    $customerData = array();
    try {
      // validate the customer data values
      $this->validateCustomerData();
      $customerData = $this->createCustomerDataArray();

      $customer = Mage::getModel('customer/customer')->load($customerid);
      $customer->setPassword($_REQUEST["password"]);
      $customer->save();
      return 1;
      /*$customermodel = new Mage_Customer_Model_Customer_Api();
      $status_flag = $customermodel->update($customerId, $customerData);
      return $status_flag;*/
    } catch (Exception $e) {
      echo json_encode(array('status'=> 0, 'message' => $e->getMessage()));
      exit;
    }
  }
  public function createDataArray($key, $value, $arrayData)
  {
    if(!empty($value)){
      $arrayData[$key] = $value;
    }
    return $arrayData;
  }
  public function createCustomerDataArray()
  {
    $customerData = array();
    $customerData = $this->createDataArray("customer_id",$_REQUEST['customer_id'],$customerData);
    $customerData = $this->createDataArray("email",$_REQUEST['email'],$customerData);
    $customerData = $this->createDataArray("firstname",$_REQUEST['firstname'],$customerData);
    $customerData = $this->createDataArray("lastname",$_REQUEST['lastname'],$customerData);
    $customerData = $this->createDataArray("password",$_REQUEST['password'],$customerData);
    $customerData = $this->createDataArray("group_id",$_REQUEST['group_id'],$customerData);
    $customerData = $this->createDataArray("prefix",$_REQUEST['prefix'],$customerData);
    $customerData = $this->createDataArray("suffix",$_REQUEST['suffix'],$customerData);
    $customerData = $this->createDataArray("dob",$_REQUEST['dob'],$customerData);
    $customerData = $this->createDataArray("taxvat",$_REQUEST['taxvat'],$customerData);
    $customerData = $this->createDataArray("gender",$_REQUEST['gender'],$customerData);
    $customerData = $this->createDataArray("middlename",$_REQUEST['middlename'],$customerData);
    return $customerData;
  }
  public function validateCustomerData(){
    if(!empty($_POST['prefix']) && !in_array($_POST['prefix'],array('Mr.', 'Ms.'))) {
      echo json_encode(array('status' => 0, 'message' => 'Prefix must be either Mr. or Ms.'));
      exit;
    }
    if(!empty($_POST['firstname']) && !preg_match("/^[a-zA-Z]*$/",$_POST['firstname'])) {
      echo json_encode(array('status' => 0, 'message' => 'Please provide valid first name.'));
      exit;
    }
    if(!empty($_POST['lastname']) && !preg_match("/^[a-zA-Z]*$/",$_POST['lastname'])) {
      echo json_encode(array('status' => 0, 'message' => 'Please provide valid last name.'));
      exit;
    }
    if(!empty($_POST['mobile']) && !preg_match("/^[0-9]{5,7}$/",$_POST['mobile'])) {
      echo json_encode(array('status' => 0, 'message' => 'Please provide valid Mobile.'));
      exit;
    }
    if(!empty($_POST['email']) && !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
      echo json_encode(array('status' => 0, 'message' => 'Please provide valid Email.'));
      exit;
    }
    if(!empty($_POST['password']) && strlen($_POST['password']) < 6) {
      echo json_encode(array('status' => 0, 'message' => 'Your password must be atleast 6 character long.'));
      exit;
    }
  }

  public function createCustomerAddress()
  {
    $addressData = $this->getCreateAddressDataArray();
    try {
      $customermodel = new Mage_Customer_Model_Address_Api();
      $addressId = $customermodel->create($_REQUEST["customerId"], $addressData);
      return $addressId;
    } catch (Exception $e) {
      echo json_encode(array('status'=> 0, 'message' => $e->getMessage()));
      exit;
    }
  }
  public function getCreateAddressDataArray()
  {
    $addressData = array();
    $addressData['customerId'] = (isset($_REQUEST["customerId"])) ? $_REQUEST["customerId"] : '';
    $addressData['company'] = (isset($_REQUEST["company"])) ? $_REQUEST["company"] : '';
    $addressData['fax'] = (isset($_REQUEST["fax"])) ?  $_REQUEST["fax"] : "";
    $addressData['firstname'] = (isset($_REQUEST["firstname"])) ? $_REQUEST["firstname"] : "";
    $addressData['middlename'] = (isset($_REQUEST["middlename"])) ? $_REQUEST["middlename"] : "";
    $addressData['lastname'] = (isset($_REQUEST["lastname"])) ? $_REQUEST["lastname"] : "";
    $street_line_1 = (isset($_REQUEST["street_line_1"])) ? $_REQUEST["street_line_1"] : "";
    $street_line_2 = (isset($_REQUEST["street_line_2"])) ? $_REQUEST["street_line_2"] : "";
    $addressData['street'] = array($street_line_1, $street_line_2);
    $addressData['city'] = (isset($_REQUEST["city"])) ? $_REQUEST["city"] : "";
    $addressData['country_id'] = (isset($_REQUEST["country_id"])) ? $_REQUEST["country_id"] : "";
    $addressData['region'] = (isset($_REQUEST["region"])) ? $_REQUEST["region"] : "";
    $addressData['region_id'] = (isset($_REQUEST["region_id"])) ? $_REQUEST["region_id"] : "";
    $addressData['postcode'] = (isset($_REQUEST["postcode"])) ? $_REQUEST["postcode"] : "";
    $addressData['telephone'] = (isset($_REQUEST["telephone"])) ? $_REQUEST["telephone"] : "";
    $addressData['is_default_billing'] = (isset($_REQUEST["is_default_billing"])) ?  $_REQUEST["is_default_billing"] : false;
    $addressData['is_default_shipping'] = (isset($_REQUEST["is_default_shipping"])) ? $_REQUEST["is_default_shipping"] : false;
    $addressData['prefix'] = (isset($_REQUEST["prefix"])) ? $_REQUEST["prefix"] : "";
    $addressData['updated_at'] = (isset($_REQUEST["updated_at"])) ? Mage::getModel('core/date')->date('Y-m-d H:i:s') : "";
    $addressData['created_at'] = (isset($_REQUEST["created_at"])) ? Mage::getModel('core/date')->date('Y-m-d H:i:s') : "";
    return $addressData;
  }

  public function updateCustomerAddress()
  {
    $addressData = $this->getUpdateAddressDataArray();
    $storeId = Mage::app()->getStore()->getStoreId();
    try {
      $customermodel = new Mage_Customer_Model_Address_Api();
      $status_flag = $customermodel->update($_REQUEST["addressId"], $addressData);
    } catch (Exception $e) {
      echo json_encode(array('status'=> 0, 'message' => $e->getMessage()));
    }
  }

  public function getUpdateAddressDataArray()
  {
    $addressData = array();

    $addressData['addressId'] = (isset($_REQUEST["addressId"])) ? $_REQUEST["addressId"] : "";
    $addressData['company'] = (isset($_REQUEST["company"])) ? $_REQUEST["company"] : '';
    $addressData['fax'] = (isset($_REQUEST["fax"])) ?  $_REQUEST["fax"] : "";
    $addressData['firstname'] = (isset($_REQUEST["firstname"])) ? $_REQUEST["firstname"] : "";
    $addressData['middlename'] = (isset($_REQUEST["middlename"])) ? $_REQUEST["middlename"] : "";
    $addressData['lastname'] = (isset($_REQUEST["lastname"])) ? $_REQUEST["lastname"] : "";
    $street_line_1 = (isset($_REQUEST["street_line_1"])) ? $_REQUEST["street_line_1"] : "";
    $street_line_2 = (isset($_REQUEST["street_line_2"])) ? $_REQUEST["street_line_2"] : "";
    $addressData['street'] = array($street_line_1, $street_line_2);
    $addressData['city'] = (isset($_REQUEST["city"])) ? $_REQUEST["city"] : "";
    $addressData['country_id'] = (isset($_REQUEST["country_id"])) ? $_REQUEST["country_id"] : "";
    $addressData['region'] = (isset($_REQUEST["region"])) ? $_REQUEST["region"] : "";
    $addressData['region_id'] = (isset($_REQUEST["region_id"])) ? $_REQUEST["region_id"] : "";
    $addressData['postcode'] = (isset($_REQUEST["postcode"])) ? $_REQUEST["postcode"] : "";
    $addressData['telephone'] = (isset($_REQUEST["telephone"])) ? $_REQUEST["telephone"] : "";
    $addressData['is_default_billing'] = (isset($_REQUEST["is_default_billing"])) ?  $_REQUEST["is_default_billing"] : false;
    $addressData['is_default_shipping'] = (isset($_REQUEST["is_default_shipping"])) ? $_REQUEST["is_default_shipping"] : false;
    $addressData['prefix'] = (isset($_REQUEST["prefix"])) ? $_REQUEST["prefix"] : "";
    $addressData['updated_at'] = (isset($_REQUEST["updated_at"])) ? Mage::getModel('core/date')->date('Y-m-d H:i:s') : "";
    $addressData['created_at'] = (isset($_REQUEST["created_at"])) ? Mage::getModel('core/date')->date('Y-m-d H:i:s') : "";
    return $addressData;
  }
/* @param : customerId
* @desc : get user wishlist function (not api)
* @package : Neo_Mobileapi
* @author : deepakd
*/
	public function getWishlistListing($customerId)
	{
		$wishList = Mage::getSingleton('wishlist/wishlist')->loadByCustomer($customerId);
		$wishListItemCollection = $wishList->getItemCollection();

		try {
		  if (count($wishListItemCollection)) {
			$arrProductIds = array();
			foreach ($wishListItemCollection as $item) {
					/* @var $product Mage_Catalog_Model_Product */
					$product = $item->getProduct();

					$tmpData["product_id"] = $product->getId();
					// get product image url
					$base_image_url = (string)Mage::helper('catalog/image')->init($product,'thumbnail');
					$tmpData["image"] = $base_image_url;
					$tmpData['name'] = $product->getData('name');
					$tmpData['price'] = number_format($product->getData('price'),2,".","");
          $tmpData['special_price'] = $product->getSpecialPrice();
          // add instock flag
          $product_qty_eb = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
          $stock_eb = $product->getStockItem();
          if ($stock_eb->getIsInStock()) {
            if($product_qty_eb == 0) {
                $tmpData['is_instock'] = false;
            } else {
                $tmpData['is_instock'] = true;
            }
          } else {
              $tmpData['is_instock'] = false;
          }
					$arrProduct[] = $tmpData;
				}
				return $arrProduct;
			} else {
				echo json_encode(array('status' => 1, 'message' => 'Your wishlist is empty', 'data' => array()));
				die;
			}
		} catch (Exception $e) {
			echo json_encode(array('status' => 0, 'message' => 'Product not added to wishlist.'.$e->getMessage()));
			die;
		}
		return false;
	}

	public function getCustomerOrders($customer_id, $limit = 0)
	{
		$orders = Mage::getModel('sales/order')
			->getCollection()
			->addFieldToSelect(array('increment_id','grand_total','created_at','status'))
			->addFieldToFilter('customer_id',$customer_id)
      ->addAttributeToSort('created_at', 'desc');

		if($limit > 0) {
			$orders->getSelect()->limit($limit);
		}
		return $orders->getData();
	}
  /*
  * @desc : retrive the orders details and return it to Myorders api
  * @author : deepakd
  */
	public function getCustomerMyOrders($customer_id, $page_no = 1, $page_size = 10)
	{
		$orders = Mage::getModel('sales/order')
			->getCollection()
			->addFieldToSelect(array('increment_id','grand_total','created_at','state','status'))
			->addFieldToFilter('customer_id',$customer_id)
      ->setOrder('created_at','DESC')
      ->setCurPage($page_no)
      ->setPageSize($page_size);

		/*if($limit > 0) {
			$orders->getSelect()->limit($limit);
		}*/
		$order_data = $orders->getData();

		foreach ($order_data as $order) {
			$temp_arr["order_id"] = $order["entity_id"];
			$temp_arr["increment_id"] = $order["increment_id"];
			$temp_arr["grand_total"] = $order["grand_total"];
			$temp_arr["created_at"] = $order["created_at"];
			$temp_arr["state"] = $order["state"];
			$temp_arr["status"] = $order["status"];
			$current_order_data = Mage::getModel('sales/order')->load($order["entity_id"]);
			$ordered_items = $current_order_data->getAllItems();
			foreach($ordered_items as $item)
			{
				$product = Mage::getModel('catalog/product')->load($item->getItemId());
				$temp_arr1["product_id"] = $product->getId();
				$base_image_url = (string)Mage::helper('catalog/image')->init($product,'image');
				$temp_arr1["image"] = $base_image_url;
				$temp_arr1["qty"] = $item->getQtyOrdered(); //ordered qty of item
				$temp_arr1["product_name"] = $item->getName();     // etc.
				$temp_arr["order_items"][] = $temp_arr1;
			}
			$result[] = $temp_arr;
		}
		return $result;
	}

	public function getOrderDetails($order_id, $trackOrder = false)
	{
		$order = Mage::getModel('sales/order')->load($order_id);
		$order_id = $order->getId();
		if($order_id < 1) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid order id'));
			exit;
		}
		$data = array();
		$data['entity_id'] = $order->getId();
		$data['increment_id'] = $order->getData('increment_id');
		$data['created_at'] = $order->getData('created_at');
		$data['status'] = $order->getData('status');
		$data['state'] = $order->getData('state');
		$data['grand_total'] = $order->getData('grand_total');

		$order_items = array();
		foreach($order->getItemsCollection() as $item) {
			$item_data['name'] = $item->getName();
			$item_data['image'] = $item->getProduct()->getImageUrl();
			$item_data['price'] = $item->getPrice();
			$order_items[] = $item_data;
		}
		$data['order_items'] = $order_items;
		if($trackOrder) {
			$data['billing_address'] = $order->getBillingAddress()->getData();
			$data['shipping_address'] = $order->getShippingAddress()->getData();
		}
		return $data;
	}

	public function trackOrder($order_id)
	{
		return $this->getOrderDetails($order_id, true);
	}
  public function addPushIdAndDeviceType($device_type, $push_id, $user_id)
  {
    if(!empty($push_id)) {
      $andoridDataArray = Mage::getModel('neo_notification/pushnotification')
                          ->getCollection()
                          ->addFieldToFilter('device_type',$device_type)
                          //->addFieldToFilter('device_Id',$push_id)
                          ->addFieldToFilter('user_id',$user_id);
      if (count($andoridDataArray)) {
          $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
          $sql = "UPDATE `neo_notification_pushnotification` SET `device_Id` = '".$push_id."',`updated_at` = '".now()."' WHERE `device_type` = '".$device_type."' AND `user_id` = '".$user_id."'";
          $connection->query($sql);
          return 0;
      } else {
          /* $andoridDataArray = Mage::getModel('neo_notification/pushnotification');
          $data = array();
          $data['device_type'] = $device_type;
          $data['device_Id'] = $push_id;
          $data['user_id'] = $user_id;
          $data['status'] = 1;
          $data['created_at'] = now();
          $data['updated_at'] = now();
          $andoridDataArray->addData($data);*/
          $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
          $sql = "INSERT INTO `neo_notification_pushnotification` (`entity_id`,`device_type`, `device_Id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES ('','".$device_type."', '".$push_id."', $user_id, '1', '".now()."', '".now()."')";
          $connection->query($sql);
          return 1;
      }
    }
  }
}
?>
