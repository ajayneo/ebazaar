<?php //Dev: Mahesh Gurav
//Function : To check orders in last status and update their tracking status
//Date: 28th Mar 2018
//Made Live : 03rd Apr 2018
class Neo_Orderreturn_Model_Trackstatus{
	//function to find tracing status of orders
	public function init(){
		//Create a variable for start time. 
		Mage::log('init order status',null,'shipment_track.log',true);
		$time_start = microtime(true);
	    $ecom_carrier = array('ecom_express','ecom');
	    $vxpress_carrier = array('v_express','vexpress');
	    $bluedart_carrier = array('bluedart');
	    try{

	    $collection = Mage::getResourceModel('sales/order_collection');
	    $collection->addFieldToSelect(array('entity_id','increment_id','status'));
	    $collection->getSelect()->join(array("s" => 'sales_flat_shipment_track'),"main_table.entity_id = s.order_id",array('carrier_code','track_number'));
	    $collection->addFieldToFilter('main_table.created_at',array('gteq'=>gmdate('Y-m-d h:i:s', strtotime('-15 day'))));
            // $collection->addFieldToFilter('main_table.created_at',array('gteq'=>gmdate('Y-03-01 00:00:01')));
	    // $collection->addFieldToFilter('main_table.increment_id',200083624);
	    $collection->addFieldToFilter('main_table.status',array('neq'=>'delivered'));
	    $collection->addFieldToFilter('main_table.status',array('neq'=>'canceled'));
	    $collection->addFieldToFilter('main_table.status',array('eq'=>'shipped'));
	    // echo $collection->getSelect(); exit;
	    Mage::log("orders count to check status = ".count($collection),null,'shipment_track.log',true);
	    $bluedart = array();
	    $ecom = array();
	    $ecom_i = 0;
	    $bluedart_i = 0;
	    foreach ($collection as $order) {
	        if(in_array($order->getCarrierCode(), $ecom_carrier)){
	            $ecom[] = $order->getTrackNumber();
	            $ecom_i++;
	            // if($ecom_i == 19) break;
	        }else if(in_array($order->getCarrierCode(), $bluedart_carrier)){
	            $bluedart[] = $order->getTrackNumber();
	            $bluedart_i++;
	            // if($bluedart_i == 19) break;
	        }else if(in_array($order->getCarrierCode(), $vxpress_carrier)){
	            // echo "This is vexpress = ".$order->getTrackNumber()." <br>";
	        }
	    }

	    $bluedart_status = $this->bluedart($bluedart);
	    $ecom_status = $this->ecom($ecom);
	    $delivered_awb = array_merge($ecom_status['delivered'],$bluedart_status['delivered']);
	    $out_for_delivery = array_merge($ecom_status['out_for_delivery'],$bluedart_status['out_for_delivery']);
	    if(count($out_for_delivery) == 1){
	    	$out_for_delivery = $out_for_delivery[0];
	    }
	    

	    // echo count($out_for_delivery); exit;
	    //set orders status to delivered
	    if(count($delivered_awb) > 0){
			Mage::log("delivered count = ".count($delivered_awb),null,'shipment_track.log',true);
		    $this->setStatusDelivered($delivered_awb);
	    }

		if($out_for_delivery !== NULL){
		    $shipment = Mage::getModel('sales/order_shipment_track')->getCollection();
		    $shipment->addFieldToSelect(array('order_id','track_number'));
		    if(count($out_for_delivery) > 1){
				$search_awb = "(".implode(",", $out_for_delivery).")";
		    	$shipment->getSelect()->where("track_number IN $search_awb AND out_for_delivery_notify <> '1'");
			}else{
				$shipment->getSelect()->where("track_number = $out_for_delivery AND out_for_delivery_notify <> '1'");
			}    
			// echo $shipment->getSelect(); exit();
		    //send out for delivery notification
		    if(count($shipment) > 0){
				Mage::log("out for delivery count = ".count($shipment),null,'shipment_track.log',true);
			    $this->outfordelivery($shipment);
		    }
	    }

		    //Create a variable for end time. 
			$time_end = microtime(true);
			//Subtract the two times to get seconds.
			$time = $time_end - $time_start ;
			$execution_time = 'Execution time : ' . $time . ' seconds for updating '.(count($delivered_awb)+count($shipment)).' orders' ;
			Mage::log($execution_time,null,'shipment_track.log',true);
	    }catch(Exception $e){
	    	// echo $e->getMessage();
	    	Mage::log($e->getMessage(),null,'shipment_track.log',true);
	    }
	    return;
	}

