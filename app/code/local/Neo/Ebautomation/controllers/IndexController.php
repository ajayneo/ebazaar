<?php
class Neo_Ebautomation_IndexController extends Mage_Core_Controller_Front_Action{
	/*
	* @author : Sonali Kosrabe
	* @date : 22-aug-2017
	* @purpose : To run cron for order automation i.e. creating order invoice and shipment automatically 
	*/
    public function IndexAction() {
    	// Check first of module is enabled or not
      if(Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
      		Mage::getModel('ebautomation/ebautomation')->orderProcessAutomation();
      		Mage::getModel('ebautomation/ebautomation')->prepaidOrderProcessAutomation();
      		$this->processNullOrderInNavision(); // Process order code if error comes as NULL from Navision
      		
      }
	}


	
	/*
	* @author : Sonali Kosrabe
	* @date : 22-nov-2017
	* @purpose : To process order invoice and shipment automatically from Admin button
	*/
	public function processOrderForShipmentAction()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		//echo Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select');die;
		if(Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
      		Mage::getModel('ebautomation/ebautomation')->orderProcessAutomation($orderId);
      		Mage::getModel('ebautomation/ebautomation')->prepaidOrderProcessAutomation($orderId);
      		Mage::getSingleton("adminhtml/session")->addSuccess("Please check invoice and shipment.");
      	}else{
      		Mage::getSingleton("adminhtml/session")->addError("Auto Invoice/Shipment is disabled.");
      	}
      	

