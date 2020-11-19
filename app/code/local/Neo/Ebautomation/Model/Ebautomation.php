<?php
class Neo_Ebautomation_Model_Ebautomation extends Mage_Core_Model_Abstract
{
	/// Credit Days Payment Method Order Process
	public function orderProcessAutomation($orderId = NULL){

		ini_set("memory_limit",-1);
		
		$to = gmdate("Y-m-d H:i:s");
		$lastTime = strtotime('-96 hour');//strtotime($to) - 3600;
		$from = gmdate('Y-m-d H:i:s', $lastTime);
		

		if($orderId != NULL){
			$orders = Mage::getModel('sales/order')->getCollection();
			$orders->addFieldToFilter('entity_id',$orderId);

		}else{
			$orders = Mage::getModel('sales/order')->getCollection();
			$orders->getSelect()->join(
			    array('p' => $orders->getResource()->getTable('sales/order_payment')),
			    'p.parent_id = main_table.entity_id',
			    array()
			);
			$orders->addFieldToFilter('method',array('in'=>array('banktransfer','cashondelivery')));
			$orders->addFieldToFilter('state','new');
			$orders->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
		}


		/*echo $orders->getSelect();
		print_r($orders->getColumnValues('increment_id'));
		die;*/

		
		$phicomm_skus = array('Mobile3351','Mobile3350');
		foreach ($orders as $order) {
			$orderId = $order->getIncrementId();
			$order_skus = array();
			foreach ($order->getAllItems() as $item) {
				$order_skus[] = $item->getSku();
			}
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			if(Mage::getStoreConfig('ebautomation/ebautomation_credentials/enable')){
				if(!$customer->getNavMapStatus()){
					Mage::getModel('ebautomation/customer')->mapCustomerInNavision($customer);
				}
			}
			
			
			
				/// Do not process for Phicomm products found in order
				if(!array_intersect($phicomm_skus, $order_skus)){

					if($order->canInvoice()){
						$this->createInvoiceByOrder($orderId);
					}
					
					if($order->canShip()){
						$this->createShipment($orderId,$customer);
						//$this->sendPackagingShipments($orderId);
					}
					
				}
			
			

		}
	}


	public function prepaidOrderProcessAutomation($orderId = NULL){
		
		$to = gmdate("Y-m-d H:i:s");
		//$lastTime = strtotime('-5 days');
		$from = gmdate('2017-10-01 00:00:00');
		
		if($orderId != NULL){
			$orders = Mage::getModel('sales/order')->getCollection();
			$orders->addFieldToFilter('entity_id',$orderId);

		}else{
			$orders = Mage::getModel('sales/order')->getCollection();
			$orders->getSelect()->join(
			    array('p' => $orders->getResource()->getTable('sales/order_payment')),
			    'p.parent_id = main_table.entity_id',
			    array()
			);
			$orders->addFieldToFilter('status','financeapproved');
			$orders->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
		}
		$phicomm_skus = array('Mobile3351','Mobile3350');
		foreach ($orders as $order) {
			$orderId = $order->getIncrementId();
			$order_skus = array();
			foreach ($order->getAllItems() as $item) {
				$order_skus[] = $item->getSku();
			}
			
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			if(Mage::getStoreConfig('ebautomation/ebautomation_credentials/enable')){
				if(!$customer->getNavMapStatus()){
					Mage::getModel('ebautomation/customer')->mapCustomerInNavision($customer);
				}
			}
			
			
			
				/// Do not process for Phicomm products found in order
				if(!array_intersect($phicomm_skus, $order_skus)){

					if($order->canInvoice()){
						//echo "Sonali";die;
						$this->createInvoiceByOrder($orderId);
					}
					
					if($order->canShip()){
						$this->createShipment($orderId,$customer);
						//$this->sendPackagingShipments($orderId);
					}
					
				}
			
			

		}
	}


	/*******To check all ordered item get shipped to update order status "COD Payment Pending"*******************/
	public function getAllQtyShippedStatus($orderId){
		$totalQtyOrdered = 0; $totalQtyShipped = 0; $totalQtyInvoiced = 0;
		$orderItems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id',$orderId);

		foreach ($orderItems as $orderItem) {
			$totalQtyOrdered = $totalQtyOrdered + $orderItem->getQtyOrdered();
			$totalQtyInvoiced = $totalQtyInvoiced + $orderItem->getQtyInvoiced();
			$totalQtyShipped = $totalQtyShipped + $orderItem->getQtyShipped();
			
		}
		$qtyArray = array($totalQtyOrdered,$totalQtyInvoiced,$totalQtyShipped);
		//print_r($qtyArray);die;
		if(count(array_unique($qtyArray)) == 1){
			return true;
		}else{
			return false;
		}
	}