	//function to check bluedart shipments status
	public function bluedart($awb){
	    // echo "Checking ".count($awb)." AWB numbers in bluedart<br/>";
	    $lickey = '4ecbd06dc0b9737d69120029cb05c9df';
	    $loginid = 'BOM00001';
	    // $awbnumbers = $order->getTrackNumber();
	    $awbnumbers = implode(",", $awb);
	    $url = "http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=$loginid&awb=awb&numbers=$awbnumbers&format=xml&lickey=$lickey&verno=1.3f&scan=1";

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url); 
	    curl_setopt($ch, CURLOPT_POST, 1); 
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    // curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
	    $xml = curl_exec($ch);
	    $res = simplexml_load_string($xml);
	    $trackingArray = (array)$res;
	    $awb_numbers_delivered = array();
	    $awb_out_for_delivery = array();
	    foreach ($trackingArray['Shipment'] as $key => $data) {
	    	# code...
	    	$awb = (array)$data;
	    	// echo "<pre>"; print_r($awb); echo "<pre>";
	    	$track_number = (array)$awb['@attributes'];
	    	$track_number = $track_number['WaybillNo'];
	    	// echo $track_number." : ".$awb['Status']." <br>";

	        if($awb['Status'] == 'SHIPMENT OUT FOR DELIVERY'){
	        	// echo "bluedart change status for track # ".$track_number." ".$awb['Status']."<br>";
	            $awb_out_for_delivery[] = $track_number;
	        }

	        if($awb['Status'] == 'SHIPMENT DELIVERED'){
	        	// echo "<pre>"; print_r($awb);
	        	// echo "bluedart change status for track # ".$track_number." ".$awb['Status']."<br>";
	            $awb_numbers_delivered[] = $track_number;
	        }
	        
	        if($awb['Status'] == 'SHIPMENT IN TRANSIT'){
	        	// echo "<pre>"; print_r($awb);
	        	// echo " change status for track # ".$track_number." DELIVERED <br>";
	        }
	    }

