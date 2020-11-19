<?php
class Neo_Ebautomation_Model_Customer extends Mage_Core_Model_Abstract
{
	
	
	/*
	* @Author : Sonali Kosrabe
	* @Date : 3-nov-2017
	* @Purpose : To Map Customer in Navision
	*/
	public function customerImport(Varien_Event_Observer $observer){

		$event = $observer->getEvent();
		$customerData = $event->getCustomer();
		Mage::log('Event map customer for email '.$customerData->getEmail(), null, 'nav.log', true);
		$this->mapCustomerInNavision($customerData);

	}

	/*
	* @Author : Sonali Kosrabe
	* @Date : 3-nov-2017
	* @Purpose : To Map Customer in Navision at once
	*/
	public function allCustomerMapping(){
		if(!Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
			//Mage::getSingleton("adminhtml/session")->addError("NAvision automation is disabled.");
			return;
		}
		$customerData = Mage::getModel('customer/customer')->getCollection();
		$customerData->addFieldToFilter('repcode',array('neq'=>null));
		$customerData->addAttributeToFilter('group_id',array('in'=>array(4)));
		$customerData->addAttributeToFilter('nav_map_status',array('in'=>array('','0')));

		$customerData = $customerData->getColumnValues('entity_id');
		
		$chunked_customerData = array_chunk($customerData, 3000);
		foreach ($chunked_customerData as $customers) {
			foreach ($customers as $key => $customerId) {
				$customer = Mage::getModel('customer/customer')->load($customerId);
				$this->mapCustomerInNavision($customer);
			}
		}
		
	}

	/*
	* @Author : Sonali Kosrabe
	* @Date : 3-nov-2017
	* @Purpose : To Map Customer in Navision at once
	*/
	public function specificCustomerMapping(){
		if(!Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
			//Mage::getSingleton("adminhtml/session")->addError("NAvision automation is disabled.");
			return;
		}
		$customerData = Mage::getModel('customer/customer')->getCollection();
		$customerData->addFieldToFilter('created_at',array('gt'=>'2018-04-16 00:00:00'));
		//$customerData->addAttributeToFilter('entity_id',array('in'=>array(44145)));
		
		$customerData = $customerData->getColumnValues('entity_id');

		print_r($customerData);
		//die;
		
		$chunked_customerData = array_chunk($customerData, 3000);
		foreach ($chunked_customerData as $customers) {
			foreach ($customers as $key => $customerId) {
				$customer = Mage::getModel('customer/customer')->load($customerId);
				$customer->setnav_map_status(0)->save();
				$this->mapCustomerInNavision($customer);
				//die;
			}
		}
		
	}

