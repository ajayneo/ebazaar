<?php
class Neo_Customapiv6_TrackController extends Neo_Customapiv6_Controller_HttpAuthController
{
	
	public function trackyourorderAction()
	{
		//$_REQUEST['order_id']

		//if(empty($_REQUEST['firstname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['firstname'])) {
		//	echo json_encode(array('status' => 0, 'message' => 'Please provide valid first name.'));
		//	exit;
		//}
		try{

			// $orderIncrementId = '200046266'; //bluedart
			// $orderIncrementId = '200080759'; //bluedart 24th oct17 NF
			// $orderIncrementId = '200079986'; //bluedart 16th oct17 Del
			// $orderIncrementId = '200043676'; //ecom
			// $orderIncrementId = '200068307'; //ecom
		if(!empty($_REQUEST['order_id'])){
			
			$order_id = $_REQUEST['order_id'];
			if(substr($order_id, 0, 4) == 2000){
				$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
			}else{
				$order = Mage::getModel('sales/order')->load($order_id);
			}

			$shipmentCollection = $order->getShipmentsCollection();
			Mage::getSingleton('core/translate')->init('en_US', true); 
			$queryArray = array();
			$queryArray['step'] = 'step1';
			$queryArray['order_date'] = Mage::getModel('core/date')->date('g:ia jS F, Y', strtotime($order->getCreatedAt()));
			
			if(is_object($order->getShippingAddress())){
				$shipping = $order->getShippingAddress()->getFormated();

				foreach($shipmentCollection as $shipment){
				        $shipmentIncrementId = $shipment->getIncrementId();
				        $shipment=Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);
			            foreach ($shipment->getAllTracks() as $track) {

			               $queryArray['track_number'] = $track->getTrackNumber();
			               $queryArray['order_increment_id'] = $orderIncrementId;
			               $queryArray['order_id'] = $order_id;
			               $queryArray['title'] = $track->getTitle();
			               $queryArray['carrier_code'] = $track->getCarrierCode();
	       	               $queryArray['step'] = 'step2';
	       	               $queryArray['ship_date'] = Mage::getModel('core/date')->date('g:ia jS F, Y', strtotime($track->getCreatedAt()));
		               	   $queryArray['ship_addr'] = $shipping;


			               break 2; 
			            }

	          	}
	         }

          	// print_r($queryArray);

          	if($queryArray['title'] == 'ecom_express' || $queryArray['carrier_code'] == 'ecom_express' || $queryArray['carrier_code'] == 'ecom'){
          		$returnTracking = $this->ecomexpress($queryArray);
          	}elseif($queryArray['title'] == 'bluedart' || $queryArray['title'] == 'Bluedart'){
          		$returnTracking = $this->bluedart($queryArray);
          	}else{
		      	$queryArray['text1'] = 'Order Placed';
		      	$queryArray['text2'] = 'Dispatched';
		      	$queryArray['text3'] = 'Delivered';
		      	$queryArray['customer'] = '';
		      	$queryArray['message'] = 'Order placed and we are processing';
		      	$queryArray['ship_date'] = '';
		      	$queryArray['tracking_details'] = array();
		      	$queryArray['delivery_date'] = '';
		      	$queryArray['ship_addr'] = '';
		      	$queryArray['awb'] = array();
		      	$queryArray['awb']['customer'] = '';
				$queryArray['awb']['status'] = '';
				$queryArray['awb']['tracking_status'] = '';
				$queryArray['awb']['receiver'] = '';
				$queryArray['awb']['delivery_date'] = '';

		      	$returnTracking = $queryArray;
		    }
          	// header('Content-type:application/json;charset=utf-8');
			
        }
			if($returnTracking){
				echo json_encode(array('status' => 1, 'data' => $returnTracking));
				exit;
			}else{
				echo json_encode(array('status' => 0, 'message' => 'Data Not found.'));
				exit;
			}



			

		}catch(Exception $ex){

		}
	}


	protected function ecomexpress($queryArray)
	{
		    $username = 'amiable92914';
			$pass = 'a4m7ia38b6lel2e3c9tr';
			$awb = $queryArray['track_number'];
			$order = $queryArray['order_increment_id'];

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

			$returnTracking = array();
			$scanStatus = array();
			//$xml = new SimpleXMLElement($xml);
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
								if($code > 0){
								$scanStatus[$j]['date'] = $value[0];
								$scanStatus[$j]['status'] = $this->__($value[1]);
								$scanStatus[$j]['region'] = $value[8];
								$scanStatus[$j]['code'] = $code;
								$j++;
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



			$returnTracking['awb']['customer'] = 'ECOM EXPRESS LTD';
			$returnTracking['awb']['status'] = $awb['field'][10];
			$returnTracking['message'] = '';
			if(is_object($awb['field'][11])){
				$returnTracking['awb']['tracking_status'] = '';
				$returnTracking['message'] = 'Shipment data shared to courier company';
			}else{
				$returnTracking['awb']['tracking_status'] = $awb['field'][11];
				$returnTracking['message'] = $awb['field'][11];
			}

			if(is_object($awb['field'][15])){
				$returnTracking['awb']['receiver'] = '';
			}else{
				$returnTracking['awb']['receiver'] = $awb['field'][15];
			}

			if(is_object($awb['field'][18])){
				$returnTracking['awb']['receiver'] = '';
			}else{
				$returnTracking['awb']['delivery_date'] = $awb['field'][18];
			}

			
			
			$returnTracking['status'] = $scanStatus;
			$all_scans = array();
			foreach ($scanStatus as $key => $step) {
				$s = explode(",",$step['date']);
				$date = $s[0];
				$region = $step['region'];
				$all_scans[$date][$region][] = $step;
			}

			// echo "<pre>";
			// print_r($all_scans);
			$track_labels = array();
			$io = 0;
			foreach ($all_scans as $key => $city) {
				$track_labels[$io]['date'] = $key;
				foreach ($city as $key => $value) {
					$track_labels[$io]['city'] = $key;
					$track_labels[$io]['scans'] = $value;
				}
				$io++;
			}

			// exit;

			// $returnTracking['status'] = $scanStatus;
			// $returnTracking['tracking_details'] = $all_scans;
			$returnTracking['tracking_details'] = $track_labels;

			if($returnTracking['awb']['tracking_status'] == 'Delivered'){
				$returnTracking['step'] = 'step3';
			}else{
				$returnTracking['step'] = $queryArray['step'];
			}

			$returnTracking['delivery_date'] = $returnTracking['awb']['delivery_date'];
			$returnTracking['customer'] = 'ECOM EXPRESS LTD';
			$returnTracking['ship_date'] = $queryArray['ship_date'];
			$returnTracking['order_date'] = $queryArray['order_date'];
			$returnTracking['ship_addr'] = $queryArray['ship_addr'];
			$returnTracking['text1'] = 'Order Placed';
			$returnTracking['text2'] = 'Dispatched';
			$returnTracking['text3'] = 'Delivered';
			// $returnTracking['message'] = '';

			return $returnTracking;
	}


	protected function bluedart($queryArray)
	{
		$lickey = '4ecbd06dc0b9737d69120029cb05c9df';
		$loginid = 'BOM00001';
		$awbnumbers = $queryArray['track_number'];
		$url = "http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=$loginid&awb=awb&numbers=$awbnumbers&format=xml&lickey=$lickey&verno=1.3f&scan=1";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
		$xml = curl_exec($ch);
		$res = simplexml_load_string($xml);
		$trackingArray = (array)$res;
		// echo "<pre>";
		// print_r($trackingArray); exit;
		$returnTracking = array();
		$scanStatus = array();

		// $xml = new SimpleXMLElement($xml);

		$i = 1;
		$j = 0;
		foreach($trackingArray as $key=>$data)
		{
			$awb = (array) $trackingArray['Shipment'];

			// foreach ($data as $newkey => $newvalue) {
			// 	foreach ($newvalue as $key => $value) { 
			// 		$scan = ( array ) $value;

			// 		$scanStatus[$j]['date'] = $scan['ScanDate'].' '.$scan['ScanTime'];
			// 		$scanStatus[$j]['status'] = $this->__($scan['Scan']);
			// 		$scanStatus[$j]['resion'] = $scan['ScannedLocation'];

			// 		if($i == 5){
			// 			 break 2; 
			// 		}

			// 		$j++;
			// 		$i++;
			// 	}
			// }

			$scans = $awb['Scans'];
		}
		// echo "<pre>";
		// print_r($awb);

		$scan_cnt = 0;
		// $scanStatus = array();
		// foreach ($scans as $key => $value) {
		// 	$scanStatus[$scan_cnt]['date'] = (string) $value->ScanDate;
		// 	$scanStatus[$scan_cnt]['time'] = (string) $value->ScanTime;
		// 	$scanStatus[$scan_cnt]['status'] = (string) $value->Scan;
		// 	$scanStatus[$scan_cnt]['resion'] = (string) $value->ScannedLocation;
		// 	$scanStatus[$scan_cnt]['code'] = (string) $value->ScanCode;
		// 	$scan_cnt++;
		// }

		$all_scans = array();
		foreach ($scans as $key => $value) {
			// var_dump($value);
			$date = (string) $value->ScanDate;
			$s = explode(",",$date);
			$date = $s[0];
			$region = (string) $value->ScannedLocation;
			$all_scans[$date][$region][] = $value;
		}

		// echo "<pre>";
		// print_r($all_scans);
		$track_labels = array();
		$io = 0;
		foreach ($all_scans as $key => $city) {
			$track_labels[$io]['date'] = $key;
			foreach ($city as $key => $value) {
				$track_labels[$io]['city'] = $key;
				// $track_labels[$io]['scans'] = $value;
				// var_dump($value); exit;
				// "date": "18 Jul, 2017, 08:33 ",
				// "status": "Shipment arrived at Courier facility",
				// "region": "HYDERABAD",
				// "code": 5
				$details = array();
				$k = 0;
				foreach ($value as $key => $det) {
					$details[$k]['date'] = (string) $det->ScanDate.",".(string) $det->ScanTime;
					$details[$k]['region'] = (string) $det->ScannedLocation;
					$details[$k]['code'] = (string) $det->ScanCode;
					$details[$k]['status'] = (string) $det->Scan;
					$k++;
				}
				$track_labels[$io]['scans'] = $details;
			}
			$io++;
		}

		// print_r($track_labels);
		// $returnTracking['awb']['customer'] = $awb['CustomerName'];
		$returnTracking['awb']['status'] = $awb['StatusType'];
		$returnTracking['awb']['tracking_status'] = $awb['Status'];
		$returnTracking['awb']['receiver'] = $awb['ReceivedBy'];

		//on delievered show message delivered on 


		$returnTracking['status'] = '';
		// $returnTracking['status'] = $scanStatus;

		$returnTracking['text3'] = '';
		$returnTracking['message'] = '';
		
		if($awb['Status'] == 'SHIPMENT DELIVERED' || $awb['StatusType'] == 'DL'){
			$status_date = $awb['StatusDate'];
			$status_time = $awb['StatusTime'];
			$returnTracking['delivery_date'] = "Delivered on ".$status_date." ".$status_time;
			$returnTracking['step'] = 'step3';
			$returnTracking['text3'] = 'Delivered';
		}else if($awb['ExpectedDeliveryDate'] && $awb['StatusType'] !== 'DL'){
			$returnTracking['delivery_date'] = "Expected Delivery Date: ". $awb['ExpectedDeliveryDate'];
			$returnTracking['step'] = 'step2e';
		}else if($awb['StatusType'] == 'NF'){
			$returnTracking['delivery_date'] = "Shipment delayed due to external factors";
			$returnTracking['message'] = 'Delayed due to external factors';
			$returnTracking['step'] = 'step2e';
		}else{
			$returnTracking['step'] = $queryArray['step'];
		}
		

		$returnTracking['text1'] = 'Order Placed';
		$returnTracking['text2'] = ' Dispatched';
		
		$returnTracking['customer'] = 'BY BLUEDART SHIPMENT';
		$returnTracking['ship_date'] = $queryArray['ship_date'];
		$returnTracking['order_date'] = $queryArray['order_date'];
		$returnTracking['ship_addr'] = $queryArray['ship_addr'];
		$returnTracking['tracking_details'] = $track_labels;

		return $returnTracking;
	}

	public function trackyourorderidAction()
	{
        //$_REQUEST['email'] = 'rakshita.hegde@wwindia.com'; 
		if(!empty($_REQUEST['order_id']))
		{

			$customer_id = '';				
			$order_id = $_REQUEST['order_id'];
			$orders = '';			
			$orders = $order = Mage::getModel('sales/order')->loadByIncrementId($order_id)->getId();          	    						            
            if($orders != '')
            {

					if($_REQUEST['email'] != '' || $_REQUEST['mobile'] != '')
					{

					   	$order_collection = Mage::getModel('sales/order')->getCollection()
					    						->addFieldToFilter('increment_id',$_REQUEST['order_id'])
					    						->getFirstItem();						
				        $tele = '1';
				        if($order_collection->getShippingAddress()){
				            $tele = $order_collection->getShippingAddress()->getTelephone();
				        }		    	 
				    			if($order_collection['customer_email'] == $_REQUEST['email'] || $tele == $_REQUEST['mobile'])
				    			{
		                                 $orders = Mage::getModel('sales/order')->getCollection()
						                 ->addAttributeToFilter('increment_id', $_REQUEST['order_id']);

		                                 if($order_collection->getCustomerId())
		                                 {
		                                   echo json_encode(array("status" => 1, "userid" => $order_collection->getCustomerId()));exit;						     
		                                 }
		                                 else
		                                 {
		                                 	echo json_encode(array("status" => 0, "message" => 'Order id not valid.'));exit;
		                                 }
				    			}else{                        
				    				echo json_encode(array("status" => 0, "message" => 'Invalid email id or mobile no.'));exit;		    				
				    			}

					}
					else
					{
			           echo json_encode(array("status" => 0, "message" => 'Please enter email id or mobile no.'));
			           exit;			
					}
            }
            else
            {		    				    		
		      echo json_encode(array("status" => 0, "message" => 'Order id does not exist.'));exit;		    				    	
		    }
		}
		else
		{
           echo json_encode(array("status" => 0, "message" => 'Please enter order number.'));
           exit;			
		}		
	}		
} 