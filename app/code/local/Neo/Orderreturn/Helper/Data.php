<?php
class Neo_Orderreturn_Helper_Data extends Mage_Core_Helper_Abstract
{

	protected $status = array(1=>"Pending", 2=>"Approved", 3=>"Rejected", 4=>"In Pickup Process", 5=>"In Payment Process", 6=>"Complete");
	protected $return_action = array(1=>"Replace", 2=>"Refund");
	protected $reason = array(1=>"Malfunctioning in Product", 2=>"Damage in Product");

	const XML_PATH_EMAIL_RECIPIENT  = 'return/return/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'return/return/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'return/return/email_template';
    //const XML_PATH_EMAIL_TEMPLATE   = 'return/return/email_orderreturn';


	public function encrypt($data){
	    return base64_encode($data);
	}

	public function decrypt($data){
	    return base64_decode($data);
	}

	public function getOrderImei($orderId)
	{
		return Mage::getModel('sales/order')->loadByIncrementId($orderId);
	}

	public function returnData($orderId)
	{
		$returnData = Mage::getModel("orderreturn/return")->getCollection()->addFieldToFilter('order_number',$orderId)->getFirstItem()->getData();

		$canceledImei = explode(',', $returnData['canceled_imei']);
		$reason = explode(',', $returnData['reason']);

		$returnArray = array();

		for($i=0;$i<count($canceledImei);$i++)
		{
			if($canceledImei[$i]){
				$returnArray[$canceledImei[$i]] = $reason[$i];
			}
		}
     
		return $returnArray;
	}

	public function getImeiList($orderId){
		$bigArray = array();
		
		if(!empty($orderId)){
			$returnData = Mage::getModel("orderreturn/return")->getCollection()->addFieldToFilter('order_number',$orderId)->getFirstItem()->getData();
			// print_r($returnData); die();
			$bigArray['order_id'] = $returnData['order_number'];
			$bigArray['bank_id'] = $returnData['bank_id'];
			$reason = json_decode($returnData['reason'],1);
			$return_action = json_decode($returnData['return_action'],1);
			$status = json_decode($returnData['status'],1);
			$utr_filepath = json_decode($returnData['utr_filepath'],1);
			$replace_increment_id = json_decode($returnData['replace_increment_id'],1);
			$product_img = json_decode($returnData['product_img'],1);
			$remarks = json_decode($returnData['remarks'],1);
			$docket_no = json_decode($returnData['docket_no'],1);
			$pickup_address = $returnData['pickup_address'];
			$pay_reference_no = json_decode($returnData['pay_reference_no'],1);
			$payment_done = json_decode($returnData['payment_done'],1);
			$sales_entry_no = json_decode($returnData['sales_entry_no'],1);
			$sales_entry_pass = json_decode($returnData['sales_entry_pass'],1);

			$canceledImei = rtrim($returnData['canceled_imei'],",");
			$canceled_imeis = explode(',', $canceledImei);
			
			$imei_list = array(); 
			foreach ($canceled_imeis as $key => $imei) {
				$imei_list[$imei]['reason'] = $this->reason[$reason[$imei]];
				$imei_list[$imei]['return_action'] = $this->return_action[$return_action[$imei]];
				$imei_list[$imei]['status'] = $this->status[$status[$imei]];
				$imei_list[$imei]['utr_filepath'] = $utr_filepath[$imei];
				$imei_list[$imei]['return_increment_id'] = $replace_increment_id[$imei];
				$imei_list[$imei]['product_img'] = $product_img[$imei];
				$imei_list[$imei]['remarks'] = $remarks[$imei];
				$imei_list[$imei]['docket_no'] = $docket_no[$imei];
				$imei_list[$imei]['pay_reference_no'] = $pay_reference_no[$imei];
				$imei_list[$imei]['payment_done'] = $payment_done[$imei];
				$imei_list[$imei]['sales_entry_no'] = $sales_entry_no[$imei];
				$imei_list[$imei]['sales_entry_pass'] = $sales_entry_pass[$imei];
			}
			$bigArray['pickup_address'] = $pickup_address;
			$bigArray['imei_list'] = $imei_list;
		}

		return $bigArray;
	}


