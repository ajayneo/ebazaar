<?php
class Neo_Shippinge_Model_Ecom extends Mage_Core_Model_Abstract
{
	public function remove_bs($Str) {  
		$StrArr = str_split($Str); $NewStr = '';
		foreach ($StrArr as $Char) {    
			$CharNo = ord($Char);
			if ($CharNo == 163) { $NewStr .= $Char; continue; } // keep £ 
			if ($CharNo > 31 && $CharNo < 127) {
			  $NewStr .= $Char;    
			}
		}  
		return $NewStr;
	}
	public function register($post)
	{
		$incrementId = $post["incrementid"];
		$weight = (float)$post["weight"];
		$invoice_id     = $post["invoice_id"];
		$input_array = array();
		if($incrementId)
		{
			$order = Mage::getModel("sales/order")->loadByIncrementId($incrementId);
			$invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
			$orderId = $order->getEntityId();
			$invoice_number = $invoice->getIncrementId();
			$invoice_date = $invoice->getCreatedAt();
			$invoice_date_format = date('dMY',strtotime($invoice_date));
			$total_qty = (int) $invoice->getTotalQty();
			$ecom_gstdetails = Mage::helper('indiagst')->getEcomGstDetails($invoice_id);
			$sellergstin = Mage::helper('indiagst')->sellerGstIn($orderId);

			$multiseller = false;

			if(count($ecom_gstdetails) > 1){
				$multiseller = true;
			}

			$invoice_items = $invoice->getAllItems();
			$item_description = '';
			if(count($invoice_items) > 1){
				foreach ($invoice_items as $item) {
					$item_description .= $item->getName().' +';
				}
			}else{
				  foreach ($invoice_items as $item) {
					$item_description = $item->getName();
				}
			}

			//to fix long name issue on 2nd Jul 2018 Mahesh Gurav
			if($incrementId == 200110812){
				$item_description = 'HP Probook 640 G1 + Lenovo ThinkPad T410 + Laptop T430 + Lenovo X230';
			}

			$item_description = rtrim($item_description,'+');

			$shipping_address = $order->getShippingAddress();
            $customer_name = $order->getCustomerFirstname();
            $amount = 0 ;
			$payment_method = $order->getPayment()->getMethodInstance()->getCode();
			$product = 'PPD';
			if($payment_method == "cashondelivery") {
				$product = 'COD';
				$amount = (int)$invoice->getGrandTotal();
			}
			//fix issue of amount 0 should be passed to PPD orders
			// if($amount == 0)
			// {
			// 	$amount = 1;
			// }

			$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress($invoice->getorder_id());
			$shipping = $shipping_address->getData();
			
			//street character correction
			$street = $shipping['street'];
			$street = str_replace('#','',trim($street));
			//$street = preg_replace('#\s+#',',',trim($street));
			
			$warehouse0 = $warehouseAddress[0];
			$warehouse1 = $warehouseAddress[1];
			$warehouse2 = $warehouseAddress[2];
			$warehouse7 = $warehouseAddress[7];
			$warehouse8 = $warehouseAddress[8];
			$city = $shipping['city'];
			$telephone = $shipping['telephone'];
			$region = $shipping['region'];
			$postcode = $shipping['postcode'];
			
			$wh_postcode = $warehouseAddress[4];


			//$username = "compuindiabom";
			//$username = "compuindiabom";
			//$password = "c2omp345uindib4om";
			//$password = "4a5M@i6a7b8l2e5E6lE";

			$username ="amiable92914";
			$password ="a4m7ia38b6lel2e3c9tr";

			//$username = "amiable92914";
			//$password = "a5M6i7@A7b8L9e"; 
			$testusername = 'ecomexpress';
			$testpassword = 'Ke$3c@4oT5m6h#$';
			
			//test url to fecth awb number
			$awburl = 'http://ecomm.prtouch.com/apiv2/fetch_awb/';
			$liveawburl = 'http://api.ecomexpress.in/apiv2/fetch_awb/';
			
			//extract data from the post
			$awb_request['username']=$username;
			$awb_request['password']=$password;
			$awb_request['count']=1;
			$awb_request['type']= $product;

			//changes added by Mahesh Gurav on 04/09/2017
			//awb numbers from model
			$awb_no = 0;
			if($product == 'COD'){
				$awb_cod = Mage::getModel('ecom/codawb')->getCollection();
		        $awb_cod->addFieldToFilter('status',array('eq'=>'0'));
		        
		        $awb_no_data = $awb_cod->getFirstItem();

		        if($awb_no_data->getData()){
		        	$awb_no = $awb_no_data->getAwb();
		        	$disable_awb = Mage::getModel('ecom/codawb')->load($awb_no_data->getId());
		        }else{
		        	$error_response = array();
					$error_response[] = array(
						"error" => true,
						"message"   =>  'COD AWB Numbers are used up, refill AWB numbers for COD orders'
					);
					return $error_response;
					exit;
		        }
		       
			}else{
				$awb_ppd = Mage::getModel('ecom/ppdawb')->getCollection();
		        $awb_ppd->addFieldToFilter('status',array('eq'=>'0'));
		        
		        $awb_no_data = $awb_ppd->getFirstItem();

		        if($awb_no_data->getData()){
		        	$awb_no = $awb_no_data->getAwb();
		        	$disable_awb = Mage::getModel('ecom/ppdawb')->load($awb_no_data->getId());
		        }else{
		        	$error_response = array();
					$error_response[] = array(
						"error" => true,
						"message"   =>  'COD AWB Numbers are used up, refill AWB numbers for COD orders'
					);
					return $error_response;
					exit;
		        }
			}


			if($awb_no == 0){
				$error_response = array();
					$error_response[] = array(
						"error" => true,
						"message"   =>  'AWB Numbers are used up, please refill AWB Numbers'
					);
					return $error_response;
					exit;
			}
			
			//prepare for shipment call
			$data['username']=$username;
			$data['password']=$password;
			//$wh_postcode = $postcode = 400611;

			$additional_info = '';
			if($multiseller){
				$additional_info .='"ADDITIONAL_INFORMATION": {
				"MULTI_SELLER_INFORMATION": [';
				$ecom_gst_items = array_values($ecom_gstdetails);
				$additional_info .= implode(",", $ecom_gst_items);
				$additional_info .=']}';
			}else{
				$singleItem = Mage::helper('indiagst')->getEcomSingleItem($invoice_id);

				$additional_info ='"ADDITIONAL_INFORMATION":'.$singleItem;
			}
			

			$item_description = str_replace('"', '', $item_description);
			$item_description = str_replace('(', '', $item_description);
			$item_description = str_replace(')', '', $item_description);
			
			$data['json_input']='[{"AWB_NUMBER":"'.$awb_no.'","ORDER_NUMBER":"'.$incrementId.'","PRODUCT":"'.$product.'","CONSIGNEE":"'.$customer_name.'","CONSIGNEE_ADDRESS1":"'.$street.'","CONSIGNEE_ADDRESS2":"ADD2","CONSIGNEE_ADDRESS3":"ADD3","DESTINATION_CITY":"'.$city.'","PINCODE":"'.$postcode.'","STATE":"'.$region.'","MOBILE":"'.$telephone.'","TELEPHONE":"'.$telephone.'","ITEM_DESCRIPTION":"'.$item_description.'","PIECES":"'.$total_qty.'","COLLECTABLE_VALUE":"'.$amount.'","DECLARED_VALUE":"'.$amount.'","ACTUAL_WEIGHT":"'.$weight.'","VOLUMETRIC_WEIGHT":"0","LENGTH":"10","BREADTH":"10","HEIGHT":"10","PICKUP_NAME":"'.$warehouse0.'","PICKUP_ADDRESS_LINE1":"'.$warehouse1.'","PICKUP_ADDRESS_LINE2":"'.$warehouse2.'","PICKUP_PINCODE":"'.$wh_postcode.'","PICKUP_PHONE":"'.$warehouse7.'","PICKUP_MOBILE":"'.$warehouse8.'","RETURN_PINCODE":"'.$wh_postcode.'","RETURN_NAME":"'.$warehouse0.'","RETURN_ADDRESS_LINE1":"'.$warehouse1.'","RETURN_ADDRESS_LINE2":"'.$warehouse2.'","RETURN_PHONE":"'.$warehouse7.'","RETURN_MOBILE":"'.$warehouse8.'","DG_SHIPMENT": "false",'.$additional_info.'}]';

			//$data['json_input'] = mb_convert_encoding($data['json_input'], 'UTF-8', 'UTF-8');

			$data['json_input'] = $this->remove_bs($data['json_input']);
			$payload = $data['json_input'];
			//Live URL: 
			// http://api.ecomexpress.in/apiv2/manifest_awb/
			$liveurl = 'http://api.ecomexpress.in/apiv3/manifest_awb/';
			//test url to  send api request
			// $url = 'http://ecomm.prtouch.com/apiv2/manifest_awb/';
		

			$ch = curl_init();                    // initiate curl
			curl_setopt($ch, CURLOPT_URL,$liveurl);
			curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // define what you want to post
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
			$output = curl_exec($ch); // execute
            //close connection

			curl_close($ch);

			$output_array = json_decode($output,1);
            $success = $output_array['shipments'][0]['success'];
            $awb_number = $output_array['shipments'][0]['awb'];

            if(!empty($awb_number)) {
            $disable_awb->setStatus('1');
            	$disable_awb->save();
			$response = array();
			$response[] = array(
					"error" => false,
					"awb"   =>  $awb_no,
					"destination"   => $shipping_address->getRegion().' '.$shipping_address->getCity(),
					"payload" 	=> $payload
				);
			} else {
				$response = array();
				$response[] = array(
					"error" => true,
					"message"   =>  $output_array['shipments'][0]['reason'] ."Payload : ".$payload
				);
			}

			return $response;
		}
	}

	public function registerBackup($post)
	{
		$incrementId = $post["incrementid"];
		$weight = (float)$post["weight"];
		$invoice_id     = $post["invoice_id"];

		if($incrementId)
		{
			$order = Mage::getModel("sales/order")->loadByIncrementId($incrementId);
			$invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
			$shipping_address = $order->getShippingAddress();
            $customer_name = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
            $amount = (int)$invoice->getGrandTotal();
			$payment_method = $order->getPayment()->getMethodInstance()->getCode();
			$product = 'PPD';
			if($payment_method == "cashondelivery") {
				$product = 'COD';
			}

			$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress($invoice->getorder_id());

			$username = "compuindiabom";
			$password = "c2omp345uindib4om";
			 $xml      =
			"<?xml version='1.0' encoding='UTF-8'?> <ECOMEXPRESS-OBJECTS>   
			<SHIPMENT> <AWB_NUMBER></AWB_NUMBER>
			<ORDER_NUMBER>".$incrementId."</ORDER_NUMBER>  
			<PRODUCT>".$product."</PRODUCT>
			<CONSIGNEE>".$customer_name."</CONSIGNEE>
			<CONSIGNEE_ADDRESS1>".$shipping_address->getStreet()."</CONSIGNEE_ADDRESS1>
			<CONSIGNEE_ADDRESS2></CONSIGNEE_ADDRESS2>
			<CONSIGNEE_ADDRESS3></CONSIGNEE_ADDRESS3>
			<DESTINATION_CITY>".$shipping_address->getCity()."</DESTINATION_CITY>
			<PINCODE>".$shipping_address->getPostcode()."</PINCODE>
			<STATE>".$shipping_address->getRegion()."</STATE>  
			<MOBILE></MOBILE>
			<TELEPHONE>".$shipping_address->getTelephone()."</TELEPHONE>  
			<ITEM_DESCRIPTION></ITEM_DESCRIPTION>  
			<PIECES>1</PIECES>  
			<COLLECTABLE_VALUE>".$amount."</COLLECTABLE_VALUE>  
			<DECLARED_VALUE>".$amount."</DECLARED_VALUE>
			<ACTUAL_WEIGHT>".$weight."</ACTUAL_WEIGHT>
			<VOLUMETRIC_WEIGHT>0</VOLUMETRIC_WEIGHT>  <LENGTH> 10</LENGTH>
			<BREADTH>10</BREADTH>  <HEIGHT>10</HEIGHT> <VENDOR_ID></VENDOR_ID>
			<PICKUP_NAME>Electronics Bazaar</PICKUP_NAME>
			<PICKUP_ADDRESS_LINE1>".$warehouseAddress[1]."</PICKUP_ADDRESS_LINE1>
			<PICKUP_ADDRESS_LINE2>".$warehouseAddress[2]."</PICKUP_ADDRESS_LINE2>
			<PICKUP_PINCODE>".$warehouseAddress[4]."</PICKUP_PINCODE>
			<PICKUP_PHONE>".$warehouseAddress[7]."</PICKUP_PHONE>
			<PICKUP_MOBILE>".$warehouseAddress[8]."</PICKUP_MOBILE>  </SHIPMENT>
			</ECOMEXPRESS-OBJECTS>";
			$postfields["username"] = $username; 
			$postfields["password"] = $password; 
			$postfields["xml_input"]      = $xml;
			$postdata = "username=$username&password=$password&xml_input=".urlencode($xml);

			#Test URL: http://ecomm.prtouch.com/apiv2/manifest_awb/&username=USER NAME&password=PASSWORD&json_input=JSON_INPUT Live URL: http://api.ecomexpress.in/apiv2/manifest_awb/username=USERNA ME&password=PASSWORD&json_input=JSON_INPUT 

			$url = "http://api.ecomexpress.in/api/api_create_order_to_awb_xml/";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_POST, 1); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
			$xml = curl_exec($ch);
			$res = simplexml_load_string($xml);
			$awb1 = (array)$res;


            $awb2 = (array)$awb1['AIRWAYBILL-OBJECTS'];
            $awb3 = (array)$awb2['AIRWAYBILL'];
            $awb = (array)$awb3['airwaybill_number'];
                       
			if($awb[0]) {
			$response = array();
			$response[] = array(
					"error" => false,
					"awb"   =>  $awb[0],
					"destination"   => $shipping_address->getRegion().' '.$shipping_address->getCity()
				);
			} else {
				$response = array();
				$response[] = array(
					"error" => true,
					"message"   =>  $xml
				);
			}
			return $response;
		}
	}

