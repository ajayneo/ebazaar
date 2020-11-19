<?php
    class Neo_Shippinge_Model_Cron {

		public function updateOrderDeliverStatus(){
			Mage::log('Start of the Cron',null,'FedexNewout.log');
			
			require_once(Mage::getModuleDir("", "Neo_Shippinge").DS."wsdl".DS."fedex-common.php5");
			$path_to_wsdl = Mage::getModuleDir("", "Neo_Shippinge").DS."wsdl".DS."TrackService_v9.wsdl";
			ini_set("soap.wsdl_cache_enabled", "0");
			$client = new SoapClient($path_to_wsdl, array('trace' => 1));
			$orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('trackstatuscode','0');
			$final_array_status = array('Delivered','Delivery exception');
			$allowed_title = array("STANDARD_OVERNIGHT","FEDEX_EXPRESS_SAVER");
			//$shippinge_model = Mage::getModel('shippinge/shippinge');
			foreach($orders as $order){
				foreach($order->getShipmentsCollection() as $shipment){
					foreach($shipment->getAllTracks() as $tracking_number){
						$title = $tracking_number->getTitle();
						if(in_array($title, $allowed_title)){
							$track_number = $tracking_number->getTrackNumber();
							Mage::log($order->getIncrementId(),null,'FedexNewout.log');
							Mage::log($track_number,null,'FedexNewout.log');
							$request['WebAuthenticationDetail'] = array(
								'UserCredential' =>array(
									'Key' => '4Vlh08EoUsB4K14q',
									'Password' => 'NL1ts4iuFKMUsfrlsKEDyFasb'
								)
							);
							$request['ClientDetail'] = array(
								'AccountNumber' => '621168169',
								'MeterNumber' => '107487887'
							);
							$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Track Request using PHP ***');
							$request['Version'] = array(
								'ServiceId' => 'trck', 
								'Major' => '9', 
								'Intermediate' => '1', 
								'Minor' => '0'
							);
							$request['SelectionDetails'] = array(
								'PackageIdentifier' => array(
									'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
									'Value' => $track_number
								)
							);
							
							try {
								if(setEndpoint('changeEndpoint')){
									$newLocation = $client->__setLocation(setEndpoint('endpoint'));
								}
								
								$response = $client ->track($request);
							    /*if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
									if($response->HighestSeverity != 'SUCCESS'){
										echo '<table border="1">';
										echo '<tr><th>Track Reply</th><th>&nbsp;</th></tr>';
										trackDetails($response->Notifications, '');
										echo '</table>';
									}else{
								    	if ($response->CompletedTrackDetails->HighestSeverity != 'SUCCESS'){
											echo '<table border="1">';
										    echo '<tr><th>Shipment Level Tracking Details</th><th>&nbsp;</th></tr>';
										    trackDetails($response->CompletedTrackDetails, '');
											echo '</table>';
										}else{
											echo '<table border="1">';
										    echo '<tr><th>Package Level Tracking Details</th><th>&nbsp;</th></tr>';
										    trackDetails($response->CompletedTrackDetails->TrackDetails, '');
											echo '</table>';
										}
									}
							       printSuccess($client, $response);
							    }else{
							        printError($client, $response);
							    }*/

								$track_status = $response->CompletedTrackDetails->TrackDetails->StatusDetail->Description;
								$save_data = array();
								
								/*$save_data['order_id'] = $tracking_number->getOrderId();
								$save_data['order_inc'] = $order->getIncrementId();
								$save_data['customer_id'] = $order->getCustomerId();
								$save_data['tracking_no'] = $track_number;
								$save_data['track_status'] = $track_status;
								$save_data['tracking_title'] = $tracking_number->getTitle();
								if($track_status == 'Delivery exception'){
									$actionstatus = $response->CompletedTrackDetails->TrackDetails->StatusDetail->AncillaryDetails->ActionDescription;
									$save_data['actionstatus'] = $actionstatus;
								}
								
								Mage::getModel('shippinge/shippinge')->savetrack($save_data);*/
								
								/*$ship_id = $shippinge_model->unsetData()->load($order->getId(),'order_id');
								if($ship_id->getShippingeId()){
									Mage::log('Update Order Track Status of '.$order->getId(),null,'if.log');
									if($track_status == 'Delivery exception'){
										$actionstatus = $response->CompletedTrackDetails->TrackDetails->StatusDetail->AncillaryDetails->ActionDescription;
										$ship_id->setActionstatus($actionstatus);
									}
									$ship_id->setTrackStatus($track_status)->save();
								}else{
									$save_data['order_id'] = $tracking_number->getOrderId();
									$save_data['order_inc'] = $order->getIncrementId(); 
									$save_data['customer_id'] = $order->getCustomerId();  
									$save_data['tracking_no'] = $track_number;
									$save_data['track_status'] = $track_status;
									$save_data['tracking_title'] = $tracking_number->getTitle();
									if($track_status == 'Delivery exception'){
										$save_data['actionstatus'] = $response->CompletedTrackDetails->TrackDetails->StatusDetail->AncillaryDetails->ActionDescription;
									}
									$shippinge_model->unsetData()->setData($save_data)->save();	
								}

								if(in_array($track_status, $final_array_status)){
									$order->setTrackstatuscode('1');
									$order->save();
								}*/

								Mage::log($response->CompletedTrackDetails->TrackDetails->StatusDetail->Description,null,'FedexNewout.log');
							    //writeToLog($client);    // Write to log file
							} catch (SoapFault $exception) {
							    printFault($exception, $client);
							}
						}
				   }
				}
			}
			Mage::log('End of the Cron',null,'FedexNewout.log');
		}
    }
?>