	public function getCustomerAddress($request_id){


		$orderreturn = Mage::getModel('orderreturn/return')->load($request_id);

		$increment_id = $orderreturn->getOrderNumber();

		$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
		//$billingAddress = $order->getBillingAddress();
		$shippingAddress = $order->getShippingAddress();

		if($shippingAddress){
		$street = $shippingAddress->getStreet();

		$street_str = '';
		foreach ($street as $key => $value) {
			$street_str .= $value.", ";
		}

		$sub_address = rtrim($street_str,", ");

		$city = $shippingAddress->getCity();
		$state = $shippingAddress->getRegion();
		$postcocde = $shippingAddress->getPostcode();

		$address = $sub_address.", ".$city.", ".$state.", ".$postcocde;

		return $address;
		}
	}

	public function getAdminImeiList($request_id){

		if(!empty($request_id)){

			$order_number = $this->getOrderNumber($request_id);
			$bigArray = $this->getImeiList($order_number);

			return $bigArray;
		}

		return false;
	}


	public function getOrderNumber($request_id){

		if(!empty($request_id)){
			$orderreturn = Mage::getModel('orderreturn/return')->load($request_id);
			$increment_id = $orderreturn->getOrderNumber();

			return $increment_id;
		}

		return false;
	}

	public function getStatusLabel(){
		return $this->status;
	}
	
	public function getReturn(){
		return $this->return_action;
	}

	//get email content for request
	public function requestEmailTable($returnData){
		$list_imei =  $returnData['canceled_imei'];
		$reason_array = json_decode($returnData['reason'],1);
		$return_action_array = json_decode($returnData['return_action'],1);
		$status_array = json_decode($returnData['status'],1);
		$status_label = $this->status;
		$reason_label = $this->reason;
		$imei_array = explode(",", rtrim($list_imei,","));
		$table = '';
		$sn = 1;
		$order = Mage::getModel('sales/order')->loadByIncrementId($returnData['order_number']);	
		foreach ($imei_array as $key => $imei) {
			foreach ($order->getInvoiceCollection() as $inv)
          	{
	            $items = $inv->getItemsCollection()->getData();
	            foreach ($items as $item)
	            {
	              	// $imeiNumbers = explode(' ', $item['serial']);
	              	 $serial = (string) $item['serial'];
            	$imeiNumbers = preg_split('/\s+/', $serial);
	              	foreach ($imeiNumbers as $imeio)
	              	{
		                if($imei == $imeio){
		                  $proName = $item['name'];
		                  break;
		                }
	              	}
	            }
          	}	
			
			$reason = $reason_label[$reason_array[$imei]];
			// $return_action = $return_action_array[$imei];
			$status = $status_label[$status_array[$imei]];	


		    $directory = Mage::getBaseDir('media') . DS . 'order_return' . DS . $imei . DS;
		    $files = scandir ($directory);
		    $firstFile = Mage::getBaseUrl('media') .  'order_return' . DS . $imei . DS . $files[2];
			
			$table .= '<tr style="background:#FFFFFF;">';
	        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">'.$sn.'</td>';
	        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">'.$proName.'</td>';
	        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">'.$imei.'</td>';
	        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">'.$reason.'</td>';
	          
	        // $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">';
	        // if($files[2]){
	        // 	$table .= '<a href="'.$firstFile.'" target="_blank"><img style="width:40px;" src="'.$firstFile.'"></a>';
	        // }else{
	        // 	$table .= '<img style="width:40px;" src="http://www.electronicsbazaar.com/media/emailcampaigns/return-order/images/logo1.png">';
	        // }
	        // $table .= '</td>';

	        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; padding:5px 10px; line-height:18px;">'.$status.'</td>';

	        $table .= '</tr>';
	        $sn++;
		}

		return $table;
	}