      	$this->_redirect('adminhtml/sales_order/view', array('order_id'=>$orderId, '_secure' => true));
	}

	/*
	* @author : Sonali Kosrabe
	* @date : 22-nov-2017
	* @purpose : To process order invoice and shipment automatically from Admin button
	*/
	public function processOrderInNavisionAction()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		if(Mage::getStoreConfig('ebautomation/ebautomation_credentials/enable')){
			$order = Mage::getModel('sales/order')->load($orderId);
      		Mage::getModel('ebautomation/order')->orderImport($order);
      		Mage::getSingleton("adminhtml/session")->addSuccess("Order Processed in Navision.");
      	}else{
      		Mage::getSingleton("adminhtml/session")->addError("Automation with Navision is disabled.");
      	}
      	

      	$this->_redirect('adminhtml/sales_order/view', array('order_id'=>$orderId, '_secure' => true));
	}


	/*
	* @author : Sonali Kosrabe
	* @date : 22-nov-2017
	* @purpose : To process order invoice and shipment automatically from Admin button
	*/
	public function processNullOrderInNavision()
	{

		$orders = Mage::getModel('sales/order')->getCollection()
				->addFieldToFilter('created_at', array('from' => date('Y-m-d',strtotime('-10 day'))))
				->addFieldToFilter('status','shipped')
				->addFieldToFilter('mapped_status','0');
		$orderIds = $orders->getColumnValues('entity_id');
		foreach ($orderIds as $orderId) {
			
			if(Mage::getStoreConfig('ebautomation/ebautomation_credentials/enable')){
				$order = Mage::getModel('sales/order')->load($orderId);
	      		Mage::getModel('ebautomation/order')->orderImport($order);
	      	}
	      	
		}
	}


	/*
	* @author : Sonali Kosrabe
	* @date : 22-nov-2017
	* @purpose : To send AWB status to EB team
	*/
	public function sendAwbRemainingStatusAction(){
		Mage::getModel('ebautomation/ebautomation')->sendAwbRemainingStatus();
	}
	
	public function testAPIAction(){
		$pageURL = 'http://111.119.217.101:8013/EBWebAutomation/WS/Amiable%20Electronics%20Pvt.%20Ltd./Page/ItemCard';
		$odataUrl = "http://111.119.217.101:8014/EBWebAutomation/OData/Company('Amiable%20Electronics%20Pvt.%20Ltd.')/ItemCard";
		// Read Single Record
		$params = array('No' => '*1*'); 

		try{
			$ch = curl_init();

			
			curl_setopt($ch, CURLOPT_URL,$pageURL);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
			// receive server response ...
			curl_setopt($ch, CURLOPT_USERPWD, 'nav1:Win$123');   

			/*curl_setopt($ch, CURLOPT_HTTPHEADER, [
			        'Method: POST',
		            'Connection: Keep-Alive',
		            'User-Agent: PHP-SOAP-CURL',
		            'Content-Type: text/xml; charset=utf-8',
		            'SOAPAction: "Read"',                        

			]);   */

			$server_output = curl_exec($ch);
			//echo "dfasd";
			print_r($server_output);

			curl_close ($ch);
		}catch(Exception $e){
			echo $e->getMessage();

		}


		/*try{
			echo "assa";

			$options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE,
                'connection_timeout' => 500
            );
			$service = new SoapClient($pageURL,$options	);



			$service->user = "nav1";  //nav1:Win$123
	    	$service->password = "Win$123";
			$service->login($service->user,$service->password);
			$params = array('No' => '*1*');
			print_r($params);
			$result = $service->Read($params);

			$item = $result->Item;

			echo "Read Single Record Result: Item Filter No = *1*:" . $item->No . "&nbsp;" . $item->Description."<br><br>";

		}

		catch (Exception $e) {

			echo "<hr><b>ERROR: SoapException:</b> [".$e."]<hr>";

			echo "<pre>".htmlentities(print_r($service->__getLastRequest(),1))."</pre>";

		}*/
	}

	public function getXmlAction(){
		$test_array = array (
		  'bla' => 'blub',
		  'foo' => 'bar',
		  'another_array' => array (
		    'stack' => 'overflow',
		  ),
		);
		//header('Content-type: text/xml');
    	//$xml = new SimpleXMLElement('<root/>');
		//array_walk_recursive($test_array, array ($xml, 'addChild'));
		//print $xml->asXML();

		//creating object of SimpleXMLElement
		$xml_user_info = new SimpleXMLElement("<?xml version=\"1.0\"?><user_info></user_info>");

		//function call to convert array to xml
		$this->array_to_xml($test_array,$xml_user_info);

		//saving generated xml file
		$xml_file = $xml_user_info->asXML(Mage::getUrl().'/users.xml');
		echo $xml_file = $xml_user_info->asXML();

		//success and error message based on xml creation
		if($xml_file){
		    echo 'XML file have been generated successfully.';
		}else{
		    echo 'XML file generation error.';
		}

		
		
	}

	//function defination to convert array to xml
	function array_to_xml($array, &$xml_user_info) {
	    foreach($array as $key => $value) {
	        if(is_array($value)) {
	            if(!is_numeric($key)){
	                $subnode = $xml_user_info->addChild("$key");
	                $this->array_to_xml($value, $subnode);
	            }else{
	                $subnode = $xml_user_info->addChild("item$key");
	                $this->array_to_xml($value, $subnode);
	            }
	        }else {
	            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
	        }
	    }
	}

	



	/* Testing Function For Automation */ 

	/*public function testOrderProcessAction() {
    	
      		Mage::getModel('ebautomation/ebautomation')->orderProcessAutomation();
      
	}

	public function prepaidOrderProcessAutomationAction() {
    	
      		Mage::getModel('ebautomation/ebautomation')->prepaidOrderProcessAutomation();
      
	}*/

	public function getPayUBizPaymentStatusAction(){
		$key = Mage::getStoreConfig('payment/payubiz/merchant_key');
		//$var1 = date('Y-m-d');//"E00000290_403993715516661211";//200056491;
		$var1 = '200071261';
		//$var2 = date('Y-m-d');
		//$command = 'get_Transaction_Details';//'get_Transaction_Details';
		$command = 'verify_payment';
		$salt = Mage::getStoreConfig('payment/payubiz/salt');
		//echo $command;die;
		$data = $key."|".$command."|".$var1."|".$salt;//Security parameter—- SHA512(key|command|var1|salt)
		$hash = hash('sha512', $data);
		//die;
		$r = array('key' => $key , 'hash' =>$hash , 'var1' => $var1, /*'var2' => $var2,*/'command' => $command);
		$qs= http_build_query($r);

		$wsUrl = "https://info.payu.in/merchant/postservice.php?form=1";
		
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $wsUrl);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        $o = curl_exec($c);
		

		print_r($o);
	}


	public function getPayTmPaymentStatusAction(){
		//return Mage::helper('paytm')->getPendingPaymentStatus();

		$orders = Mage::getModel('sales/order')->getCollection();
		$orders->getSelect()->join(
		    array('p' => $orders->getResource()->getTable('sales/order_payment')),
		    'p.parent_id = main_table.entity_id',
		    array()
		);
		$orders->addFieldToFilter('method','paytm_cc');
		$orders->addFieldToFilter('state','new');
		echo $orders->getSelect();
		print_r($orders->getColumnValues('increment_id'));
		die;	
		echo $request['ORDERID'] = '200051047';// '200077976';//'200051047';
		//die;
		$order = Mage::getModel('sales/order');
			
            $order->loadByIncrementId($request['ORDERID']);
            //echo $order->getPayment()->getMethod();die;
            if (!$order->getId()) {
                Mage::log('No order for processing found');
            }
			

			$isValidChecksum = false;
			$txnstatus = false;
			$authStatus = false;
			$mer_encrypted = Mage::getStoreConfig('payment/paytm_cc/inst_key');
			$const = (string)Mage::getConfig()->getNode('global/crypt/key');
			$mer_decrypted= Mage::helper('paytm')->decrypt_e($mer_encrypted,$const);
			
			$merid_encrypted = Mage::getStoreConfig('payment/paytm_cc/inst_id');
			$const = (string)Mage::getConfig()->getNode('global/crypt/key');
			$merid_decrypted= Mage::helper('paytm')->decrypt_e($merid_encrypted,$const);
			
			//check returned checksum
			/*if(isset($request['CHECKSUMHASH']))
			{
				$return = Mage::helper('paytm')->verifychecksum_e($parameters, $mer_decrypted, $request['CHECKSUMHASH']);
				if($return == "TRUE")
				$isValidChecksum = true;
				
			}*/
			
			/*if($request['STATUS'] == "TXN_SUCCESS"){
				$txnstatus = true;
			}*/
			
			$_testurl = NULL;
			if(Mage::getStoreConfig('payment/paytm_cc/mode')==1)
				$_testurl = Mage::helper('paytm/Data2')->STATUS_QUERY_URL_PROD;
			else
				$_testurl = Mage::helper('paytm/Data2')->STATUS_QUERY_URL_TEST;

            // echo $_testurl; exit;

			//if($txnstatus && $isValidChecksum){
				// Create an array having all required parameters for status query.
				//echo "<pre>"; print_r($request);
				$requestParamList = array("MID" => $merid_decrypted , "ORDERID" => $request['ORDERID']);
				
				$StatusCheckSum = Mage::helper('paytm')->getChecksumFromArray($requestParamList, $mer_decrypted);
							
				$requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
				print_r($requestParamList);
				// Call the PG's getTxnStatus() function for verifying the transaction status.
				
				$check_status_url = 'https://pguat.paytm.com/oltp/HANDLER_INTERNAL/getTxnStatus';
				if(Mage::getStoreConfig('payment/paytm_cc/mode') == '1')
				{
					$check_status_url = 'https://secure.paytm.in/oltp/HANDLER_INTERNAL/getTxnStatus';
				}

				$responseParamList = Mage::helper('paytm')->callNewAPI($check_status_url, $requestParamList);
				echo "<pre>"; print_r($responseParamList); die;

				if($responseParamList['STATUS'] == 'TXN_SUCCESS'){
					$order->setState('processing');
					$order->setStatus('financeapproved');
					$order->addStatusHistoryComment('Payment Received in paytm '.$responseParamList['TXNDATE'].' '.$responseParamList['TXNID'].' '.$request['ORDERID'].' '.$responseParamList['TXNAMOUNT'], false);
					$order->save();
				}elseif($responseParamList['STATUS'] == 'TXN_FAILURE'){
					$order->setState('new');
					$order->setStatus('paytmpaymentpending');
					$order->addStatusHistoryComment('Payment Failed in PayTm', false);
					$order->save();
				}
			//}


	}

	/*public function setProductPriceAndQtyAction(){
		$_params = $this->getRequest()->getParams();
		
		$qty = $_params['qty'];
		$price = str_replace(',', '', $_params['price']);
		
		if(!isset($_params['sku']) || empty($_params['sku'])){
			echo json_encode(array('status' => 0, 'message' => "SKU Not Defined."));
			exit;
		}

		if(isset($_params['price']) && (!is_numeric($price) || $price < 0)) { // || (!is_numeric($qty) || $qty < 0)){
			echo json_encode(array('status' => 0, 'message' => "Data Invalid."));
			exit;
		}
		if(isset($_params['qty']) && (!is_numeric($qty))){
			echo json_encode(array('status' => 0, 'message' => "Data Invalid."));
			exit;
		}


		$reason = $_params['reason']; 
		
		$statusFlag = 2; //Disable
		Mage::app()->getStore()->setConfig('catalog/frontend/flat_catalog_product', 0);

		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_params['sku']);
		$product->load($product->getId());
		if($product){
			$isPriceUpdate = false;
			$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());

			if(isset($_params['price'])){		// update only price
				$product->setData('price',$price);
				if($stockItem->getQty() > 0){
					$statusFlag = 1;
				}
				
				
			}
			
			if (isset($_params['qty']) && $stockItem->getId() > 0 and $stockItem->getManageStock()) { // update only qty
				$changedQty = $stockItem->getQty() + (int)$qty; 
				$stockItem->setQty($changedQty);
				$stockItem->setIsInStock((int)($changedQty > 0));

				$product->setQtyUpdateReason($reason);
				$product->setQtyUpdateFlag(1);
				

				if($product->getPrice() != NULL && $product->getPrice() > 0){
					$statusFlag = 1;
				}
			}

			try{
				
				$product->setStatus($statusFlag);
				if($product->save() && $stockItem->save()){
					echo json_encode(array('status' => 1, 'message' => "Product updated succesfully."));
					exit;
				}else{
					echo json_encode(array('status' => 0, 'message' => "Unable to save status."));
					exit;
				}
			}catch(Exception $ex){
				echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
				exit;
			}
		}else{
			echo json_encode(array('status' => 0, 'message' => "Sku not found."));
				exit;
		}
		
	}*/

	public function setProductPriceAndQtyAction(){
		$_params = $this->getRequest()->getParams();
		
		$qty = $_params['qty'];
		$price = str_replace(',', '', $_params['price']);
		
		if(!isset($_params['sku']) || empty($_params['sku'])){
			echo json_encode(array('response'=>array('status' => 0, 'sku' => $_params['sku'], 'message' => "SKU Not Defined.")));
			exit;
		}

		if(isset($_params['price']) && (!is_numeric($price) || $price < 0)) { // || (!is_numeric($qty) || $qty < 0)){
			echo json_encode(array('response'=>array('status' => 0, 'sku' => $_params['sku'], 'message' => "Data Invalid.")));
			exit;
		}
		if(isset($_params['qty']) && (!is_numeric($qty))){
			echo json_encode(array('response'=>array('status' => 0, 'sku' => $_params['sku'], 'message' => "Data Invalid.")));
			exit;
		}


		$reason = $_params['reason']; 
		
		$statusFlag = 2; //Disable
		Mage::app()->getStore()->setConfig('catalog/frontend/flat_catalog_product', 0);

		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_params['sku']);
		if(!$product){
			echo json_encode(array('response'=>array('status' => 0, 'sku' => $_params['sku'], 'message' => "Product not found.")));
			exit;
		}
		$product->load($product->getId());
		if($product){
			$isPriceUpdate = false;
			$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());

			if(isset($_params['price'])){		// update only price
				$product->setData('price',$price);
				if($stockItem->getQty() > 0){
					$statusFlag = 1;
				}
				
				
			}
			
			if (isset($_params['qty']) && $stockItem->getId() > 0 and $stockItem->getManageStock()) { // update only qty
				$changedQty = $stockItem->getQty() + (int)$qty; 
				$stockItem->setQty($changedQty);
				$stockItem->setIsInStock((int)($changedQty > 0));

				$product->setQtyUpdateReason($reason);
				$product->setQtyUpdateFlag(1);
				

				if($product->getPrice() != NULL && $product->getPrice() > 0){
					$statusFlag = 1;
				}
			}

			try{
				
				//$product->setStatus($statusFlag);
				if($product->save() && $stockItem->save()){
					$updates = array('product_id'=>$product->getId(),
                                    'stock_item_id'=>$stockItem->getItemId(),
                                    'qty_from_navision'=>(int)$qty,
                                    'reason' => $reason,
                                    'updated_at'=> Mage::getModel('core/date')->date('Y-m-d H:i:s'));
                    
                    $inventoryUpdates = Mage::getModel('soapapi/inventoryupdates')->setData($updates)->save();

					echo json_encode(array('response'=>array('status' => 1, 'sku' => $_params['sku'], 'message' => "Product updated successfully.")));
					exit;
				}else{
					echo json_encode(array('response'=>array('status' => 0, 'sku' => $_params['sku'], 'message' => "Unable to save status.")));
					exit;
				}
			}catch(Exception $ex){
				echo json_encode(array('response'=>array('status' => 0, 'sku' => $_params['sku'], 'message' => $ex->getMessage())));
				exit;
			}
		}else{
			echo json_encode(array('response'=>array('status' => 0, 'sku' => $_params['sku'], 'message' => "Sku not found.")));
				exit;
		}
		
	}

	public function reindexingAction(){
		/*$indexCollection = Mage::getModel('index/process')->getCollection();
		foreach ($indexCollection as $index) {
		    $index->reindexAll();
		}*/

		// 	Product Attributes
		$process = Mage::getModel('index/process')->load(1);
		$process->reindexAll();

		// Product Prices
		$process = Mage::getModel('index/process')->load(2);
		$process->reindexAll();

		// Search Index
		$process = Mage::getModel('index/process')->load(7);
		$process->reindexAll();

		// Stock Status
		$process = Mage::getModel('index/process')->load(8);
		$process->reindexAll();
	}

	

}