	//ECOM QC RVP prepare to send
	//rvp qc awb #MaheshGurav #07Dec2017
	public	function rvpawb($count){
		
		
		$username ="amiable92914";
		$password ="a4m7ia38b6lel2e3c9tr";

		/*$testusername = 'ecomexpress';
		$testpassword = 'Ke$3c@4oT5m6h#$';*/

		//test url to fecth awb number
		// $test_fetchawb = 'http://ecomm.prtouch.com/apiv2/fetch_awb/';
		//$api_url = 'http://staging.ecomexpress.in/apiv2/fetch_awb/';
		$api_url = 'http://api.ecomexpress.in/apiv2/fetch_awb/';

		//extract data from the post
		$awb_request['username']=$username;
		$awb_request['password']=$password;
		$awb_request['count']=$count;
		$awb_request['type']= 'REV';
		// print_r($awb_request); exit;

		//http://ecomm.prtouch.com/apiv2/fetch_awb/?username=ecomexpress&password=Ke$3c@4oT5m6h#$&count=1&type=REV
		//http://api.ecomexpress.in/apiv2/fetch_awb/?username=amiable92914&password=a5M6i7@A7b8L9e&count=1&type=REV

		//initiate curl call for awb 
		$awbch = curl_init();                    // initiate curl
		curl_setopt($awbch, CURLOPT_URL,$api_url);
		curl_setopt($awbch, CURLOPT_POST, true);  // tell curl you want to post something
		curl_setopt($awbch, CURLOPT_POSTFIELDS, $awb_request); // define what you want to post
		curl_setopt($awbch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
		$awb_json = curl_exec($awbch); // execute
		//close connection
		curl_close($awbch);
		//get awb number from output
		$awb_data = json_decode($awb_json,1);

		return $awb_data['awb'];
	}
	
	//ECOM QC RVP #MaheshGurav #07Dec2017 
	//PREPARE ECOM QC DATA WITH AWB NUMBER
	public function sendQCRVP($request_id){
		
		$returnModel = Mage::getModel('orderreturn/return')->load($request_id);
		$canceled_units = $returnModel->getCanceledImei();
		$order_no = $returnModel->getOrderNumber();
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_no);
		$invoiceIds = $order->getInvoiceCollection()->getAllIds();
		$imeiArray = explode(",", $canceled_units);
		foreach ($imeiArray as $key => $value) {
			$array_ike[] = array("like"=>"%$value%");
		}
		$count_items = count($imeiArray); 
		$invoiceitems = Mage::getModel('sales/order_invoice_item')->getCollection();
		$invoiceitems->addFieldToFilter('serial',$array_ike);
		$awb_numbers = Mage::getModel('shippinge/ecom')->rvpawb($count_items);
		
		//prepare data to send to ECOM QC RVP
		$check_items_return = array();
		foreach ($invoiceitems as $item) {
			$serial_no = trim($item->getSerial());
			$item_serials = explode(" ",$serial_no);
			foreach ($item_serials as $item_serial) {
				if(in_array($item_serial, $imeiArray)){
					$check_items_return[$item_serial] = $item;
				}		
			}
		}

		$shippingAddress = $order->getShippingAddress()->getData();
		//SEND DATA TO ECOM QC RVP		
		$result = $this->sendRvpRequest($check_items_return,$shippingAddress,$order_no);

		return $result;
	}//function ends