	public function sendAdminEmail($returnData){
		$postData = array();
		$postData['ordno'] = $order_id = $returnData['order_number'];
		$postData['table'] = $this->requestEmailTable($returnData);
		try {
			$postObject = new Varien_Object();  
      		$postObject->setData($postData); 

        	$translate = Mage::getSingleton('core/translate');
	        /* @var $translate Mage_Core_Model_Translate */
	    	$translate->setTranslateInline(false);

	    	$mailTemplate = Mage::getModel('core/email_template');
	      	/* @var $mailTemplate Mage_Core_Model_Email_Template */
	    	$recipients = explode(",",Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT)); 
	    	$mailTemplate->setDesignConfig(array('area' => 'frontend'))
	          //->setReplyTo($customer_email)
	          ->sendTransactional(
	              Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
	              Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
	              $recipients,
	              null,
	              array('data' => $postObject)
	          );

	    	$translate->setTranslateInline(true);

		}catch (Exception $e) {
				echo $e->getMessage(); die();
		}
	}

	public function sendCustomerEmail($returnData, $customerEmail=false, $message){
		$postData = array();
		$order_id = $returnData['order_number'];

		$orderModel = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		$customer_email = $orderModel->getCustomerEmail();
		$customerName = $orderModel->getCustomerName();

		if($customerEmail){
			try {
				$postData['salutation'] = $customerName;
				$postData['message'] = $message;
				$postData['table'] = $this->requestEmailTable($returnData);
				$postData['ordno'] = $order_id;
				$varObject = new Varien_Object();  
	      		$varObject->setData($postData); 
	        	$translate_customer = Mage::getSingleton('core/translate');
		        /* @var $translate Mage_Core_Model_Translate */
		    	$translate_customer->setTranslateInline(false);

		    	$mailTemplate_customer = Mage::getModel('core/email_template');
		      	/* @var $mailTemplate Mage_Core_Model_Email_Template */
		    	//$recipients = explode(",",Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT)); 
		    	$mailTemplate_customer->setDesignConfig(array('area' => 'frontend'))
		          // ->setReplyTo($customer_email)
		          ->sendTransactional(
		              Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
		              Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
		              $customer_email,
		              null,
		              array('data' => $varObject)
		          );

		    	$translate_customer->setTranslateInline(true);

			}catch (Exception $e) {
					echo $e->getMessage(); die();
			}
		}	
	}

	public function sendEmail($returnData, $customerEmail=false, $message){
		$list_imei =  $returnData['canceled_imei'];
		$reason_array = json_decode($returnData['reason'],1);
		$return_action_array = json_decode($returnData['return_action'],1);
		$status_array = json_decode($returnData['status'],1);
		$status_label = $this->status;
		$reason_label = $this->reason;
		$imei_array = explode(",", rtrim($list_imei,","));
		$table = '';
			$sn = 1;
		$order = Mage::getModel('sales/order')->loadByIncrementId($returnData['order_number']);	
		foreach ($imei_array as $key => $imei) {
		foreach ($order->getInvoiceCollection() as $inv)
          {
            $items = $inv->getItemsCollection()->getData();
            foreach ($items as $item)
            {
              //$imeiNumbers = explode(' ', $item['serial']);
           	$serial = (string) $item['serial'];
        	$imeiNumbers = preg_split('/\s+/', $serial);
              foreach ($imeiNumbers as $imeio)
              {
                if($imei == $imeio){
                  $proName = $item['name'];
                  break;
                }
              }
            }
          }	
		$reason = $reason_label[$reason_array[$imei]];
		// $return_action = $return_action_array[$imei];
		$status = $status_label[$status_array[$imei]];	


    $directory = Mage::getBaseDir('media') . DS . 'order_return' . DS . $imei . DS;
    $files = scandir ($directory);
    $firstFile = Mage::getBaseUrl('media') .  'order_return' . DS . $imei . DS . $files[2];
		
		$table .= '<tr style="background:#FFFFFF;">';
        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">'.$sn.'</td>';
        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">'.$proName.'</td>';
        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">'.$imei.'</td>';
        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">'.$reason.'</td>';
          
        $table .= '<td style="font-family:arial; font-size:12px; color:#686868; border-right:1px solid #cccccc; padding:5px 10px; line-height:18px;">';
        if($files[2]){
        	$table .= '<a href="'.$firstFile.'" target="_blank"><img style="width:40px;" src="'.$firstFile.'"></a>';
        }else{
        	$table .= '<img style="width:40px;" src="http://www.electronicsbazaar.com/media/emailcampaigns/return-order/images/logo1.png">';
        }
        $table .= '</td>';

        $table .= '<td style="padding:5px 10px; line-height:18px;">'.$status.'</td>';

        $table .= '</tr>';
        $sn++;
		}

		// $postData = $table;
		$postData['ordno'] = $order_id = $returnData['order_number'];

		$orderModel = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		$customer_email = $orderModel->getCustomerEmail();
		$customerName = $orderModel->getCustomerName();

		$postData['table'] = $table;
		try {
			$postObject = new Varien_Object();  
      		$postObject->setData($postData); 

        	$translate = Mage::getSingleton('core/translate');
	        /* @var $translate Mage_Core_Model_Translate */
	    	$translate->setTranslateInline(false);

	    	$mailTemplate = Mage::getModel('core/email_template');
	      	/* @var $mailTemplate Mage_Core_Model_Email_Template */
	    	$recipients = explode(",",Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT)); 
	    	$mailTemplate->setDesignConfig(array('area' => 'frontend'))
	          //->setReplyTo($customer_email)
	          ->sendTransactional(
	              Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
	              Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
	              $recipients,
	              null,
	              array('data' => $postObject)
	          );

	    	$translate->setTranslateInline(true);

		}catch (Exception $e) {
				echo $e->getMessage(); die();
		}	

		//notify customer
		if($customerEmail){
			try {
				$postData['salutation'] = $customerName;
				$postData['message'] = $message;


				$varObject = new Varien_Object();  
	      		$varObject->setData($postData); 
	        	$translate_customer = Mage::getSingleton('core/translate');
		        /* @var $translate Mage_Core_Model_Translate */
		    	$translate_customer->setTranslateInline(false);

		    	$mailTemplate_customer = Mage::getModel('core/email_template');
		      	/* @var $mailTemplate Mage_Core_Model_Email_Template */
		    	//$recipients = explode(",",Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT)); 
		    	$mailTemplate_customer->setDesignConfig(array('area' => 'frontend'))
		          // ->setReplyTo($customer_email)
		          ->sendTransactional(
		              Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
		              Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
		              $customer_email,
		              null,
		              array('data' => $varObject)
		          );

		    	$translate_customer->setTranslateInline(true);

			}catch (Exception $e) {
					echo $e->getMessage(); die();
			}
		}
	    //return $mailTemplate->getData(); 
	}

	public function getReason(){
		return $this->reason;
	}
	//bank lists from customer using customer id
	public function getCustomerBanks($customer_id){
		if($customer_id){
			try{
				$bankCollection = Mage::getModel('orderreturn/banking')->getCollection();
				$bankCollection->addFieldToFilter('customer_id',$customer_id);

				$bankList = $bankCollection->getData();

				return $bankList;
			}catch(Exception $e){
				Mage::log($e->getMessage());
			}
		}
		return false;
	}

	public function getCustomer($order_id){
		$order_model = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		// print_r($order->getData());
		$order = $order_model->getData();
		$customer_array = array();
		if(!empty($order)){
			$customer_array['name'] = $order['customer_firstname']." ".$order['customer_lastname'];
			$customer_array['email'] = $order['customer_email'];

			$customer_id = $order['customer_id'];
			$customer = Mage::getModel('customer/customer')->load($customer_id);
			$asm_map = $customer->getAsmMap();
			$objAffiliatescontacts = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts();
			$asm_name = $objAffiliatescontacts->getOptionText($asm_map);
			$customer_array['asm_name'] = $asm_name;
		}



		return $customer_array;
	}

	public function getGridCollection(){
		$collection = Mage::getModel("orderreturn/return")->getCollection();
		$order_return = $collection->getData();
		$grid_row = array();
		$cnt=0;
		foreach ($order_return as $key => $return) {
			$order_id = $return['order_number'];
			$pickup_address = $return['pickup_address'];
			$id = $return['id'];

			$imei_array = explode(",",$return['canceled_imei']);

			$reason_array = json_decode($return['reason'],1);
			$return_array = json_decode($return['return_action'],1);
			$status_array = json_decode($return['status'],1);
			
			foreach ($imei_array as $key => $imei) {
				$grid_row[$cnt]['id'] = $id;
				$grid_row[$cnt]['canceled_imei'] = $imei;
				$grid_row[$cnt]['order_number'] = $order_id;
				$grid_row[$cnt]['pickup_address'] = $pickup_address;
				$grid_row[$cnt]['reason'] = $this->reason[$reason_array[$imei]];
				$grid_row[$cnt]['return'] = $this->return_action[$return_array[$imei]];
				$grid_row[$cnt]['status'] = $this->status[$status_array[$imei]];
			$cnt++;
			}

		}
		return $grid_row;
	}


	//custom grid for order return module
	/*
		Array
	(
	    [id] => Array
	        (
	            [from] => 1
	            [to] => 2
	        )

	    [order_number] => 78
	    [canceled_imei] => 09
	    [created_at] => Array
	        (
	            [from] => 02/3/2017
	            [to] => 02/3/2017
	            [locale] => en_US
	        )

	    [updated_at] => Array
	        (
	            [locale] => en_US
	        )

	)*/

	public function getCustomGrid($filterdata){
		$collection = Mage::getModel("orderreturn/return")->getCollection();

		//filter by id
		if(!empty($filterdata['id'])){
			// echo $filterdata['id']['from'];
			if(!empty($filterdata['id']['from'])){
				$collection->addFieldToFilter('id',array('gteq'=>$filterdata['id']['from']));
			}
			if(!empty($filterdata['id']['to'])){
				$collection->addFieldToFilter('id',array('lteq'=>$filterdata['id']['to']));
			}	
		}

		//filter by order_number
		if(!empty($filterdata['order_number'])){
			$collection->addFieldToFilter('order_number',array('eq'=>$filterdata['order_number']));
		}

		//filter by canceled_imei

		//filter by created_at

		//filter by updated_at

		$grid = $collection->getData();
		$cnt=0;
		$orderreturn = array();
		foreach ($grid as $key => $value) {
			if(!empty($value['canceled_imei'])){
				$imeiObj = $this->getJsonDecodeData($value);
				foreach ($imeiObj as $key => $imeielement) {
					$orderreturn[] = $imeielement;
				}
			}
		}

		return $orderreturn;
	}

	public function getJsonDecodeData($value){
		// print_r($value); die();
		$canceled_imei = $value['canceled_imei'];
		$order_number = $value['order_number'];
		$reason = json_decode($value['reason'],1); //json
		$return_action = json_decode($value['return_action'],1); //json
		$status = json_decode($value['status'],1); //json
		$pickup_address = $value['pickup_address'];
		$utr_filepath = $value['utr_filepath'];
		$replace_increment_id = json_decode($value['replace_increment_id'],1); //json
		$remarks = json_decode($value['remarks'],1); //json
		$docket_no = json_decode($value['docket_no'],1); //json
		$sales_entry_pass = json_decode($value['sales_entry_pass'],1); //json
		$sales_entry_no = json_decode($value['sales_entry_no'],1); //json
		$payment_done = json_decode($value['payment_done'],1); //json
		$pay_reference_no = json_decode($value['pay_reference_no'],1); //json
		$created_at = $value['created_at']; 
		$updated_at = $value['updated_at'];

		$customer = $this->getCustomer($order_number); 

		$order = Mage::getModel('sales/order')->loadByIncrementId($order_number);
		foreach ($order->getInvoiceCollection() as $inv)
        {
            $items = $inv->getItemsCollection()->getData();
            foreach ($items as $item)
            {
              //$imeiNumbers = explode(' ', $item['serial']);
              $serial = (string) $item['serial'];
            	$imeiNumbers = preg_split('/\s+/', $serial);
              foreach ($imeiNumbers as $imeio)
              {
                // if($imei == $imeio){
                //   $proName = $item['name'];
                //   $sku = $item['sku'];
                //   break;
                // }

                $imei_product[$imeio]['name'] = $item['name'];
                $imei_product[$imeio]['sku'] = $item['sku'];
              }
            }
        }
        //echo "<pre>";
        //print_r($imei_product);
    	$imei_array = explode(",", $canceled_imei);
    	//print_r($imei_array);
    	//die('debugging issue');
    	$grid_row = array();
		$serial = 1; 
    	foreach ($imei_array as $key => $imei) {
    		//$grid_row[$imei]['id'] = $serial;
			$grid_row[$imei]['canceled_imei'] = $imei;
			$grid_row[$imei]['order_number'] = $order_number;
			$grid_row[$imei]['product_name'] = $imei_product[$imei]['name'];
			$grid_row[$imei]['sku'] = $imei_product[$imei]['sku'];
			$grid_row[$imei]['asm_name'] = $customer['asm_name'];
			$grid_row[$imei]['pickup_address'] = $pickup_address;
			$grid_row[$imei]['reason'] = $this->reason[$reason[$imei]];
			$grid_row[$imei]['return_action'] = $this->return_action[$return_action[$imei]];
			$grid_row[$imei]['status'] = $this->status[$status[$imei]];
			//$grid_row[$imei]['pickup_address'] = $utr_filepath;
			$grid_row[$imei]['replace_increment_id'] = $replace_increment_id[$imei];
			$grid_row[$imei]['remarks'] = $remarks[$imei];
			$grid_row[$imei]['docket_no'] = $docket_no[$imei];
			$grid_row[$imei]['sales_entry_pass'] = $sales_entry_pass[$imei];
			$grid_row[$imei]['sales_entry_no'] = $sales_entry_no[$imei];
			$grid_row[$imei]['payment_done'] = $payment_done[$imei];
			$grid_row[$imei]['pay_reference_no'] = $pay_reference_no[$imei];

			$serial++;
    	}
    	//print_r($grid_row);
    	//exit;
    	return $grid_row;
	}

	public function getCustomCsv($customGrid){

		//print_r($customGrid);

		if(!empty($customGrid)){
			$fivedigit = rand ( 10000 , 99999 );
			$filename = 'orderreturn_export_'.date('Ymd',strtotime(NOW())).'_'.$fivedigit.'.csv';
			$filepath = 'var/orderreturn/export/'.$filename;
			if (!is_dir('var/orderreturn/export/')){
				mkdir('var/orderreturn/export', 0755, true);
			}
			$fp = fopen($filepath, 'w+');
			if($fp){
				$cnt = 0;
				foreach ($customGrid as $key => $grid) {
					if($cnt == 0){
						$header = array_keys($grid);
						$row1 = array_values($grid);
						fputcsv($fp, $header);
						fputcsv($fp, $row1);
					}else{
						$row = array_values($grid);
						fputcsv($fp, $row);
					}
					$cnt++;
				}
				fclose($fp);
			}	
			//return $filename;
			$dl_file = $filename; // simple file name validation
			$fullPath = $filepath;
			if ($fd = fopen ($fullPath, "r")) {
			    $fsize = filesize($fullPath);
			    $path_parts = pathinfo($fullPath);
			    $ext = strtolower($path_parts["extension"]);
			    switch ($ext) {
			        case "pdf":
			        header("Content-type: application/pdf");
			        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
			        break;
			        // add more headers for other content types here
			        default;
			        header("Content-type: application/octet-stream");
			        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
			        break;
			    }
			    header("Content-length: $fsize");
			    header("Cache-control: private"); //use this to open files directly
			    while(!feof($fd)) {
			        $buffer = fread($fd, 2048);
			        echo $buffer;
			    }
			}
			fclose ($fd);
		}
		return false;
	}

	public function addressList(){
		$customer_session = Mage::getSingleton('customer/session');
		//if customer session exits
		if(!empty($customer_session)){
			//get customer
			$customer = $customer_session->getCustomer();
			$address_list = array();
			//get customer address
			foreach ($customer->getAddresses() as $key => $address) {
				//echo $key;
				//Zend_Debug::dump($address->debug());
				$street = $address->getStreet();

				$street_str = '';
				foreach ($street as $str_key => $value) {
					$street_str .= $value.", ";
				}
				$sub_address = rtrim($street_str,", ");

				//$sub_address = preg_replace('#\s+#',',',trim($sub_address));

				$city = $address->getCity();
				$state = $address->getRegion();
				$postcocde = $address->getPostcode();

				$address_list[$key] = $sub_address.", ".$city.", ".$state.", ".$postcocde;
			}
		}

		return $address_list;
	}

	public function getReuestStatus($req_id){
		if($req_id){
			$model = Mage::getModel('orderreturn/return')->load($req_id);
			$status = $model->getStatus();
			$status_array = json_decode($status,1);
			$check_status = array_values($status_array);

			return $check_status;
		}
	}

	public function getInvoicedOrder($order_number){
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_number);
		$imei_product = array();
		foreach ($order->getInvoiceCollection() as $inv)
        {
            $items = $inv->getItemsCollection()->getData();
            foreach ($items as $item)
            {
              // $imeiNumbers = explode(' ', $item['serial']);
               $serial = (string) $item['serial'];
            	$imeiNumbers = preg_split('/\s+/', $serial);
              foreach ($imeiNumbers as $imeio)
              {
                $imei_product[$imeio]['name'] = $item['name'];
                $imei_product[$imeio]['sku'] = $item['sku'];
              }
            }
        }

        return $imei_product;
	}

	//check is shipment id available as condition to make return available
	public function getShipmentId($increment_id){
		if($increment_id){
			$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
			$shipment = $order->getShipmentsCollection()->getFirstItem();
			return $shipmentIncrementId = $shipment->getIncrementId();
		}

		return false;
	}

	//@function: canReturn
	//@developer: Mahesh Gurav
	//@Date: 08/11/2017 

	public	function canReturn($increment_id){
		if($increment_id){
			$_order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
			$updated_at = $_order->getUpdatedAt();
			$status = $_order->getStatus();
            $order_status = array('replace','returned_and_refunded','replaced','returned','holded','closed','canceled');
            $imei_list = $this->getInvoicedOrder($increment_id);
            //check order is delivered from track your order
            $is_delivered = Mage::helper('trackyourorder')->trackingDetails($increment_id);

            //check if shipping address pincode is serviced by Quality Check Reverse Pickup ECOM company
            $is_ecomQc = $this->pincodeEcomQcRv($increment_id);

			if (!in_array($status, $order_status) && (strtotime($updated_at) > strtotime('-30 days')) && !empty($imei_list) && !empty($is_delivered) && $is_delivered['step'] == 'step3' && $is_ecomQc == 1){
				return true;
			}
		}

		return false;
	}


	//@function pincodeEcomQcRv check pincode is serviced by ECOM RVP QC
	//@developer Mahesh Gurav
	//@date 10 Nov 2017
	public function pincodeEcomQcRv($increment_id){
	    $_order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
    	$address = $_order->getShippingAddress();
    	$ship_address = $address->getData();
      	$postcode = (int) trim($ship_address['postcode']);

      	$serviceablePincodes = Mage::getModel('operations/serviceablepincodes')->getCollection();
      	$serviceablePincodes->addFieldToFilter('pincode',$postcode)->getFirstItem();
      	//$comWc is 1 means serviced
      	//0 means not serviced
      	foreach ($serviceablePincodes as $checkPincode) {
      		$ecomQc = (int) $checkPincode->getData('ecom_qc');
      	}

      	return $ecomQc;
	}

}
	 