	public function createInvoiceByOrder($increment_id){
				$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
				
				$productData = array(); $skuData = array();$warehouse = array();
				foreach($order->getAllItems() as $item) {
					
			   		$sku = $item->getSku();
			   		$skuData[] = $sku;
					$item_id = $item->getItemId(); //order_item_id
					$qty = $item->getQtyOrdered();   //qty ordered 
					
					$allProductItems[] =  $item_id;  /// Creating all products array


					$stock = Mage::getModel('ebautomation/stockchennai')->getCollection()
					->addFieldToFilter('sku',$sku);
					//->addFieldToFilter('qty',array('gteq'=>$qty));
					$stock = $stock->getData();
					if(count($stock)>0)
					{
						$warehouse['Tamilnadu Warehouse'][$item_id] = $qty;
					}
					else
					{
						//$warehouse['Bhiwandi Warehouse'][$item_id] = $qty;
						$warehouse['Nerul Warehouse'][$item_id] = $qty;
					}
					
				} 

				if(count($warehouse) > 1){
					$order->addStatusHistoryComment('Order will not process as order related to multiple warehouse.', false);
 					$order->save();
 					return;
				}

				foreach ($warehouse as $key => $value) {
					$stockloc = $key; ///Warehouse
					$warehouseProduct = array_keys($value);  // Items of warehouse
					foreach ($allProductItems as $productItem) {
						if(in_array($productItem,$warehouseProduct)){
							$productData[$productItem] = $value[$productItem];
						}else{
							$productData[$productItem] = 0;
						}
					}
					$this->createInvoice($order,$productData,$stockloc); /// Call to create invoice
					$order->save();
				}
				
		

			}

			

			public function createInvoice($order,$productData,$stockloc){
				try{
					$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($productData);
					
					$invoice->register();

					if($order->getPayment()->getMethod() != 'cashondelivery'){
						$invoice->pay();
					}
					
					$transactionSave = Mage::getModel('core/resource_transaction')
					    ->addObject($invoice)
					    ->addObject($invoice->getOrder())
					    ->save();
					
					$order->setIsInProcess(true);
					$order->setCustomerNoteNotify(true);
					$order->addStatusHistoryComment('Automatically INVOICED by Magento.', false);
 			
					
					$invoice->sendEmail(true);
					if($invoice->getId()){
						$stockLocationModelRaw = Mage::getModel('stocklocation/location')->load();       
						$data = array('order_id'=>$order->getId(),'invoice_id'=>$invoice->getId(),'stock_location'=> $stockloc);
	                    $stockLocationModelRaw->setData($data);
	                    $stockLocationModelRaw->save();
						/*if($stockloc == 'Tamilnadu Warehouse'){
							

		                    foreach ($productData as $key => $value) {
		                    	$orderItem = Mage::getModel('sales/order_item')->load($key);
		                    	$orderSku = $orderItem->getSku();
		                    	$stockchennai = Mage::getModel('ebautomation/stockchennai')->load($orderSku,'sku');
		                    	$newQty = $stockchennai->getQty() - $value;
		                    	$stockchennai->setQty($newQty);
								$stockchennai->save();
	                    	}
		                }*/
			        }
					
					return $invoice->getId();

				}catch(Exception $e){
					$order->addStatusHistoryComment($e->getMessage(), false)->save();
				}
			}
		



			public function createShipment($increment_id, $customer){
				
				$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);

				$invoices = Mage::getModel('sales/order_invoice')->getCollection()->addFieldToFilter('order_id',$order->getId());
				
				foreach ($order->getAllItems() as $item) {
					$item_id = $item->getItemId(); //order_item_id
					$allProductItems[] =  $item_id;  /// Creating all products array
				}
				$invoiceGrandTotal = array();
				foreach ($invoices as $invoice) {
						$invoiceGrandTotal[$invoice->getId()] = $invoice->getGrandTotal();
						$invoiceItemWeight = 0;
						foreach ($invoice->getAllItems() as $item) {
							$invoiceItemsPerInvoice[$invoice->getId()][$item->getOrderItemId()] = $item->getQty();
							$orderItem = Mage::getModel('sales/order_item')->load($item->getOrderItemId()); ///load order item to fetch ordered item weight
							$invoiceItemWeight = $invoiceItemWeight + $orderItem->getWeight()*$item->getQty(); /// product weight * invoiced qty
						}
						$invoiceWeight[$invoice->getId()] = $invoiceItemWeight;
				}

