<?php //check ecom payload
require_once('../app/Mage.php'); 
Mage::app();			
	//84665,84663,84459,84667
			$order = Mage::getModel("sales/order")->load(84667);
			$incrementId = $order->getIncrementId();

			$invoiceCollection = $order->getInvoiceCollection();
			foreach($invoiceCollection as $invoice){
			    //var_dump($invoice);
			    $invoice_id =  $invoice->getId();
			    // $invoiceIncrementId =  $invoice->getIncrementId();
			}
			$invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
			$orderId = $order->getEntityId();
			$invoice_number = $invoice->getIncrementId();
			$invoice_date = $invoice->getCreatedAt();
			$invoice_date_format = date('dMY',strtotime($invoice_date));
			$total_qty = (int) $invoice->getTotalQty();
			$ecom_gstdetails = Mage::helper('indiagst')->getEcomGstDetails($invoice_id);
			$sellergstin = Mage::helper('indiagst')->sellerGstIn($orderId);

			// echo "<pre>"; 
			// print_r($order->getData());
			// echo "</pre>";
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

			$item_description = rtrim($item_description,'+');

			$shipping_address = $order->getShippingAddress();
            $customer_name = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
            $amount = 0 ;
			$payment_method = $order->getPayment()->getMethodInstance()->getCode();
			echo $payment_method;
			$product = 'PPD';
			if($payment_method == "cashondelivery") {
				$product = 'COD';
				$amount = (int)$invoice->getGrandTotal();
			}
			if($amount == 0)
			{
				$amount = 1;
			}

			//echo $amount;exit; 
			$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress($invoice->getorder_id());
			$shipping = $shipping_address->getData();
			
			//street character correction
			$street = $shipping['street'];
			$street = preg_replace('#\s+#',',',trim($street));
			
			$warehouse0 = $warehouseAddress[0];
			$warehouse1 = $warehouseAddress[1];
			$warehouse2 = $warehouseAddress[2];
			$warehouse7 = $warehouseAddress[7];
			$warehouse8 = $warehouseAddress[8];
			$city = $shipping['city'];
			$telephone = $shipping['telephone'];
			$region = $shipping['region'];
			$postcode = $shipping['postcode'];
			//validate post code
			$ecom_pincodes_str = Mage::getStoreConfig('shipping/ecom/serviceable_pincodes');
			$ecom_pincodes_str_other = Mage::getStoreConfig('shipping/ecom/serviceable_pincodes_other');
			$ecom_pincodes_str_other_two = Mage::getStoreConfig('shipping/ecom/serviceable_pincodes_other_two');
			$ecom_pincodes = explode(",", $ecom_pincodes_str);
			$ecom_pincodes_other = explode(",", $ecom_pincodes_str_other);
			$ecom_pincodes_other_two = explode(",", $ecom_pincodes_str_other_two);
			$pincode_response = array();
			$check_postcode = array_merge($ecom_pincodes,$ecom_pincodes_other,$ecom_pincodes_other_two);
			if(!in_array($postcode, $check_postcode)){
				$pincode_response = array();
				$pincode_response[] = array(
					"error" => true,
					"message"   =>  'This pincode is not serviceable in Ecom Shipment'
				);
				return $pincode_response;
				exit;
			}
			$wh_postcode = $warehouseAddress[4];

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
			// $awb_no = 0;
			// if($product == 'COD'){
			// 	$awb_cod = Mage::getModel('ecom/codawb')->getCollection();
		 //        $awb_cod->addFieldToFilter('status',array('eq'=>'0'));
		        
		 //        $awb_no_data = $awb_cod->getFirstItem();

		 //        if($awb_no_data->getData()){
		 //        	$awb_no = $awb_no_data->getAwb();
		 //        	$disable_awb = Mage::getModel('ecom/codawb')->load($awb_no_data->getId());
		 //        }else{
		 //        	$error_response = array();
			// 		$error_response[] = array(
			// 			"error" => true,
			// 			"message"   =>  'COD AWB Numbers are used up, refill AWB numbers for COD orders'
			// 		);
			// 		return $error_response;
			// 		exit;
		 //        }
		       
			// }else{
			// 	$awb_ppd = Mage::getModel('ecom/ppdawb')->getCollection();
		 //        $awb_ppd->addFieldToFilter('status',array('eq'=>'0'));
		        
		 //        $awb_no_data = $awb_ppd->getFirstItem();

		 //        if($awb_no_data->getData()){
		 //        	$awb_no = $awb_no_data->getAwb();
		 //        	$disable_awb = Mage::getModel('ecom/ppdawb')->load($awb_no_data->getId());
		 //        }else{
		 //        	$error_response = array();
			// 		$error_response[] = array(
			// 			"error" => true,
			// 			"message"   =>  'COD AWB Numbers are used up, refill AWB numbers for COD orders'
			// 		);
			// 		return $error_response;
			// 		exit;
		 //        }
			// }

			$awb_no = 8554646;
			if($awb_no == 0){
				$error_response = array();
					$error_response[] = array(
						"error" => true,
						"message"   =>  'AWB Numbers are used up, please refill AWB Numbers'
					);
					return $error_response;
					exit;
			}

			// Mage::log($awb_request, 1, 'ecom_op.log', true);
			//initiate curl call for awb 
			/*$awbch = curl_init();                    // initiate curl
			curl_setopt($awbch, CURLOPT_URL,$liveawburl);
			curl_setopt($awbch, CURLOPT_POST, true);  // tell curl you want to post something
			curl_setopt($awbch, CURLOPT_POSTFIELDS, $awb_request); // define what you want to post
			curl_setopt($awbch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
			$awb_json = curl_exec($awbch); // execute
			//close connection
			curl_close($awbch);
			// Mage::log($awb_json, 1, 'ecom_op.log', true);
			//get awb number from output
			$awb_data = json_decode($awb_json,1);

			foreach ($awb_data['awb'] as $key => $val) {
				$awb_no = $val;
			}*/			

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
			
			$data['json_input']='[{"AWB_NUMBER":"'.$awb_no.'","ORDER_NUMBER":"'.$incrementId.'","PRODUCT":"'.$product.'","CONSIGNEE":"'.$customer_name.'","CONSIGNEE_ADDRESS1":"'.$street.'","CONSIGNEE_ADDRESS2":"ADD2","CONSIGNEE_ADDRESS3":"ADD3","DESTINATION_CITY":"'.$city.'","PINCODE":"'.$postcode.'","STATE":"'.$region.'","MOBILE":"'.$telephone.'","TELEPHONE":"'.$telephone.'","ITEM_DESCRIPTION":"'.$item_description.'","PIECES":"'.$total_qty.'","COLLECTABLE_VALUE":"'.$amount.'","DECLARED_VALUE":"'.$amount.'","ACTUAL_WEIGHT":"5","VOLUMETRIC_WEIGHT":"0","LENGTH":"10","BREADTH":"10","HEIGHT":"10","PICKUP_NAME":"'.$warehouse0.'","PICKUP_ADDRESS_LINE1":"'.$warehouse1.'","PICKUP_ADDRESS_LINE2":"'.$warehouse2.'","PICKUP_PINCODE":"'.$wh_postcode.'","PICKUP_PHONE":"'.$warehouse7.'","PICKUP_MOBILE":"'.$warehouse8.'","RETURN_PINCODE":"'.$wh_postcode.'","RETURN_NAME":"'.$warehouse0.'","RETURN_ADDRESS_LINE1":"'.$warehouse1.'","RETURN_ADDRESS_LINE2":"'.$warehouse2.'","RETURN_PHONE":"'.$warehouse7.'","RETURN_MOBILE":"'.$warehouse8.'","DG_SHIPMENT": "false",'.$additional_info.'}]';

			echo "<pre>";
			print_r($data);

?>