	/*
	* @Author : Sonali Kosrabe
	* @Date : 3-nov-2017
	* @Purpose : To Map Customer in Navision
	*/
	public function mapCustomerInNavision($customerData){

		if(!Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
			Mage::getSingleton("adminhtml/session")->addError("Navision automation is disabled.");
			return;
		}
		
		if($customerData->getRepcode() == ''){
			$repcode = 'Aff_'.date('Ymds');
		}else{
			$repcode = $customerData->getRepcode();
		}

		if($customerData->getnav_map_status()){
			return;
		}
		
		$groupName = Mage::getModel('customer/group')->load($customerData->getGroupId())->getCustomerGroupCode();

		$stateCodes = Mage::helper('indiagst')->getStateCodes();
		$reg = Mage::getModel('directory/region')->load($customerData->getCusState());
		
		$cusStateCode = Mage::helper('ebautomation')->stateCodeMapping($reg->getCode());

		$asm_map = $customerData->getResource()->getAttribute('asm_map')->getFrontend()->getValue($customerData);
		
		$customerArray = array('id'=>$customerData->getId(),
					'repcode'=>$repcode,
					'firstname'=>$customerData->getFirstname(),
					'lastname'=>$customerData->getLastname(),
					'email'=>$customerData->getEmail(),
					'group'=>$groupName,
					'asm_map'=>$asm_map,
					'mobile'=>$customerData->getMobile(),
					'taxvat'=>$customerData->getTaxvat(),
					'gstin' =>$customerData->getGstin(),
					'pincode'=>$customerData->getPincode(),
					'city'=>$customerData->getCusCity(),
					'state'=>$cusStateCode,
					'country'=>$customerData->getCusCountry(),
					'affiliate_store'=>$customerData->getAffiliateStore(),
					'gen_bus_posting_group'=>'B2B',
					'customer_posting_group'=>'B2B',
					'tax_liable'=>true
					);
		
		try{
		
			$action = 'Create';
			$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cus="urn:microsoft-dynamics-schemas/page/cust">
			    <x:Header/>
			    <x:Body>
			        <cus:Create>
			            <cus:cust>
			                <cus:Name>'.$customerArray['firstname'].' '.$customerArray['lastname'].'</cus:Name>
			                <cus:Search_Name>'.$customerArray['firstname'].' '.$customerArray['lastname'].'</cus:Search_Name>
			                <cus:Repcode>'.$customerArray['repcode'].'</cus:Repcode>
			                <cus:Gen_Bus_Posting_Group>'.$customerArray['gen_bus_posting_group'].'</cus:Gen_Bus_Posting_Group>
			                <cus:Customer_Posting_Group>'.$customerArray['customer_posting_group'].'</cus:Customer_Posting_Group>
			                <cus:Tax_Liable>'.$customerArray['tax_liable'].'</cus:Tax_Liable>
			                <cus:Post_Code>'.$customerArray['pincode'].'</cus:Post_Code>
			                <cus:City>'.$customerArray['city'].'</cus:City>
			                <cus:Country_Region_Code>'.$customerArray['country'].'</cus:Country_Region_Code>
			                <cus:State_Code>'.$customerArray['state'].'</cus:State_Code>
			                <cus:Phone_No>'.$customerArray['mobile'].'</cus:Phone_No>
			                <cus:G_S_T_No>'.$customerArray['gstin'].'</cus:G_S_T_No>
			                <cus:E_Mail>'.$customerArray['email'].'</cus:E_Mail>
			                <cus:Mobile_No>'.$customerArray['mobile'].'</cus:Mobile_No>
			            </cus:cust>
			        </cus:Create>
			    </x:Body>
			</x:Envelope>';

			$apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_customer_url');
		
			
			$result = Mage::helper('ebautomation')->callNavisionSOAPApi($apiUrl, $action, $params);
			//echo "dsfs";die;
			$datetime = gmdate("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); 
			
			if($result['sBody']->sFault || $result['SoapBody']->Create_Result->cust->Key == null){
				$response = $result['sBody']->sFault->faultstring;
				if($response == 'Rapcode already exist'){
					$customerData->setnav_map_status(1)->setRepcode($repcode);
					$response = $customerData->getNavMapDetails()."\r\n#".$datetime.": ".$response;//die;
					$customerData->setnav_map_details($response)->save();
					//return true;

				}elseif (strpos($response, 'Customer already exists') !== false){
					$customerData->setnav_map_status(1)->setRepcode($repcode);
					$response = $customerData->getNavMapDetails()."\r\n#".$datetime.": ".$response;//die;
					$customerData->setnav_map_details($response)->save();
				}else{
					$customerData->setnav_map_status(0)->setRepcode($repcode);
					$response = $customerData->getNavMapDetails()."\r\n#".$datetime.": ".$response;//die;
					$customerData->setnav_map_details($response)->save();
					//return false;
				}
			}else{
				$response = $result['SoapBody']->Create_Result->cust->Key;
				$customerData->setnav_map_details("#".$datetime.": Customer Mapped in Navision with Key :".$response);
				$customerData->setnav_map_status(1)->setRepcode($repcode);
				$customerData->save();
				//return true;
			}
		}catch(Exception $e){
			$response = $customerData->getNavMapDetails()."\r\n#".$datetime.": Exception Occured: ".$e->getMessage();
			$customerData->setnav_map_details($response)->setRepcode($repcode)->save();
			//return false;
		}

		Mage::log('End line of save map customer for email '.$customerData->getEmail(), null, 'nav.log', true);
	 
	}

	/*
	* @Author : Sonali Kosrabe
	* @Date : 3-feb-2017
	* @Purpose : To get customer credit limit while placing order and in my account
	*/	
	public function getCreditLimit($user_id,$grand_total = NULL){

		if(!Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
			$credit = null;
			return $credit;
		}

	    
	    $customerData = Mage::getModel('customer/customer')->load($user_id);
	    //$repcode = 'dhcgdbyuwgf73897130894';//$this->getRequest()->getPost('repcode');
	    $repcode = trim($customerData->getRepcode());
	    $repcode = strtoupper($repcode);
	    Mage::log("Repcode> ".$repcode,null,'creditlimit.log');
	    try{
	    
	      	$action = 'Read';
			$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rap="urn:microsoft-dynamics-schemas/page/rapcode_wise_credit_limit">
					<x:Header/>
					<x:Body>
						<rap:Read>
							<rap:Repcode>'.$repcode.'</rap:Repcode>
						</rap:Read>
					</x:Body>
				</x:Envelope>';

			$apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_credit_limit_url');

			$result = Mage::helper('ebautomation')->callNavisionSOAPApi($apiUrl, $action, $params);
			//print_r($result);
			// Mage::log(var_dump($result),null,'creditlimit.log');
			$credit = array();
			
			$credit['message'] = '';
			if($result['sBody']->sFault){
				$response = $result['sBody']->sFault->faultstring;
				$credit['message'] = $response;
				return $credit;
			}else{
				$total_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Credit_Limit_LCY);
				$used_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Balance_LCY);
				$overdue_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Overdue_Balance_LCY);
				$balance_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Total_Balance);
				$vow_credit_limit = implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Vow_Credit_Limit);

				//Vow credit restrict to only vow products in cart By Mahesh Gurav on 12th April 2018
				if($credit['vow_credit_limit'] == 'true'){
					$quote = Mage::getModel('sales/quote')->loadByCustomer($customerData);
					$vow_only = false;
					if($quote){
						foreach ($quote->getAllItems() as $item) {
							if(strpos($item->getName(), 'VOW') === false){
								$vow_only = true;
							}
						}
					}
					if($vow_only){
						return $credit;
					}
				}

				$toDate = date('Y-m-d H:i:s');
            	$fromDate = date('Y-m-d H:i:s', strtotime("-3 day"));
            	$orderCollection = Mage::getModel('sales/order')->getCollection();
            	$orderCollection->getSelect()->join(
				    array('p' => $orderCollection->getResource()->getTable('sales/order_payment')),
				    'p.parent_id = main_table.entity_id',
				    array()
				);
    			$orderCollection->addFieldToFilter('customer_id',$user_id);
    			$orderCollection->addFieldToFilter('status',array('nin'=>array('shipped','canceled')));
    			$orderCollection->addFieldToFilter('method',array('in'=>array('banktransfer')));
    			$orderCollection->addFieldToSelect('grand_total');
    			$orderCollection->addFieldToFilter('main_table.created_at',array('from' => $fromDate, 'to' => $toDate));
		    	$orderCollection->getSelect()->columns('SUM(grand_total) as grand_total');
		    	$orderData = $orderCollection->getData();
		    	$lastOneHourPlacedOrderTotal = $orderData[0]['grand_total'];
		    	
		    	$credit['total_credit'] =  $total_credit;
				$credit['utilized_credit'] =  $used_credit; 
				$credit['pre_balance_credit'] =  $balance_credit;
				
				$credit['overdue_credit'] =  $overdue_credit;
				$credit['balance_credit'] =  $balance_credit - $lastOneHourPlacedOrderTotal; 
				$credit['total_label'] = 'Total Credit Limit';
				$credit['utilized_label'] = 'Used';
				$credit['balance_label'] = 'Balance';
				$credit['overdue_label'] = 'Overdue';
				$credit['lastOneHourPlacedOrderTotal'] = $lastOneHourPlacedOrderTotal;
				$credit['vow_credit_limit'] = $vow_credit_limit;

				$lastPlacedMessage = '';
		      	if($credit['lastOneHourPlacedOrderTotal'] > 0){
		      		$lastPlacedMessage = "Last order placed amount ".Mage::helper('core')->currency($credit['lastOneHourPlacedOrderTotal'], true, false); 
		      	}
		      	
		      	
		      	if($credit['balance_credit'] == 1){
			      $credit['message'] = "Your Credit limit is not set yet.";
			      $credit['bounce'] = 1;
			    }else if($credit['overdue_credit'] > 0){
			      $credit['message'] = "Your credit has overdue ".Mage::helper('core')->currency($credit['overdue_credit'], true, false);
			      $credit['bounce'] = 1;
			    }else if( $grand_total > $credit['balance_credit']){
			      $credit['message'] = "Your order amount exceeded than available balance. Your available credit balance is ".Mage::helper('core')->currency($credit['balance_credit'], true, false);
			      	$credit['bounce'] = 1;
			    }else{
			      $credit['message'] = "Your available credit balance is ".Mage::helper('core')->currency($credit['balance_credit'], true, false).' '.$lastPlacedMessage;
			      $credit['bounce'] = 0;
			    }
			    /*************** three conditions for disallowing credit *****************************/
			    //if total_credit is 1 that means credit is disabled from Navision
				//if overdue_credit that means transation is not settled within due days
			    //balance_credit should be able to grand total order
				if($credit['overdue_credit'] > 0){
					$credit['allow'] = 0;
				}else if($credit['balance_credit'] <= 1){
					$credit['allow'] = 0;
				}else if($credit['balance_credit'] < $grand_total){
					$credit['allow'] = 0;
				}else if($credit['total_credit'] == 1){
					$credit['allow'] = 0;
				}else{
					$credit['allow'] = 1;
				}
				//print_r($credit);
				return $credit;
			}
		}catch(Exception $e){
			
		}
	}

	/*
	* @Author : Sonali Kosrabe
	* @Date : 3-feb-2017
	* @Purpose : To get customer credit limit while placing order and using it at sales_order_place_after
	*/
	public function getCreditLimitLog($user_id){

		if(!Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
			$credit = null;
			return $credit;
		}
	    
	    $customerData = Mage::getModel('customer/customer')->load($user_id);
	    //$repcode = 'dhcgdbyuwgf73897130894';//$this->getRequest()->getPost('repcode');
	    $repcode = trim($customerData->getRepcode());
	    $repcode = strtoupper($repcode);
	    
	    $action = 'Read';
	      
		$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rap="urn:microsoft-dynamics-schemas/page/rapcode_wise_credit_limit">
				<x:Header/>
				<x:Body>
					<rap:Read>
						<rap:Repcode>'.$repcode.'</rap:Repcode>
					</rap:Read>
				</x:Body>
			</x:Envelope>';

		$apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_credit_limit_url');

		$result = Mage::helper('ebautomation')->callNavisionSOAPApi($apiUrl, $action, $params);

		
		$credit = array();
		
		$credit['message'] = '';
		if($result['sBody']->sFault){
			$response = $result['sBody']->sFault->faultstring;
			$credit['message'] = $response;
			return $credit;
		}else{
			$total_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Credit_Limit_LCY);
			$used_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Balance_LCY);
			$overdue_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Overdue_Balance_LCY);
			$balance_credit = (int) implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Total_Balance);

	    	$credit['total_credit'] =  $total_credit;
			$credit['utilized_credit'] =  $used_credit; 
			$credit['overdue_credit'] =  $overdue_credit;
			$credit['balance_credit'] =  $balance_credit;
			
			
			return $credit;
		}
		
	}
	
	


}
?>