	    return array('delivered'=>$awb_numbers_delivered,'out_for_delivery'=>$awb_out_for_delivery);
	}

	//function to check ecom shipments status
	public function ecom($awb){
	    // print_r($awb);
	    // echo "Ecom awb ".count($awb)." numbers checking <br>";
	    $username = 'amiable92914';
	    $pass = 'a4m7ia38b6lel2e3c9tr';
	    $order = array();
	    $url = "http://plapi.ecomexpress.in/track_me/api/mawbd/";
	    $awb_numbers = implode(",", $awb);

	    $postData['awb'] = $awb_numbers;
	    $postData['order'] = $order;
	    $postData['username'] = $username;
	    $postData['password'] = $pass;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url); 
	    curl_setopt($ch, CURLOPT_POST, 1); 
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
	    $xml = curl_exec($ch);
	    $res = simplexml_load_string($xml);
	    $trackingArray = (array)$res;
	    $ecom_delivered = array();
	    foreach ($trackingArray['object'] as $key => $obj) {
	        $data = (array) $obj;
	        $awb_number = $data['field'][0];
	        $awb_status = $data['field'][11];
	        if($awb_status == 'Delivered'){
	        	// echo "ecom change status for track # ".$awb_number." $awb_status <br>";
	            $ecom_delivered[] = $awb_number;
	        }

	        if($awb_status == 'Out For Delivery'){
	        	// echo "ecom change status for track # ".$awb_number." $awb_status <br>";
	        	$out_for_delivery[] = $awb_number;
	        }
	    }

	    return array('delivered'=>$ecom_delivered,'out_for_delivery'=>$out_for_delivery);            
	}

	//set orders delivered
	public function setStatusDelivered($awbnumbers){
		// Mage::register('isSecureArea', 1);
	    $shipment = Mage::getModel('sales/order_shipment_track')->getCollection();
	    $shipment->addFieldToSelect('order_id');
	    if(count($awbnumbers) > 1){
			$search_awb = "(".implode(",", $awbnumbers).")";
	    	$shipment->getSelect()->where("main_table.track_number IN $search_awb");
	    }else{
	    	$shipment->addFieldToFilter('main_table.track_number',$awbnumbers);
	    }

    	if(count($shipment) >= 1){
    		Mage::log('count delivered orders =  '.count($shipment),null,'shipment_track.log',true);
	    	foreach ($shipment as $ship) {
		    	$order_id = $ship->getOrderId();
				try{
					// Mage::log('start delivered status order #'.$order_id,null,'shipment_track.log',true);
				   	$order = Mage::getModel('sales/order')->load($order_id);
	 				$order->setStatus('delivered');
		 			//change order state
		 			$order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
					// Add the comment and save the order (last parameter will determine if comment will be sent to customer)
					$order->addStatusHistoryComment('Order status changed to delivered by orderreturn/trackstatus',false);
					$order->save();
					Mage::log('set delivered status order #'.$order->getIncrementId(),null,'shipment_track.log',true);
				}catch(Exception $e){
			    	Mage::log($e->getMessage(),null,'shipment_track.log',true);
					// return false;
			    }	
		    }

    	}
	    return true;
	}

	//set orders out for delivery & notifications on SMS and email
	public function outfordelivery($shipment){
		try{
	    	if(count($shipment) > 0){
		    	foreach ($shipment as $ship) {
			    	$order_id = $ship->getOrderId();
				    Mage::log('set out for delivery start order id #'.$order_id,null,'shipment_track.log',true);
			    	$collection = Mage::getResourceModel('sales/order_collection');
			    	$collection->addFieldToSelect(array('customer_id','increment_id','base_grand_total'));
			    	$collection->addFieldToFilter('entity_id',$order_id);
			    	$customer_array = $collection->getColumnValues('customer_id');
			    	$order_array = $collection->getColumnValues('increment_id');
			    	$total_array = $collection->getColumnValues('base_grand_total');
			    	$customer_id = $customer_array[0];
			    	$order_num = $order_array[0];
			    	$grand_total = $total_array[0];
			    	$_customer = Mage::getModel('customer/customer')->load($customer_id);
			    	$email = $_customer->getEmail();
			    	$mobile = $_customer->getMobile();

			    	$sms_message = 'Hi '.ucfirst($_customer->getFirstname()).', Your order # '.$order_num.' is out for delivery. The Order value of this order is Rs.'.number_format($grand_total);

			    	// echo $sms_message; exit;

			    	
			    	
			    	$ship->setData('out_for_delivery_notify',1);
			    	$ship->save();

			    	$order = Mage::getModel('sales/order')->load($order_id);
			    	//change order status
				    $order->setStatus('out_for_delivery');
				    // change order state
				    $order->setData('state','out_for_delivery');
				    // Add the comment and save the order (last parameter will determine if comment will be sent to customer)
				    $order->addStatusHistoryComment('Order status changed to out for delivery by orderreturn/trackstatus', false);
				    $order->save();
					$this->sendEmail(ucfirst($_customer->getFirstname()), 'Rs.'.number_format($grand_total), $order_num, $email);
					$this->sendSms($sms_message,$mobile);
				    Mage::log('set out for delivery finish order #'.$order->getIncrementId(),null,'shipment_track.log',true);

			    }
	    	}
	    }catch(Exception $e){
	    	Mage::log($e->getMessage(),null,'shipment_track.log',true);
	    }
		// return true;
		return true;
	}

	public function sendSms($message,$mobile){
		$uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
        $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
        $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');
        $api_url = Mage::getStoreConfig('ebpin_settings/ebpin_login/api_url');

        $data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$mobile.'&Text='.urlencode($message);
        $ch=curl_init($api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result=curl_exec($ch);
        // $result = json_decode($result,1);
        // return true;
        Mage::log("sms sent to mobile #".$mobile,null,'shipment_track.log',true);
        return true;
    }

	public function sendEmail($name = null, $amount = null, $order = null, $email = null){
		$websiteId = Mage::app()->getWebsite()->getId();

		// Instance of customer loaded by the given email
		$customer = Mage::getModel('customer/customer')
		    ->setWebsiteId($websiteId)
		    ->loadByEmail($email);
		$asm = $customer->getAsmMap();
		
		$asm_detail = Mage::getResourceModel('asmdetail/asmdetail_collection');
		$asm_detail->addFieldToSelect(array('email'));
		$asm_detail->addFieldToFilter('name',$asm);
		foreach ($asm_detail as $asmdata) {
			$asm_email = $asmdata->getEmail();
		}
		if($asm_email == ''){
			$asm_email = 'info@electronicsbazaar.com';
		}

		Mage::log("email start #".$order." asm = ".$asm_email,null,'shipment_track.log',true);
		$bcc_emails = array('support@electronicsbazaar.com');
		$subject = 'Electronics Bazaar: Shipment out for delivery for Order # '.$order;
		try{
			$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
			$total = $currency_symbol.number_format($amount);
			$template_variables = array('username'=>$name,'order_id'=>$order,'amount'=>$amount,'email'=>$email);
			$template_id = "shipment_status_email_template";
			$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
			$processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
			//echo $processedTemplate; exit;
			$senderName = Mage::getStoreConfig('trans_email/ident_sales/name');
			$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email');
			
			$z_mail = new Zend_Mail('utf-8');
		    $z_mail->setBodyHtml($processedTemplate)
		        ->setSubject($subject)
		        ->addTo($email)
		        ->addCc($asm_email)
		        ->addBcc($bcc_emails)
		        ->setFrom($senderEmail, $senderName);
			$z_mail->send();
			Mage::log("email sent to order #".$order,null,'shipment_track.log',true);
		}catch(Exception $e){
			Mage::log($e->getMessage(),null,'shipment_track.log',true);
		}

		return true;
	}
}
