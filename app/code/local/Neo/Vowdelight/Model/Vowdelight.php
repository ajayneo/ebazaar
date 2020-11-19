<?php class Neo_Vowdelight_Model_Vowdelight extends Mage_Core_Model_Abstract{

    protected function _construct(){



       $this->_init("vowdelight/vowdelight");



    }
	
	//send ecom reverse pickup
    public function ecom_reverse_pickup($req_id){
		if(!$req_id){
			return false;
		}
		$vowResource = Mage::getResourceModel('vowdelight/vowdelight_collection');
		$vowResource->addFieldToFilter('request_id',$req_id);
		$vowOldImei = $vowResource->addFieldToSelect(array('request_id','old_order_no','old_imei_no','sku'));
		
		$test_url =  'http://staging.ecomexpress.in/apiv2/manifest_awb_rev_v2/';
		$testusername = 'ecomexpress';
		$testpassword = 'Ke$3c@4oT5m6h#$';

		$live_url = 'http://api.ecomexpress.in/apiv2/manifest_awb_rev_v2/';
		$username ="amiable92914";
		$password ="a4m7ia38b6lel2e3c9tr";
		
		$response_msg = array();
		foreach ($vowOldImei as $vow) {
			//load product
			if($i == 0){
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $vow->getSku());
				$price =  $product->getPrice() + (12/100*$product->getPrice());
				$item_description = $product->getName();
			}
			
			//get order address
			$order_no  = $vow->getOldOrderNo();
			$serial_no = $vow->getOldImeiNo();
			$_order    = Mage::getModel('sales/order')->loadbyIncrementId($order_no);
			$_address  = $_order->getShippingAddress();
			$revpickup_name = $_address->getFirstname().' '.$_address->getLastname();
			$revpickup_address1 = $_address->getStreet();
			$revpickup_city = $_address->getCity();
			$revpickup_pincode = $_address->getPostcode();
			// $revpickup_pincode = 111111;
			$revpickup_state = $_address->getRegion();
			$revpickup_mobile = $_address->getTelephone();
			$order_id = $_order->getEntityId();

			//seller address
			$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress($order_id);
			$seller_name = $warehouseAddress[0];
			$seller_address =  $warehouseAddress[1].', '.$warehouseAddress[2].' ,'.$warehouseAddress[3];
			$seller_pincode = $warehouseAddress[4];			
			// $seller_pincode = 111111;			
			$seller_state = $warehouseAddress[5];			
			$seller_mobile1 = $warehouseAddress[7]; //9833538063			
			$seller_mobile2 = $warehouseAddress[8];	//9769492607
			
			//generate AWB Numbers
			$awb_numbers = Mage::getModel('shippinge/ecom')->rvpawb(1);
			$awb_no = $awb_numbers[0];
			
			//create json payload for reverse pickup
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
			"DECLARED_VALUE": "'.$price.'", 
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

			//create data for curl request
			$payload = '{ "ECOMEXPRESS-OBJECTS": { "SHIPMENT":'.$rvp_data_live.'}}';
			$data['username']=$username;
			$data['password']=$password;
			$data['json_input'] = $payload;
			
			//send curl request
			$ch = curl_init();                    // initiate curl
			curl_setopt($ch, CURLOPT_URL,$live_url);
			curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // define what you want to post
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
			$output = curl_exec($ch); // execute

			//response object
			$output_array = json_decode($output,1);
			$airwaybill_response = $output_array['RESPONSE-OBJECTS']['AIRWAYBILL-OBJECTS']['AIRWAYBILL'];
			
			//shipment object
			$shipments = $output_array['shipments'];
			
			//prepare response messages
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
					
					//set AWB number in vow delight model with paylod
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
		}//end of for each vow

		return $response_msg;
	}//end of function

}

	 