				$_order = Mage::getModel("sales/order")->load($order->getId());
				$shipping_address = $_order->getShippingAddress();

				
				$result = array();
				foreach ($invoiceItemsPerInvoice as $key => $value) {
					$invoiceId = $key;

					$invoiceItems = array_keys($value);
					$shipmentTotalQtyPerShipment = 0;
					foreach ($allProductItems as $productItem) {
						if(in_array($productItem,$invoiceItems)){
							$productData[$productItem] = $value[$productItem];
							$shipmentTotalQtyPerShipment = $shipmentTotalQtyPerShipment + $value[$productItem];
						}else{
							$productData[$productItem] = 0;
						}
					}

					// New Logic according to TAT
					$paymentMethod = $order->getPayment()->getMethod();
					$city = $shipping_address->getCity();
					//removing DHL from operation on 8th June 2018 2:00PM Mahesh Gurav

					// if(in_array($city, array('MUMBAI','PUNE')) && !in_array($paymentMethod,array('banktransfer','cashondelivery'))){
					// 	$serviceType = 'DHL';	
					// } 

					if($invoiceGrandTotal[$invoiceId] <= 200000){
						$pincode = $shipping_address->getPostcode();
						
						$deliveryData = Mage::getModel('operations/serviceablepincodes')->getProductDeliveryOnDetailsPage($pincode,$paymentMethod);
						$serviceType = $deliveryData['serviceType'];
						if($serviceType == ''){
							$order->addStatusHistoryComment('Order is not serviceable.', false);
			 				$order->save();
			 				return;
						}

					}else{
						//$serviceType = 'DHL';	  //for DHL
						$serviceType = 'bluedart';
					}

					if($serviceType == 'dhl'){
						$serviceType = 'DHL';
					}
					
					
					
					$post['incrementid'] = $increment_id;
					$post['invoice_id'] = $invoiceId;

					if($invoiceWeight[$invoiceId] > 0){
						$post['weight'] = $invoiceWeight[$invoiceId];
					}else{
						$post['weight'] = 1;
					}

					
					if($serviceType == "v_express" || $serviceType == 'vexpress'){
						$result = array();
			            $api_result = Mage::getModel('shippinge/vexpress')->vexpress($post);
			           
			            if($api_result['error']){
							$result['error'] = $api_result['error'];
							$result['message'] = $api_result['msg'];
							$order->addStatusHistoryComment("V-Express Shipment Error : ".$result['message'], false)->save();
						}else{
							$result['error'] = $api_result['error'];
							$result['awb'] = $api_result['awb'];
							$result['destination'] = $api_result['destination'];
							$result['payload'] = $api_result['payload'];
						}
			        }
			        
			        

			       
			        if($serviceType == "bluedart"){ //if($result['error'] == true){   
			        	/* && $order->getGrandTotal() < 200000*/
			        	//$serviceType = "bluedart";
			        	$result = array();
			        	$api_result = Mage::getModel('shippinge/bluedart')->bluedart($post);
			        	
			        	if($api_result[0]['error']){
							$result['error'] = $api_result[0]['error'];
							$result['message'] = $api_result[0]['msg'];
							$order->addStatusHistoryComment("Bluedart Shipment Error : ".$result['message'], false)->save();
						}else{
							$result['error'] = $api_result[0]['error'];
							$result['awb'] = $api_result[0]['awb'];
							$result['destination'] = $api_result[0]['destination'];
							$result['payload'] = $api_result[0]['payload'];
						}
			        }

			        if($serviceType == "bluedart" && $result['error'] == true /*&& $order->getGrandTotal() > 100000*/ && $order->getGrandTotal() <= 200000){
			        	$serviceType = "ecom_express";
					}else if($serviceType == "vexpress" && $result['error'] == true && $order->getGrandTotal() <= 200000){
						$serviceType = "ecom_express";
					}


					if($serviceType == "ecom_express" || $serviceType == "ecom") {
						$result = array();
						$api_result = Mage::getModel('shippinge/ecom')->register($post);
						if($api_result[0]['error']){
							$result['error'] = $api_result[0]['error'];
							$result['message'] = $api_result[0]['message'];
							$result['payload'] = $api_result[0]['payload'];
							$order->addStatusHistoryComment("Ecom Shipment Error : ".$result['message'], false)->save();
							
						}else{
							$result['error'] = $api_result[0]['error'];
							$result['awb'] = $api_result[0]['awb'];
							$result['destination'] = $api_result[0]['destination'];
							$result['payload'] = $api_result[0]['payload'];
						}
					}

			        /*if($serviceType == "ecom_express" && $result['error'] == true  && $order->getGrandTotal() > 200000 && $order->getGrandTotal() <= 200000){
			        	$serviceType = "DHL";
			        }*/

			        /// If shipement error will get from other courier partners and order amount greater than 200000, will assign to DHL
			        /*if($result['error'] == true  && $order->getGrandTotal() > 200000){
			        	$serviceType = "DHL";
			        }*/



			        if($serviceType == "DHL"){
			        	$result = array();
			        	$dhlModel = Mage::getModel('ebautomation/dhlawb');
			        	$dhl = $dhlModel->getCollection()->addFieldToFilter('status',0);
			        	//$dhl = $dhlModel->getCollection()->addFieldToFilter('shipment_mode',array('nin'=>'surface'));
						$warehouse = Mage::helper('stocklocation')->getStockLocation($order->getId(),$invoice->getId());
						$city = $shipping_address->getCity();
						/*if($warehouse == 'Tamilnadu Warehouse'){
							if(in_array($city, array('BANGLORE','TAMILNADU','CHENNAI','TAMIL NADU'))){
								$dhl->addFieldToFilter('region','tamil_banglore');
							}else{
								$dhl->addFieldToFilter('region','tamil_outer');
							}
						}else{
							if(in_array($city, array('MUMBAI','PUNE'))){
								$dhl->addFieldToFilter('region','local');
							}else{
								$dhl->addFieldToFilter('region','interstate');
							}
						}*/
						//by default all awb number added as serviceable PAN india
						$dhl->addFieldToFilter('region','local');
				      	$dhl->getSelect()->order('id ASC')->limit(1);
				      	

				      	if($dhl->count() >= 1){
				      		$dhl1 = $dhl->getData();
				      		$dhlAwb = $dhl1[0]['awb'];

				        	$result = array('error' => false, 'awb' => $dhlAwb, 'destination' =>  $shipping_address->getRegion()." ".$shipping_address->getCity());

				        	$dhlModel->load($dhl1[0]['id'])->setStatus(1)->save();
			        	}else{
			        		$result = array('error' => true,'message' => 'AWB number not found for DHL Shipment');
			        		$order->addStatusHistoryComment("DHL Shipment Error : ".$result['message'], false)->save();

			        	}
			        }
			        
			        if($result['awb'] != ''){
						$shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($productData);
						if($shipment){
							$shipment->register();
							$shipment->getOrder()->setIsInProcess(true);
							
							
							try {
								
								$transactionSave = Mage::getModel('core/resource_transaction')
								->addObject($shipment)
								->addObject($shipment->getOrder())
								->save();

								$shipment->sendEmail();
								
								$order->setIsInProcess(true);
								$order->setCustomerNoteNotify(true);
								$order->setState('processing');
								$order->setStatus('shipped');
								

								if(Mage::getSingleton('admin/session')->getUser()){
									$message = "Shipment generated by ". Mage::getSingleton('admin/session')->getUser()->getUsername();	
								}else{
									$message = 'Automatically SHIPPED by Magento.';
								}
								
	                			$order->addStatusHistoryComment($message, false);
 								$order->save();
								
									if($shipment->getId()) { 
							        	$carrier_code = $serviceType;
							        	$title = ucfirst(str_replace("_", " ", $serviceType));
							        	$destination_code = $result['destination'];
							        	$awb_number = $result['awb'];
							        	$payload = $result['payload'];
							        	
							        	$track = Mage::getModel('sales/order_shipment_track')
											->setShipment($shipment)
											->setData('title', $title)
											->setData('weight', $post['weight'])
											->setData('qty', $shipmentTotalQtyPerShipment)
											->setData('track_number', $awb_number)
											->setData('carrier_code', $carrier_code)
											->setData('destination_code', $destination_code)
											->setData('parent_id', $shipment->getId())
											->setData('order_id', $order->getId())
											->setData('invoice_id', $invoiceId)
											->setData('payload', $payload)
											->setData('description', "Autogenerated Shipment Tracking Information.")
											->save();
							    		
							    	}

							    	if(Mage::getStoreConfig('ebautomation/ebautomation_credentials/enable')){
										if($customer->getNavMapStatus()){
											Mage::getModel('ebautomation/order')->orderImport($order);
										}else{
											$order->addStatusHistoryComment('Customer not mapped in Navision.', false);
			 								$order->save();
										}
									}else{
										$order->addStatusHistoryComment('As automation with Navision is disabled, order not processed in Navision.', false);
			 							$order->save();
									}

									$this->sendPackagingShipments($increment_id);
								
							} catch (Mage_Core_Exception $e) {
								$order->addStatusHistoryComment($e->getMessage(), false)->save();
							}
						}
					}
				}

				
			
				
			}


			public function sendPackagingShipments($increment_id){
				$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
				$shipments = Mage::getModel('sales/order_shipment')->getCollection()->addFieldToFilter('order_id',$order->getId());
				$template_id = 27;
				$emailTemplate = Mage::getModel('core/email_template')->load($template_id);
				//$variables['increment_id'] = $order->getIncrementId();
				$template_variables['increment_id'] = $order->getIncrementId();
				$storeId = Mage::app()->getStore()->getId();
				$processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

				//get warehouse

				
				$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
				$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
				//$reciepient = Mage::helper('ebautomation')->getWarehouseMailToSent();
				$subject = "Packingslip of Order #".$order->getIncrementId();

				//try {
				   
				    
				foreach ($shipments as $shipment) {
					 $z_mail = new Zend_Mail('utf-8');

				    $z_mail->setBodyHtml($processedTemplate)
				        ->setSubject($subject)
				        ->setFrom($senderEmail, $senderName);


					$shipmentTrack = Mage::getModel('sales/order_shipment_track')->load($shipment->getId(),'parent_id');
           			$stocklocation = Mage::helper('stocklocation')->getStockLocation($shipmentTrack->getOrderId(),$shipmentTrack->getInvoiceId());

           			$reciepients = Mage::helper('stocklocation')->getWarehouseMailToSent($stocklocation);
           			//print_r($reciepients);
           			foreach ($reciepients as $reciepient) {
           				$z_mail->addTo($reciepient);
           			}
           			
           			

					if ($shipmentd = Mage::getModel('sales/order_shipment')->load($shipment->getId())) {
						$pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf(array($shipmentd));

					}
					$filename = "packingslip_".$order->getIncrementId()."_".$shipment->getIncrementId()."_".date("Y-m-d").".pdf";
					 
					$pdfFile = Mage::getBaseDir().'/media/packingslips/'.$filename;
					$pdf->save($pdfFile, true);
					
					
					
					   
				     $attachment = $z_mail->createAttachment(
	                    file_get_contents($pdfFile),
	                    'application/pdf',
	                    Zend_Mime::DISPOSITION_ATTACHMENT,
	                    Zend_Mime::ENCODING_BASE64,
	                    $filename 
	                );
					
					try{
						$z_mail->send();
					}catch(Exception $e){
						$order->addStatusHistoryComment($e->getMessage(), false)->save();
					}

				}
				
				
			}




	public function getItemTotal($item)
    {
        return $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount();
    }


    /*
	* @purpose : To send remaining DHL AWB number status.
	*/
    public function sendAwbRemainingStatus(){
    	$dhlawb = Mage::getModel('ebautomation/dhlawb')->getCollection()->addFieldToFilter('status',0);
    	$dhlawb->addFieldToSelect('*');
    	$dhlawb->addExpressionFieldToSelect('no_of_unused_awb','COUNT(id)','id');
    	$dhlawb->getSelect()->group("region");

    	$ecomawb = Mage::getModel('ecom/ppdawb')->getCollection()->addFieldToFilter('status',0);
    	$vexpressawb = Mage::getModel('vexpressawb/vexpress')->getCollection()->addFieldToFilter('status',0);
    	
    	$emailTemplate  = Mage::getModel('core/email_template')->load(28);									
 
		//Create an array of variables to assign to template
		$emailTemplateVariables = array();
		$emailTemplateVariables['content']['dhl'] = $dhlawb->getData();
		$emailTemplateVariables['content']['ecom'] = $ecomawb->count();
		$emailTemplateVariables['content']['vexpress'] = $vexpressawb->count();

		$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
		 
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		
		$subject = $emailTemplate->getTemplateSubject();

		$z_mail = new Zend_Mail('utf-8');

	    $z_mail->setBodyHtml($processedTemplate)
	        ->setSubject($subject)
	        ->setFrom($senderEmail, $senderName);
	    
	    $reciepients = explode(',',Mage::getStoreConfig('ebautomation/ebautomation_awb_status/awb_status_to'));
	    foreach ($reciepients as $reciepient) {
			$z_mail->addTo($reciepient);
		}
		
	    try{
	    	$z_mail->send();
	    }catch(Exception $e){
	    	Mage::log($e->getMessage(),Zend_Log::INFO,'awb_status_mail_log.log');
	    }
    	return;
    }


			
}
?>