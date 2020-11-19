<?php
class Neo_Customapiv6_Model_Customer extends Mage_Core_Model_Abstract
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
          $tmpData['wishlist_item_id'] = $item->getData('wishlist_item_id');
					// get product image url
					$base_image_url = (string)Mage::helper('catalog/image')->init($product,'thumbnail');
					$tmpData["image"] = $base_image_url;
					$tmpData['name'] = $product->getData('name');
          $tmpData['new_launch'] = $product->getData('new_launch');
					$tmpData['price'] = number_format($product->getData('price'),2,".","");
          $specialprice = '';
          $specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($product->getSpecialPrice() , $product->getTaxClassId()));                  
          $tmpData['special_price'] = ($specialprice > 0)?$specialprice:null;
          $tmpData['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($product->getSku());
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
    // date_default_timezone_set("Asia/Calcutta");
    
    foreach ($order_data as $order) {
      $temp_arr["order_id"] = $order["entity_id"];
      $temp_arr["increment_id"] = $order["increment_id"];
      $temp_arr["grand_total"] = $order["grand_total"];
      $temp_arr["created_at"] = $order["created_at"];
      // $created_at = string()$order["created_at"];
      $temp_arr["created_at"] = Mage::getModel('core/date')->date('d-m-Y h:i a',strtotime($order["created_at"]));
      
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
    $can_cancel = 1; $downloadinvoice = 0;
    if (!$order->canCancel() || $order->hasInvoices() || $order->hasShipments()) {
      $can_cancel = 0;
      
    }
    if($order->hasInvoices()){
      $downloadinvoice = 1;
    }

    $increment_id = $order->getData('increment_id');
    //
    $updated_at = $order->getData('updated_at');
    $order_status = array('replace','returned_and_refunded','replaced','returned','holded','closed','canceled');
    
    $returnData = Mage::getModel("orderreturn/return")->getCollection()->addFieldToFilter('order_number',$increment_id)->getFirstItem()->getData();
    $canceledImei = rtrim($returnData['canceled_imei'],",");
    $canceled_imeis = explode(',', $canceledImei);
    $return_status = json_decode($returnData['status'],1);
    $imei_list = Mage::helper('orderreturn')->getInvoicedOrder($increment_id);
    $status_label = Mage::helper('orderreturn')->getStatusLabel();
    if(!empty($imei_list)){
      foreach ($imei_list as $imei => $value) {
        //all imei list
        $sku_imei[$value['sku']][] = $imei;
        
        //return status for imei
        if($status_label[$return_status[$imei]]){
          $sku_imei_status[$value['sku']][] = $status_label[$return_status[$imei]];
        }else{
          $sku_imei_status[$value['sku']][] = "can_return";
        }
      }
    }

		$data = array();
		$data['entity_id'] = $order->getId();
		$data['increment_id'] = $order->getData('increment_id');
		$data['created_at'] = $order->getData('created_at');
		$data['status'] = $order->getData('status');
		$data['state'] = $order->getData('state');
    $data['grand_total'] = $order->getData('grand_total');
		//$data['can_cancel'] = Mage::helper('cancelorder/customer')->canCancelApp($order);
    $can_cancel = Mage::helper('cancelorder/customer')->canCancelApp($order);
    $data['can_cancel'] = 1;
    $data['download'] = $downloadinvoice;

    $msg = '';
    if($data['status'] == 'complete'){
      $msg = 'Your order is complete';
      $data['can_cancel'] = 0;
    }else if($order->hasShipments() && $order->hasInvoices()){
      $msg = 'Your order cannot be cancelled now as it is ready for dispatch';
      $data['can_cancel'] = 0;
    }else if($order->hasInvoices()){
      $msg = 'Your order cannot be cancelled now as invoice is generated';
      $data['can_cancel'] = 0;
    }else if($data['status'] == 'canceled'){
      $msg = 'Your order is already cancelled';
      $data['can_cancel'] = 0;
    }else if(!$can_cancel){
      $data['can_cancel'] = 0;
    }else{
      $data['can_cancel'] = 1;
    }
    //$msg = '';
    // if($order->hasShipments()){
    //   $msg = 'Your order cannot be cancelled now as it is ready for dispatch';
    // }else if($order->hasInvoices()){
    //   $msg = 'Your order cannot be cancelled now as invoice is generated';
    // }if (!$order->canCancel()) {
    //   $msg = 'Your order is already cancelled';
    // }

		$order_items = array();
		foreach($order->getItemsCollection() as $item) {
			$item_data['name'] = $item->getName();
			$item_data['image'] = $item->getProduct()->getImageUrl();
      $item_data['qty'] = (int) $item->getQtyOrdered();
      // $item_data['price'] = $item->getPrice();
      $item_data['price'] = $item_data['qty'] * (int) round(Mage::helper('tax')->getPrice($item, $item->getOriginalPrice()));
      $item_data['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($item->getSku());

      if(Mage::helper('orderreturn')->canReturn($increment_id)){
      // if($data['increment_id'] == 200081509){
        $item_data['return'] = 1;
      }else{
        $item_data['return'] = 0;
      }
      
      //all imei list present then list imei with return status
      if($sku_imei[$item->getSku()]){
        $item_data['imei'] = $sku_imei[$item->getSku()];
        $item_data['return_status'] = $sku_imei_status[$item->getSku()];
      }else{
        $item_data['imei'] = array();
      }

			$order_items[] = $item_data;
		}
		$data['order_items'] = $order_items;
    //if($trackOrder) { 
		if(1) {   
			//$data['billing_address'] = $order->getBillingAddress()->getData();
			//$data['shipping_address'] = $order->getShippingAddress()->getData();
      $string = trim(preg_replace('/\s\s+/', ' ', $order->getBillingAddress()->getFormated()));
      $string1 = trim(preg_replace('/\s\s+/', ' ', $order->getShippingAddress()->getFormated()));
      $data['billing_address'] = $string;
      $data['shipping_address'] = $string1;
		}

    // $data['can_cancel_msg'] = $msg;
    $data['can_cancel_msg'] = '';
    
		return $data;
	}

	public function trackOrder($order_id)
	{
		return $this->getOrderDetails($order_id, true);
	}
  //code backup done for revising new version by Mahesh on 30102017
  public function addPushIdAndDeviceTypeBackup($device_type, $push_id, $user_id){
    

    if(!empty($push_id)) {
      $andoridDataArray = Mage::getModel('neo_notification/pushnotification')
                          ->getCollection()
                          //->addFieldToFilter('device_type',$device_type)
                          ->addFieldToFilter('device_Id',$push_id);
                          //->addFieldToFilter('user_id',$user_id);
             

      if (count($andoridDataArray)) {
          $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
          //$sql = "UPDATE `neo_notification_pushnotification` SET `device_Id` = '".$push_id."',`updated_at` = '".now()."' WHERE `device_type` = '".$device_type."' AND `user_id` = '".$user_id."'";

          //issue of duplicate device id fixed 25/10/2017 Mahesh Gurav
          $sql = "UPDATE `neo_notification_pushnotification` SET `updated_at` = '".now()."' WHERE `device_Id` = '".$push_id."' AND `device_type` = '".$device_type."' AND `user_id` = '".$user_id."'";

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

    //set device Mahesh Gurav 27 Oct 2017 discontiue on 09/11/2017
  public function addPushIdAndDeviceType2($device_type, $push_id, $user_id=0){
      if(!empty($push_id) && strlen($push_id) > 0 && strlen($device_type) > 0){
        try{
            $message = '';
            //Manage new device if push id is present
            $newDevice = Mage::getModel('neo_notification/device');
            $collectionNewdevice = $newDevice->getCollection();
            $check_newdevice = $collectionNewdevice->addFieldToFilter('device_Id', array('like' => "%".$push_id."%"));
            $exisiting_newdevices = array();
            //set list of existing device id in new device table
            if(count($check_newdevice) > 0){
              foreach ($check_newdevice as $new_devices){
                $update_new_device = $newDevice->load($new_devices->getEntityId());
                $update_new_device->setDeviceId($push_id);
                $update_new_device->setUpdatedAt(Now());
                $update_new_device->save();
                $exisiting_newdevices[] =  $new_devices->getEntityId();
              }
            }else{
              $data = array();
              $data['device_type'] = $device_type;
              $data['device_Id'] = $push_id;
              $data['user_id'] = 0;
              $data['status'] = 0;
              $data['created_at'] = NOW();
              $data['updated_at'] = NOW();
              $new_device = $newDevice->addData($data);
              $new_device->save();
              $entity_id = $new_device->getEntityId();
              $message .= "New device added id# ".$entity_id;
            }


              //manage device id in push table if user id and push id is present
            if(isset($user_id) && $user_id > 0){
                //delete new device id record if user_id found
                if(count($exisiting_newdevices) > 0){
                    $deleted = $this->deleteNewDevice($exisiting_newdevices);
                    if($deleted){
                      $message .= "Deleted new devices of user_id = ".$user_id;
                    }
                }

                //Manage push device with user ids
                $deviceModel = Mage::getModel('neo_notification/pushnotification');
                $collection = $deviceModel->getCollection();
                $check_device = $collection->addFieldToFilter('device_Id', array('like' => "%".$push_id."%"));
                $check_device = $collection->addFieldToFilter('user_id', array('eq' => $user_id));

                if(count($check_device) > 0){
                    //set updated now only on exisiting push device
                    foreach ($check_device as $device) {
                      $update_device = $deviceModel->load($device->getEntityId());
                      $update_device->setDeviceId($push_id);
                      $update_device->setUpdatedAt(Now());
                      $update_device->save();
                      $entity_id = $update_device->getEntityId();

                      $message .= "Old push device updated id# ".$entity_id;
                    }

                    //device already exists
                    return 0;
                }else{
                  
                  //data for adding in push device
                  $data = array();
                  $data['device_type'] = $device_type;
                  $data['device_Id'] = $push_id;
                  $data['user_id'] = $user_id;
                  $data['status'] = 1;
                  $data['created_at'] = NOW();
                  $data['updated_at'] = NOW();

                  //add new record for push device
                  $new_device = $deviceModel->addData($data);
                  $new_device->save();
                  $entity_id = $new_device->getEntityId();
                  $message .= "New push device added id# ".$entity_id;

                  //new device added
                  return 0;
                }

              
              
            }else{
                // user id not exists then skip this step
            }
          }catch(Exception $e){ 
            $messge .= $e->getMessage(); 
          }
        }//end of if push id present
        Mage::log($message,null,'user_device.log',true);

    }//end of function

    //function for deleting device ids once user signs in
    public function deleteNewDevice($exisiting_newdevices){
    $deleted = false;
    $newDevice = Mage::getModel('neo_notification/device');
      if(count($exisiting_newdevices) > 0){
        foreach ($exisiting_newdevices as $key => $id) {
        $deleteNewDevice = $newDevice->load($id);
        if($deleteNewDevice){
          $deleteNewDevice->delete();
          $deleteNewDevice->save();
          $deleted = true;
        }                     
        }
      }

      return $deleted;
    }

    public function getCreditLimit($user_id){
    
    $customerData = Mage::getModel('customer/customer')->load($user_id);
    //$repcode = 'dhcgdbyuwgf73897130894';//$this->getRequest()->getPost('repcode');
    $repcode = trim($customerData->getRepcode());
    $repcode = strtoupper($repcode);
    // Mage::log($repcode,null,'debug_credit.log',true);
    // Mage::log(print_r($customerData,1),null,'debug_credit.log',true);
    try{
    
      $action = 'Read';
      $params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rap="urn:microsoft-dynamics-schemas/page/rapcode_wise_credit_limit"><x:Header/><x:Body><rap:Read><rap:Repcode>'.$repcode.'</rap:Repcode></rap:Read></x:Body></x:Envelope>';
      // Mage::log($params,null,'debug_credit.log',true);

      // $apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_credit_limit_url');
      // $apiUrl = 'http://111.119.217.101:7047/DynamicsNAV71/WS/Amiable%20Electronics%20Pvt.%20Ltd./Page/Rapcode_Wise_Credit_Limit';
      $apiUrl = 'http://111.119.217.101:7047/DynamicsNAV71/WS/Amiable%20Electronics%20Pvt.%20Ltd./Page/Rapcode_Wise_Credit_Limit';
    
      $result = $this->callNavisionSOAPApi($apiUrl, $action, $params);
      // print_r($result);  
      // if($user_id == 13945){
      //   echo $params;
      //   print_r($result);
      // }
      if($result['sBody']->sFault){
        $response = $result['sBody']->sFault->faultstring;
        //$response = $customerData->getNavMapDetails()."\r\n#".Date("Y-m-d").": ".$response;//die;
        //$customerData->setnav_map_details($response)->save();
      }else{
        $total_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Credit_Limit_LCY);
        $used_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Balance_LCY);
        $overdue_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Overdue_Balance_LCY);
        $credit['total_credit'] =  $total_credit;
        $credit['utilized_credit'] =  $used_credit; //this comes in postive means customer used it & negative means customers amount is with business
        // if($used_credit < 0){
        //   $credit['balance_credit'] =  $total_credit + $used_credit;
        // }else{

          
        // }
        
        $credit['balance_credit'] =  $total_credit - $used_credit;

        $credit['overdue_credit'] =  $overdue_credit;
        $credit['total_label'] = 'Total Credit Limit';
        $credit['utilized_label'] = 'Used';
        $credit['balance_label'] = 'Balance';
        $credit['overdue_label'] = 'Overdue';

        // if($total_credit == 1 || $overdue_credit !== 0){
        //   $credit = null;
        // }

        // if($user_id == 13945){
        //   // echo $params;
        //   echo "total credit = $total_credit overdue_credit = $overdue_credit";
        //   var_dump($credit);
        //   exit;
        // }

        return $credit;
        // return null;
      }
    }catch(Exception $e){
      //$response = $customerData->getNavMapDetails()."\r\n#".Date("Y-m-d").": Exception Occured: ".$e->getMessage();
      //$customerData->setnav_map_details($response)->save();
    }
  }

  public function callNavisionSOAPApi($apiUrl = NULL, $action = NULL, $params = NULL){
        ini_set( 'soap.wsdl_cache_enabled', '0' );  
        try{
            $username = 'EBAPI';
            $password = 'eb$api*#';
            //$username = 'nav1';
            //$password = 'Win$123';
            //echo "credentials => ".$username.":".$password."<br><br>";
            $headers = array( 
                  'Method: POST', 
                  'Connection: Keep-Alive', 
                  'User-Agent: PHP-SOAP-CURL', 
                  'Content-Type: text/xml; charset=utf-8', 
                  'SOAPAction: "'.$action.'"', 
            ); 
            $ch = curl_init($apiUrl); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
            curl_setopt($ch, CURLOPT_POST, true ); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM); 
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); 
            $soapResponse = curl_exec($ch); 
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResponse);
            $xml = simplexml_load_string($response);

            $result = (array) $xml;

            // Mage::log($apiUrl,null,'debug_credit.log',true);
            // Mage::log(print_r($soapResponse,1),null,'debug_credit.log',true);
            
            return $result;
        }catch(Exception $e){
            return json_encode(array('status' => 'error', 'message' => $ex->getMessage()));
        }

    }

    //remodify insert push device
    //Mahesh Gurav
    //09 Nov 2017
    //different logs for app users sign up and install
    public function addPushIdAndDeviceType($device_type, $push_id, $user_id=0){
      if(!empty($push_id) && !empty($device_type) && $user_id == 0){
        $result = $this->addNewPushId($push_id,$device_type);
      }else if(!empty($push_id) && !empty($device_type) && $user_id !== 0){
        $result = $this->addUserPushId($push_id,$device_type, $user_id);
      }else{
        $result = 1;
      }

      return $result;
    }

    //table new_device
    public function addNewPushId($push_id,$device_type){
      $log_file = 'app_activity_'.date('dmy').'.log';
      if(!empty($push_id) && !empty($device_type)){
        $newDevice = Mage::getModel('neo_notification/device');
        $collectionNewdevice = $newDevice->getCollection();
        $check_newdevice = $collectionNewdevice->addFieldToFilter('device_Id', array('like' => "%".$push_id."%"));

        $deviceModel = Mage::getModel('neo_notification/pushnotification');
        $collection = $deviceModel->getCollection();
        $check_device = $collection->addFieldToFilter('device_Id', array('like' => "%".$push_id."%"));
        if(count($check_newdevice) == 0 && count($check_device) == 0){
          $data = array();
          $data['device_type'] = $device_type;
          $data['device_Id'] = $push_id;
          $data['user_id'] = 0;
          $data['status'] = 1;
          if(!empty($_REQUEST['version'])){
            $data['version'] = $_REQUEST['version'];
          }
          $data['created_at'] = NOW();
          $data['updated_at'] = NOW();
          $new_device = $newDevice->addData($data);
          $new_device->save();
          $entity_id = $new_device->getEntityId();
          $message = "New App Install push_id ".$push_id;
          // Mage::log($message,null,$log_file,true);
        }else{
          
          //update time for device
          if(count($check_newdevice) > 0){
            foreach ($check_newdevice as $new_device) {
              $update_newDevice = Mage::getModel('neo_notification/device')->load($new_device->getEntityId());
              if($update_newDevice){
                if(!empty($_REQUEST['version'])){
                  $update_newDevice->setversion($_REQUEST['version']);  
                }
                $update_newDevice->setUpdatedAt(NOW());
                $update_newDevice->save();
              }
            }
          }
          //update time for push device
          if(count($check_device) > 0){
            foreach ($check_device as $push_device) {
              $update_pushDevice = Mage::getModel('neo_notification/pushnotification')->load($push_device->getEntityId());
              if($update_pushDevice){
                if(!empty($_REQUEST['version'])){
                  $update_pushDevice->setversion($_REQUEST['version']);  
                }
                $update_pushDevice->setUpdatedAt(NOW());
                $update_pushDevice->save();
              }
            } 
          }

        }


      return 0;
      }
      return 1;
    }

    //table pushnotifications
    public function addUserPushId($push_id,$device_type, $user_id){
      //Manage push device with user ids
      $log_file = 'app_activity_'.date('dmy').'.log';
      if(!empty($push_id) && !empty($device_type) && !empty($user_id)){

      $deviceModel = Mage::getModel('neo_notification/pushnotification');
      $collection = $deviceModel->getCollection();
      $check_device = $collection->addFieldToFilter('device_Id', array('like' => "%".$push_id."%"));
      $check_device = $collection->addFieldToFilter('user_id', array('eq' => $user_id));

      if(count($check_device) > 0){
          //set updated now only on exisiting push device
          foreach ($check_device as $device) {
            $update_device = $deviceModel->load($device->getEntityId());
            // $update_device->setDeviceId($push_id);
            if(!empty($_REQUEST['version'])){
              $update_device->setversion($_REQUEST['version']);  
            }
            $update_device->setUpdatedAt(Now());
            $update_device->save();
            $entity_id = $update_device->getEntityId();

            // $message = "Old App User Acitvity device_id ".$push_id;
          }

      }else{
        
        //data for adding in push device
        $data = array();
        $data['device_type'] = $device_type;
        $data['device_Id'] = $push_id;
        $data['user_id'] = $user_id;
        if(!empty($_REQUEST['version'])){
          $data['version'] = $_REQUEST['version'];
        }
        $data['status'] = 1;
        $data['created_at'] = NOW();
        $data['updated_at'] = NOW();
        //add new record for push device
        $deviceModel = Mage::getModel('neo_notification/pushnotification');
        // $new_device = $deviceModel->load();
        $new_device = $deviceModel->addData($data);
        $new_device->save();

        $entity_id = $new_device->getEntityId();
        $message = "New App User Signup push_id ".$push_id;
      
      }
      //---------------- delete from new device table---------------------
      $newDevice = Mage::getModel('neo_notification/device')
      ->getCollection()
      ->addFieldToFilter('device_Id', array('like' => "%".$push_id."%")); 

      if(count($newDevice) > 0){
        foreach ($newDevice as $new_devices){
          $id =  $new_devices->getEntityId();
          $deleteNewDevice = Mage::getModel('neo_notification/device')->load($id);
            if($deleteNewDevice){
              $deleteNewDevice->delete();
              $deleteNewDevice->save();
              $deleted = true;
            }                     
          
        }
      }     
      //--------------------------------------      
        if($message){
          // Mage::log($message,null,$log_file,true);
        }
        return 0;
      }
      return 1;
    }//end of function

    //recently viewed for revamp development
    public function getRecentlyViewedByCustomer($customerId, $categoryId, $limit = 4){
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $q = "SELECT DISTINCT report_viewed_product_index.product_id, report_viewed_product_index.added_at "  .
        " FROM report_viewed_product_index " .
        " INNER JOIN catalog_category_product ON catalog_category_product.product_id = report_viewed_product_index.product_id " .
        " WHERE customer_id = " . $customerId;

        if($categoryId > 0){
            $categories = $this->_getCategories($categoryId);
            $q = $q . " AND category_id in (" . $categories . ")";
        }

        $q = $q . " ORDER BY added_at desc LIMIT " . $limit;

        return $readConnection->fetchAll($q);
    }

    //set product as viewed
    //Mahesh Gurav
    //17th Jan 2018
    public function setProductViewed($product_id, $user_id=NULL){
        if($product_id > 0 && $user_id > 0){
          
          try{
            $storeId = Mage::app()->getStore()->getStoreId();
            $added_at = Mage::getModel('core/date')->date('Y-m-d H:i:s');

            //$s = "SELECT * FROM `report_viewed_product_index` WHERE `customer_id` = '$user_id' AND `product_id` = '$product_id' ORDER BY `added_at` DESC";
            
            //$resource = Mage::getSingleton('core/resource');
            //$writeConnection = $resource->getConnection('core_write');
            // $search_query = $writeConnection->query($s);
            // $search_result = $search_query->fetchAll(); 
            
            if(count($search_result) > 0){
                //$update_count = "UPDATE `report_viewed_product_index` SET `added_at`='$added_at' WHERE `customer_id` = '$user_id' AND `product_id` = '$product_id'";
                //$update_query = $writeConnection->query($update_count); 
            }else{
                //$add_count = "INSERT INTO `report_viewed_product_index`(`customer_id`, `product_id`, `store_id`, `added_at`) VALUES ('$user_id','$product_id','$storeId','$added_at')";
                //$add_query = $writeConnection->query($add_count);
            }
            // return true;
          }catch(Exception $e){
            // Mage::log($e->getMessage(),null,'customapiv6.log',true);
            // echo $e->getMessage();
            // return false;
          }  
        }
    }

    //generate corporate store id 22 Jan 2018 Mahesh Gurav
    public function createCorporateStoreId($store_id = NULL){
      $customers_corp = Mage::getModel('customer/customer')->getCollection();
      $customers_corp->addFieldToFilter('corporate_store_id',array('eq'=>$store_id));
      $customers_corp->addFieldToFilter('group_id',array('eq'=>6));
      if(count($customers_corp) > 0){
        //create again and test
        $new_store_id = 'EBLEN_'.rand (1000,9999);
        $this->createCorporateStoreId($new_store_id);
      }else{
        //continue
        return $store_id;
      }
    }

    //generate promo code for corporate user 22 Jan 2018 Mahesh Gurav
    public function createPromoCode($promo_code = NULL){
      $gadget_promo = Mage::getModel('gadget/request')->getCollection();
      $gadget_promo->addFieldToFilter('promo_code',array('eq'=>$promo_code));
      if(count($gadget_promo) > 0){
        //create again and test
        $promo_code = 'EBLENPR_'.rand (1000,9999);
        $this->createCorporateStoreId($promo_code);
      }else{
        //continue
        return $promo_code;
      }
    }

  //FCM device update on different tables
    public function addFcmIdVersion($device_type, $push_id, $user_id=0, $version){
      if(!empty($push_id) && !empty($device_type) && $user_id == 0){
        $result = $this->addNewFcmPushId($push_id,$device_type,$version);
      }else if(!empty($push_id) && !empty($device_type) && $user_id !== 0){
        $result = $this->addUserFcmPushId($push_id,$device_type, $user_id, $version);
      }else{
        $result = 1;
      }

      return $result;
    }

    //table new_device
    public function addNewFcmPushId($push_id,$device_type,$version){
      $log_file = 'app_activity_'.date('dmy').'.log';
      if(!empty($push_id) && !empty($device_type)){
        $newDevice = Mage::getModel('neo_notification/fcmdevice');
        $collectionNewdevice = $newDevice->getCollection();
        $check_newdevice = $collectionNewdevice->addFieldToFilter('device_Id', array('like' => "%".$push_id."%"));

        $deviceModel = Mage::getModel('neo_notification/fcmpush');
        $collection = $deviceModel->getCollection();
        $check_device = $collection->addFieldToFilter('device_Id', array('like' => "%".$push_id."%"));
        if(count($check_newdevice) == 0 && count($check_device) == 0){
          $data = array();
          $data['device_type'] = $device_type;
          $data['device_Id'] = $push_id;
          $data['user_id'] = 0;
          $data['status'] = 1;
          if(!empty($_REQUEST['version'])){
            $data['version'] = $_REQUEST['version'];
          }
          $data['created_at'] = NOW();
          $data['updated_at'] = NOW();
          $new_device = $newDevice->addData($data);
          $new_device->save();
          $entity_id = $new_device->getEntityId();
          $message = "New App Install push_id ".$push_id;
          // Mage::log($message,null,$log_file,true);
        }else{
          
          //update time for device
          if(count($check_newdevice) > 0){
            foreach ($check_newdevice as $new_device) {
              $update_newDevice = Mage::getModel('neo_notification/fcmdevice')->load($new_device->getEntityId());
              if($update_newDevice){
                if(!empty($_REQUEST['version'])){
                  $update_newDevice->setversion($_REQUEST['version']);  
                }
                $update_newDevice->setUpdatedAt(NOW());
                $update_newDevice->save();
              }
            }
          }
          //update time for push device
          if(count($check_device) > 0){
            foreach ($check_device as $push_device) {
              $update_pushDevice = Mage::getModel('neo_notification/fcmpush')->load($push_device->getEntityId());
              if($update_pushDevice){
                if(!empty($_REQUEST['version'])){
                  $update_pushDevice->setversion($_REQUEST['version']);  
                }
                $update_pushDevice->setUpdatedAt(NOW());
                $update_pushDevice->save();
              }
            } 
          }

        }

        //delete from other old tables pushtable

        $pushnotificationModel = Mage::getModel('neo_notification/pushnotification')
        ->getCollection()
        ->addFieldToFilter('device_Id', array('like' => "%".$push_id."%")); 

        if(count($pushnotificationModel) > 0){
          foreach ($pushnotificationModel as $pushdevice){
            $id =  $pushdevice->getEntityId();
            $deletePushDevice = Mage::getModel('neo_notification/pushnotification')->load($id);
              if($deletePushDevice){
                $deletePushDevice->delete();
                $deletePushDevice->save();
                $deleted = true;
              }                     
            
          }
        }//count model 1


        $pushnotificationtestModel = Mage::getModel('neo_notification/pushnotificationtest')
        ->getCollection()
        ->addFieldToFilter('device_Id', array('like' => "%".$push_id."%")); 

        if(count($pushnotificationtestModel) > 0){
          foreach ($pushnotificationtestModel as $pushtestdevice){
            $id =  $pushtestdevice->getEntityId();
            $deletePushTestDevice = Mage::getModel('neo_notification/pushnotificationtest')->load($id);
              if($deletePushTestDevice){
                $deletePushTestDevice->delete();
                $deletePushTestDevice->save();
                $deleted = true;
              }                     
            
          }
        }//count model 2
      return 0;
      }
      return 1;
    }

    //table pushnotifications
    public function addUserFcmPushId($push_id,$device_type, $user_id, $version){
      //Manage push device with user ids
      $log_file = 'app_activity_'.date('dmy').'.log';
      if(!empty($push_id) && !empty($device_type) && !empty($user_id)){

      $deviceModel = Mage::getModel('neo_notification/fcmpush');
      $collection = $deviceModel->getCollection();
      $check_device = $collection->addFieldToFilter('device_Id', array('like' => "%".$push_id."%"));
      // $check_device = $collection->addFieldToFilter('user_id', array('eq' => $user_id));
      // $check_device = $collection->addFieldToFilter('version', array('eq' => $version));


      // if($_REQUEST['email'] == 'thriyaunni@gmail.com'){
      //   echo $check_device->getSelect();
      //   exit('in if');

      //   }
      if(count($check_device) > 0){

        
          //set updated now only on exisiting push device
          foreach ($check_device as $device) {
            $update_device = $deviceModel->load($device->getEntityId());
            // $update_device->setDeviceId($push_id);
            if(!empty($_REQUEST['version'])){
              $update_device->setversion($_REQUEST['version']);  
            }
            $update_device->setUserId($user_id);
            $update_device->setUpdatedAt(Now());
            $update_device->save();
            $entity_id = $update_device->getEntityId();

            // $message = "Old App User Acitvity device_id ".$push_id;
          }

      }else{
        
        
        //data for adding in push device
        $data = array();
        $data['device_type'] = $device_type;
        $data['device_Id'] = $push_id;
        $data['user_id'] = $user_id;
        if(!empty($_REQUEST['version'])){
          $data['version'] = $_REQUEST['version'];
        }
        $data['status'] = 1;
        $data['created_at'] = NOW();
        $data['updated_at'] = NOW();

        // if($_REQUEST['email'] == 'thriyaunni@gmail.com'){
        //   print_r($data);
        // exit('in else');
        // }
        //add new record for push device
        $deviceModel = Mage::getModel('neo_notification/fcmpush');
        // $new_device = $deviceModel->load();
        $new_device = $deviceModel->addData($data);
        $new_device->save();

        $entity_id = $new_device->getEntityId();
        $message = "New App User Signup push_id ".$push_id;
      
      }
      //---------------- delete from new device table---------------------
      $newDevice = Mage::getModel('neo_notification/fcmdevice')
      ->getCollection()
      ->addFieldToFilter('device_Id', array('like' => "%".$push_id."%")); 

      if(count($newDevice) > 0){
        foreach ($newDevice as $new_devices){
          $id =  $new_devices->getEntityId();
          $deleteNewDevice = Mage::getModel('neo_notification/fcmdevice')->load($id);
            if($deleteNewDevice){
              $deleteNewDevice->delete();
              $deleteNewDevice->save();
              $deleted = true;
            }                     
          
        }
      }     
      //--------------------------------------      
        if($message){
          // Mage::log($message,null,$log_file,true);
        }
        return 0;
      }
      return 1;
    }//end of function  
}
?>