	//send rvp request #MaheshGurav #07Dec2017 
	public function sendRvpRequest($check_items_return,$shippingAddress,$order_no){
		$i=0;
		$count_items = count($check_items_return);
		$awb_numbers = Mage::getModel('shippinge/ecom')->rvpawb($count_items);
		
		$test_url =  'http://staging.ecomexpress.in/apiv2/manifest_awb_rev_v2/';
		$testusername = 'ecomexpress';
		$testpassword = 'Ke$3c@4oT5m6h#$';

		$live_url = 'http://api.ecomexpress.in/apiv2/manifest_awb_rev_v2/';
		$username ="amiable92914";
		$password ="a4m7ia38b6lel2e3c9tr";

		$response_msg = array();
		foreach ($check_items_return as $imei_no => $item) {
			$serial_no = trim($imei_no);
			$item_description = trim($item->getName());
			$item_sku = $item->getSku();
			$awb_no = $awb_numbers[$i];
			$invoice_id =  $item->getParentId();
			$invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
			$invoice_date = $invoice->getCreatedAt();
			$invoice_number = $invoice->getIncrementId();
			$order_id = $invoice->getorder_id();
			$seller_gst = Mage::helper('indiagst')->sellerGstIn($order_id);
			$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress($order_id);
			$invoice_formated_date = date('d-M-Y',strtotime($invoice_date));

			$item_description = str_replace('"', '', $item_description);
			$item_description = str_replace('(', '', $item_description);
			$item_description = str_replace(')', '', $item_description);
			
			$divideTax = Mage::helper('indiagst')->isInterStateGst($invoice_id);
			// $invoice_items = array();
			$cgst = $sgst = $igst = 0;
			$hsn = Mage::helper('indiagst')->getHsnBySku($item_sku);
			$price = (int)$item->getPriceInclTax();
			$tax_percent = (int)$item->getOrderItem()->getTaxPercent();
			$total_tax = (int) $item->getTaxAmount();
			if($divideTax){
				$cgst = $sgst = $total_tax / 2;
				$cgst_rate = $sgst_rate = $tax_percent / 2;
				$igst = 0;
				$igst_rate = 0;
			}else{
				$cgst = $sgst =  0;
				$cgst_rate = $sgst_rate =  0;
				$igst = $total_tax;
				$igst_rate = $tax_percent;
			}

			//address for drop pickup warehouse 
			$seller_name = $warehouseAddress[0];
			$seller_address =  $warehouseAddress[1].', '.$warehouseAddress[2].' ,'.$warehouseAddress[3];
			$seller_pincode = $warehouseAddress[4];			
			$seller_state = $warehouseAddress[5];			
			$seller_mobile1 = $warehouseAddress[7]; //9833538063			
			$seller_mobile2 = $warehouseAddress[8];	//9769492607		
			
			//address for reverse pickup
			$revpickup_name = $shippingAddress['firstname'].' '.$shippingAddress['lastname'];
			$street_lines = $shippingAddress['street'];
			$revpickup_address1 = str_replace("\n", ', ', $street_lines);
			$revpickup_address1 = trim($revpickup_address1);
			$revpickup_city = $shippingAddress['city'];
			$revpickup_pincode = $shippingAddress['postcode'];
			$revpickup_state = $shippingAddress['region'];
			$revpickup_mobile = $shippingAddress['telephone'];
			
			$rvp_data = '';
			//actual rvp for customer pickup
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
			"EXTRA_INFORMATION": "Reason for reverse pickup is Item Damaged", 
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
            "ITEM_VALUE": '.$price.',
            "ITEM_CATEGORY": "ELECTRONICS",
			"SELLER_NAME": "'.$seller_name.'",
			"SELLER_ADDRESS": "'.$seller_address.'",
			"SELLER_STATE": "'.$seller_state.'",
			"SELLER_PINCODE": "'.$seller_pincode.'",
			"SELLER_TIN": "",
			"INVOICE_NUMBER": "'.$invoice_number.'",
			"INVOICE_DATE": "'.$invoice_formated_date.'",
			"ESUGAM_NUMBER": "",
			"SELLER_GSTIN": "'.$seller_gst.'",
			"GST_HSN": "'.$hsn.'",
			"GST_ERN": "",
			"GST_TAX_NAME": "DELHI GST",
			"GST_TAX_BASE": '.number_format((float)$total_tax, 2, '.', '').',
			"GST_TAX_RATE_CGSTN": '.number_format((float)$cgst_rate, 1, '.', '').',
			"GST_TAX_RATE_SGSTN": '.number_format((float)$sgst_rate, 1, '.', '').',
			"GST_TAX_RATE_IGSTN": '.number_format((float)$igst_rate, 1, '.', '').',
			"GST_TAX_TOTAL": '.number_format((float)$total_tax, 2, '.', '').',
			"GST_TAX_CGSTN": '.number_format((float)$cgst, 2, '.', '').',
			"GST_TAX_SGSTN": '.number_format((float)$sgst, 2, '.', '').',
			"GST_TAX_IGSTN": '.number_format((float)$igst, 2, '.', '').',
			"DISCOUNT": '.number_format((float)$item->getDiscount(), 2, '.', '').'}}';

	
		//sample rvp for teesting
			$rvp_data = '{
        "AWB_NUMBER": "'.$awb_no.'",
        "ORDER_NUMBER": "'.$order_no.'",
        "PRODUCT": "rev",
        "REVPICKUP_NAME": "Pia Bhaa",
        "REVPICKUP_ADDRESS1": "Laxmi Villa Prem Nagar Tekdi Near Swami Shan Prakash Yoga Kendra Ashram",
        "REVPICKUP_ADDRESS2": "Nag",
        "REVPICKUP_ADDRESS3": "ew",
        "REVPICKUP_CITY": "Thane",
        "REVPICKUP_PINCODE": "111111",
        "REVPICKUP_STATE": "MAH",
        "REVPICKUP_MOBILE": "9999999999",
        "REVPICKUP_TELEPHONE": "0123456789",
        "PIECES": "1",
        "COLLECTABLE_VALUE": "1140",
        "DECLARED_VALUE": "2099",
        "ACTUAL_WEIGHT": "0.87",
        "VOLUMETRIC_WEIGHT": "0.87",
        "LENGTH": "320",
        "BREADTH": "200",
        "HEIGHT": "120",
        "VENDOR_ID": "",
        "DROP_NAME": "Khasra No.14/14,17,18,23, 24/1",
        "DROP_ADDRESS_LINE1": "Khasra No.14/14,17,18,23, 24/1",
        "DROP_ADDRESS_LINE2": "Village Khawaspur, Tehsil Farrukhnagar,",
        "DROP_PINCODE": "111111",
        "DROP_MOBILE": "9999999999",
        "ITEM_DESCRIPTION": "XYZ",
        "DROP_PHONE": "57567",
        "EXTRA_INFORMATION": "test info",
        "DG_SHIPMENT": "true",
        "ADDONSERVICE": ["QC"],
        "QC": [{
                        "QCCHECKCODE": "GEN_ITEM_DESC_CHECK",
                        "VALUE": "Xiaomi MI4I",
                        "DESC": "CHECK"
        }, {
                        "QCCHECKCODE": "GEN_MOBILE_IMEI_LAST4_INPUT",
                        "VALUE": "86861226",
                        "DESC": "CHECK"
        }, {
                        "QCCHECKCODE": "GEN_ITEM_DAMAGE_CHECK",
                        "VALUE": "YES",
                        "DESC": "CHECK"
        }],
        "ADDITIONAL_INFORMATION": {
                        "ITEM_VALUE": 1358.24,
                        "ITEM_CATEGORY": null,
                        "SELLER_NAME": "Shreyash Retail Private Limited",
                        "SELLER_ADDRESS": "Plot No. 231, Bangalore Karnataka 560067",
                        "SELLER_PINCODE": "111111",
                        "SELLER_TIN": "",
                        "INVOICE_NUMBER": "",
                        "INVOICE_DATE": "",
                        "ESUGAM_NUMBER": null,
                        "SELLER_GSTIN": null,
                        "SELLER_STATE": "DL",
                        "GST_TAX_NAME": "DELHI GST",
                        "GST_TAX_BASE": 4000.0,
                        "GST_TAX_RATE_CGSTN": 9.0,
                        "GST_TAX_RATE_SGSTN": 9.0,
                        "GST_TAX_RATE_IGSTN": 0.0,
                        "GST_TAX_TOTAL": 720.00,
                        "GST_HSN": null,
                        "GST_ERN": null,
                        "GST_TAX_CGSTN": null,
                        "GST_TAX_SGSTN": null,
                        "GST_TAX_IGSTN": null,
                        "DISCOUNT": null

        }
}';

			$payload = '{ "ECOMEXPRESS-OBJECTS": { "SHIPMENT":'.$rvp_data_live.'}}';
			

			$data['username']=$username;
			$data['password']=$password;
			$data['json_input'] = $payload;

			$ch = curl_init();                    // initiate curl
			curl_setopt($ch, CURLOPT_URL,$live_url);
			curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // define what you want to post
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
			$output = curl_exec($ch); // execute
			$output_array = json_decode($output,1);

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
					$response_msg[$i]['payload'] = $payload;
					
				}else if($success == 'True'){
					$response_msg[$i]['serial'] = $serial_no;
					$response_msg[$i]['msg'] = $airwaybill_response['airwaybill_number'];
					$response_msg[$i]['error'] = 0;
					$response_msg[$i]['payload'] = $payload;
				}			
			}

			if($shipments){
				$response_msg[$i]['serial'] = $serial_no;
				$response_msg[$i]['msg'] = $shipments[0]['reason'];
				$response_msg[$i]['error'] = 1;
				$response_msg[$i]['payload'] = $payload;
			}
			$i++;

		}//foreach item ends


		return $response_msg;

	}//function ends

	//send rvp request
	public function sendRvpRequestGadget($request_id){
		
		$awb_no = Mage::getModel('shippinge/ecom')->rvpawb(1);
		$gadget = Mage::getModel('gadget/request')->load($request_id);
		
		$gadgetOptions = (array)json_decode($gadget->getOptions());
		$api_url = 'http://api.ecomexpress.in/apiv2/manifest_awb_rev_v2/';
		
		$username ="amiable92914"; //live
		$password ="a4m7ia38b6lel2e3c9tr"; //live
		$response_msg = array();
		$serial_no = trim($gadgetOptions['Serial']);
		$item_description = $this->remove_bs(trim($gadget->getProname()));
		$item_sku = $gadget->getSku();
		$warehouse = 'Bhiwandi Warehouse';
		$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress(null,$warehouse);
		$invoice_formated_date = $requestDate = date('d-M-Y',strtotime($gadget->getCreatedAt()));

		$item_description = str_replace('"', '', $item_description);
		$item_description = str_replace('(', '', $item_description);
		$item_description = str_replace(')', '', $item_description);
		$item_description = $this->remove_bs($item_description);
		$hsn = Mage::helper('indiagst')->getHsnBySku($item_sku);
		$price = (int)$gadget->getPrice();
		

		//seller details
		
		$seller_name = $warehouseAddress[0];
		$seller_address =  $warehouseAddress[1].', '.$warehouseAddress[2].' ,'.$warehouseAddress[3];
		$seller_pincode = $warehouseAddress[4];			
		$seller_state = $warehouseAddress[5];			
		$seller_mobile1 = $warehouseAddress[7]; //9833538063			
		$seller_mobile2 = $warehouseAddress[8];	//9769492607		
		
		//customer details
		$email = $gadget->getEmail();
		$customer = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($email);
		
		/// if order confirmed by retailer then pickup from retailers address
		if($gadget->getIsOrderConfirmed() == 'Yes'){ 
			$addressId = $gadget->getConfrmByRetailerAddressId();
		}else{
			$addressId = $gadget->getAddressId();
		}
		$shippingAddress = Mage::getModel('customer/address')->setWebsiteId(1)->load($addressId);
		$revpickup_name = $shippingAddress->getFirstname().' '.$shippingAddress->getLastname();
		$revpickup_street = implode($shippingAddress->getStreet());
		$revpickup_city = $shippingAddress->getCity();
		$revpickup_pincode = $shippingAddress->getPostcode();
		//$revpickup_state = $shippingAddress->getRegion();
		$revpickup_mobile = $shippingAddress->getTelephone();


		$pincodeData = Mage::getModel('operations/serviceablepincodes')->getPincodeData($revpickup_pincode);
		$revpickup_state = $pincodeData['region'];
		
		$rvp_data = '';
		//actual rvp for customer pickup
		$rvp_data = '{ "AWB_NUMBER": "'.implode($awb_no).'", 
		"ORDER_NUMBER": "'.$request_id.'", 
		"PRODUCT": "rev", 
		"REVPICKUP_NAME": "'.$revpickup_name.'", 
		"REVPICKUP_ADDRESS1": "'.$revpickup_street.'", 
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
		"EXTRA_INFORMATION": "Pick up product from customer", 
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
        "ITEM_VALUE": '.$price.',
        "ITEM_CATEGORY": "ELECTRONICS",
		"SELLER_NAME": "'.$seller_name.'",
		"SELLER_ADDRESS": "'.$seller_address.'",
		"SELLER_STATE": "'.$seller_state.'",
		"SELLER_PINCODE": "'.$seller_pincode.'",
		"SELLER_TIN": "",
		"INVOICE_NUMBER": "'.$request_id.'",
		"INVOICE_DATE": "'.$requestDate.'",
		"ESUGAM_NUMBER": "",
		"SELLER_GSTIN": "'.$seller_gst.'",
		"GST_HSN": "'.$hsn.'",
		"GST_ERN": "",
		"GST_TAX_NAME": "DELHI GST",
		"GST_TAX_BASE": 0.0,
        "GST_TAX_RATE_CGSTN": 0.0,
        "GST_TAX_RATE_SGSTN": 0.0,
        "GST_TAX_RATE_IGSTN": 0.0,
        "GST_TAX_TOTAL": 0.0,
        "GST_HSN": null,
        "GST_ERN": null,
        "GST_TAX_CGSTN": null,
        "GST_TAX_SGSTN": null,
        "GST_TAX_IGSTN": null,
        "DISCOUNT": null
        }}';

        //echo $rvp_data;

		$payload = '{ "ECOMEXPRESS-OBJECTS": { "SHIPMENT":'.$rvp_data.'}}';

		$data['username']=$username;
		$data['password']=$password;
		$data['json_input'] = $payload;
		
		$ch = curl_init();                    // initiate curl
		curl_setopt($ch, CURLOPT_URL,$api_url);
		curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // define what you want to post
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
		$output = curl_exec($ch); // execute

		$output_array = json_decode($output,1);
		$airwaybill_response = $output_array['RESPONSE-OBJECTS']['AIRWAYBILL-OBJECTS']['AIRWAYBILL'];
		$shipments = $output_array['shipments'];
		$response_msg = array();
		$response_msg['payload'] = $payload;
		if($airwaybill_response){
			$response_msg['result'] = $airwaybill_response;
			$success = $airwaybill_response['success'];
			if($success == 'False'){
				$response_msg['airwaybill_number'] = $airwaybill_response['airwaybill_number'];
				$response_msg['message'] = $airwaybill_response['error_list']['reason_comment'];
				$response_msg['success'] = 0;
				
			}else if($success == 'True'){
				$response_msg['airwaybill_number'] = $airwaybill_response['airwaybill_number'];
				$response_msg['message'] = $airwaybill_response['error_list']['reason_comment'];
				$response_msg['success'] = 1;
			}			
			
		}

		if($shipments){
			$response_msg['result'] = $shipments;
			$success = $shipments['success'];
			if($success == 'False'){
				$response_msg['airwaybill_number'] = $shipments['airwaybill_number'];
				$response_msg['message'] = $shipments['error_list']['reason_comment'];
				$response_msg['success'] = 1;
				
			}else if($success == 'True'){
				$response_msg['airwaybill_number'] = $shipments['airwaybill_number'];
				$response_msg['message'] = $shipments['error_list']['reason_comment'];
				$response_msg['success'] = 0;
			}else if($shipments[0]['success'] == ''){
				$response_msg['airwaybill_number'] = '';
				$response_msg['message'] = $shipments[0]['reason'];
				$response_msg['success'] = 0;
			}	
		}
		
		return $response_msg;

	}//function ends

	//sell your gadget track reverse pickup
	public function ecomexpressTracking($queryArray)
	{
		    $username = 'amiable92914';
			$pass = 'a4m7ia38b6lel2e3c9tr';
			/*$awb = 207739492; //order tracking number
			$order =  200081509;*/
			$awb = $queryArray['awb_number']; //order tracking number
			$order =  $queryArray['order_id'];

			// $url = "http://plapi.ecomexpress.in/track_me/api/mawbd/?awb=$awb&order=$order&username=$username&password=$pass";
			$url = "http://plapi.ecomexpress.in/track_me/api/mawbd/";

			$postData['awb'] = $awb;
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

			// if($order == 10439){
			// 	echo "<pre>";
			// 	print_r($trackingArray);
			// 	echo "</pre>";
			// }
			$returnTracking = array();
			$scanStatus = array();
			$i = 1;
			$j = 0;
			foreach($trackingArray as $key=>$data)
			{
				$awb = (array) $trackingArray['object'];

				foreach ($data as $newkey => $newvalue) {
					foreach ($newvalue as $key => $value) { 
						$scan = ( array ) $value;

						foreach ($scan as $key => $value) {
							
							if($i % 2 == 0){
								$code = (int) $value[3];
								$scan_label = '';
							if($code > 0){
								
								if(strlen($value[1]) > 1){
									$scan_label = $value[1]; //scan title
								}else{
									$reason_arry = explode("-", $value[2]);
									$scan_label = $reason_arry[1];
								}
								
								if(strlen($scan_label) > 1){
									
									$scanStatus[$j]['date'] = $value[0]; //date time
									$scanStatus[$j]['status'] = $scan_label; //reason title
									$scanStatus[$j]['resion'] = $value[8]; //location
									$scanStatus[$j]['code'] = $value[3]; //reason_code
									$j++;
								}
							}		
								if($j == 5){
									 // break 2; 
								}
							}

							$i++;
						}
					}
				}
			}
			
			if($returnTracking['awb']['tracking_status'] == 'Delivered'){
				$returnTracking['step'] = 'step3';
			}else{
				$returnTracking['step'] = 'step2';
			}

			$returnTracking['awb']['status'] = $awb['field'][12];
			$returnTracking['awb']['tracking_status'] = $awb['field'][11];
			$returnTracking['awb']['receiver'] = $awb['field'][15];
			$returnTracking['awb']['delivery_date'] = Mage::getModel('core/date')->date('g:ia jS F, Y', strtotime($awb['field'][18]));
			$returnTracking['awb']['customer'] = 'ECOM EXPRESS LTD';
			$all_scans = array();
			$scanStatus = array_reverse($scanStatus);
			foreach ($scanStatus as $key => $step) {
				$s = explode(",",$step['date']);
				$date = $s[0];
				$region = $step['resion'];
				$all_scans[$date][$region][] = $step;
			}		
			// $returnTracking['status'] = $scanStatus;
			$returnTracking['status'] = $all_scans;
			$returnTracking['title'] = $queryArray['courier_title'];

			// $returnTracking['ship_date'] = $queryArray['ship_date'];
			// $returnTracking['order_date'] = $queryArray['created_at'];
			//$returnTracking['pickup_addr'] = $queryArray['pickup_addr'];

			

			$returnTracking['ship_date'] = $queryArray['ship_date'];
			$returnTracking['order_date'] = $queryArray['order_date'];
			$returnTracking['ship_addr'] = $queryArray['ship_addr'];
			$returnTracking['order'] = $queryArray['order_id'];
			$returnTracking['awbno'] = $queryArray['awb_number'];

			return $returnTracking;
	}

	//ecom check multiple shipments tracking status
	public function multiShipmentTracking($queryArray){
		
		$username = 'amiable92914';
		$pass = 'a4m7ia38b6lel2e3c9tr';
		$awb = $queryArray['awb_number']; //order tracking number
       	$order =  $queryArray['order_id'];


		//$url = "http://plapi.ecomexpress.in/track_me/api/mawbd/?awb=$awb&order=$order&username=$username&password=$pass";
		$url = "http://plapi.ecomexpress.in/track_me/api/mawbd/";

		$postData['awb'] = $awb;
		$postData['order'] = $order;
		$postData['username'] = $username;
		$postData['password'] = $pass;


		//$live_url = 'http://plapi.ecomexpress.in/track_me/api/mawbd/?awb='.$awb.'&order='.$order.'&username='.$username.'&password='.$pass;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
		$xml = curl_exec($ch);
		$res = simplexml_load_string($xml);
		$trackingArray = (array)$res;

		$tracking_awb = (array) $trackingArray['object'];
		
		$fwd_transit = array(003,004,005,006,100,203,205,303,305,306,304,305,306,307,308,309);
		$fwd_undelivered = array(200,201,202,206,207,208,209,210,212,213,214,215,216,217,221,222,223,224,225,226,227,228,229,231,233,331,332,333,666,888,218,219,220);

		$rvp_pickup_cancelled = array(015);
		$fwd_pickup_cancelled = array(1330);

		//reverse pickup tracking
		$rvp_softdata_uploaded = array(001);
		$rvp_field_pickup = array(450);
		$rvp_outfor_pickup = array(014);
		$rvp_delivered = array(999);
		$rvp_outfor_delivery = array(006);
		
		//forward shipment tracking
		$fwd_softdata_uploaded = array(001);
		
		$fwd_outfor_pickup = array(1230);
		$fwd_field_pickup = array(124);
		
		$fwd_outfor_delivery = array(006);
		$fwd_delivered = array(999);
		$return_array = array();

		if(!empty($tracking_awb)){

			foreach ($tracking_awb as $key => $awb_object) {
				$awb = (array) $awb_object;
				$order_num = $awb['field'][1]; //reason codes
				$tracking_code = $awb['field'][14]; //reason codes
				// $tracking_label = $awb['field'][11]; //connected
				// $tracking_description = $awb['field'][10]; //Bagging completed

				if(in_array($tracking_code, $rvp_softdata_uploaded) || in_array($tracking_code, $fwd_softdata_uploaded)){
					$return_array[$order_num] = 'soft_data_uploaded';
				}else if(in_array($tracking_code, $fwd_outfor_pickup) || in_array($tracking_code, $rvp_outfor_pickup)){
					$return_array[$order_num] = 'out_for_pickup';
				}else if(in_array($tracking_code, $fwd_transit) || in_array($tracking_code, $rvp_outfor_pickup)){
					$return_array[$order_num] = 'in_transit';
				}else if(in_array($tracking_code, $fwd_field_pickup) || in_array($tracking_code, $rvp_field_pickup)){
					$return_array[$order_num] = 'pickup_done';
				}else if(in_array($tracking_code, $fwd_pickup_cancelled) || in_array($tracking_code, $fwd_pickup_cancelled)){
					$return_array[$order_num] = 'pickup_cancelled';
				}else if(in_array($tracking_code, $fwd_outfor_delivery) || in_array($tracking_code, $rvp_outfor_delivery)){
					$return_array[$order_num] = 'out_for_delivery';
				}else if(in_array($tracking_code, $fwd_delivered) || in_array($tracking_code, $rvp_delivered)){
					$return_array[$order_num] = 'delivered';
				}
			}
		}

		return $return_array;
	}
}
?>
