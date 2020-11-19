<?php
class Neo_Billdesk_Model_Observer
{
    public function process_order_response($observer)
    {
		$order = $observer->getEvent()->getOrder();
		$order_inc_id = $order->getIncrementId();
		$customer_id = $order->getCustomerId();
		$payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
		if($payment_method_code == 'cashondelivery'){
		    $order_total = $order->getGrandTotal();
			$cod_fee_total = $order->getCodFee();
		    //$cod_maxlimit = Mage::getStoreConfig('payment/cashondelivery/cod_maxlimit');
		    if($cod_fee_total){
		    	$grand_total = $order->getGrandTotal() + $cod_fee_total;
				$order->setGrandTotal($grand_total);
				$paid = 0;
				$order->setTotalPaid($paid);
		    	$order->setState(Mage_Sales_Model_Order::STATE_NEW, true, 'Pending Amount.');
	            $order->setStatus('pending');

				$advance_amount = $cod_fee_total;
				//$order_grand_total = $advance_amount;
				$order_grand_total = $advance_amount;
		    }
		}else{
		    $order_grand_total = $order->getGrandTotal();
		}
	
		//$order_grand_total = $order->getGrandTotal();
		
		//$order = $observer->getEvent()->getOrder();
		$merchant_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/merchant_id');
		$currency_type = Mage::getStoreConfig('payment/billdesk_ccdc_net/currency_type');
		$security_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/security_id');
		$return_url = Mage::getStoreConfig('payment/billdesk_ccdc_net/return_url');
		$checksum_key = Mage::getStoreConfig('payment/billdesk_ccdc_net/checksum_key');
		
        //$str = 'EBAZAAR|321654987|NA|10|HDF|NA|NA|INR|DIRECT|R|ebazaar|NA|NA|F|Andheri|Mumbai|02240920005|support@billdesk.com|NA|NA|NA|http://eb.php-dev.in/billdesk/index/return';
		//$str = "$merchant_id|$order_inc_id|NA|2.00|NA|NA|NA|$currency_type|NA|R|$security_id|NA|NA|F|NA|NA|NA|NA|NA|NA|NA|$return_url";
		//$str = "$merchant_id|$order_inc_id|NA|$order_grand_total|NA|NA|NA|$currency_type|DIRECT|R|$security_id|NA|NA|F|NA|NA|NA|NA|NA|NA|NA|$return_url";
		$str = "$merchant_id|$order_inc_id|NA|$order_grand_total|NA|NA|NA|$currency_type|DIRECT|R|$security_id|NA|NA|F|$customer_id|NA|NA|NA|NA|NA|NA|$return_url";

    	$checksum = hash_hmac('sha256',$str,$checksum_key,false); 
    	$checksum = strtoupper($checksum);
    
    	Mage::getSingleton('core/session')->setChecksum($checksum);
    
    	$dataWithCheckSumValue = $str."|".$checksum;
 
    	$msg = trim($dataWithCheckSumValue);
    
    	Mage::getSingleton('core/session')->setMsg($msg);
    
	    $getSession =Mage::getSingleton('core/session')->getMsg();
	    $getChecksum =Mage::getSingleton('core/session')->getChecksum();
    
	    Mage::log("String ".$getSession,null,'neo_billdesk.log');
    }
	
	public function updateBillDeskStatusByApi() {

		//$orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('status', 'billdeskpending');
		$orders = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('status', 'billdeskpending');
		
		$td_data = '';
		$td_data .= '<table>';
		$merchant_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/merchant_id');
		$billdesk_api = 'https://www.billdesk.com/pgidsk/PGIQueryController';
		$checksum_key = Mage::getStoreConfig('payment/billdesk_ccdc_net/checksum_key');
		$security_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/security_id');
		$request_type = '0122';
		foreach ($orders as $order) {
			$order_id = $order->getIncrementId();

			Mage::log("Order ID ".$order_id,null,'neo_api.log');
			if($order_id > 0) {
				$td_data .= '<tr><td style="text-align:center;border-bottom:1px solid #ccc;padding:5px 0;width:175px">'.$order_id.'</td>';
				/*Bill Desk Info*/
				$current_datetime = date("YmdHis");
				
				$str = "$request_type|$merchant_id|$order_id|$current_datetime";
				$checksum = hash_hmac('sha256',$str,$checksum_key,false); 
				$checksum = strtoupper($checksum);
				$dataWithCheckSumValue = $str."|".$checksum;
				$msg = trim($dataWithCheckSumValue);
				$data='msg='.$msg;
				$ch=curl_init($billdesk_api);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$msg_response =curl_exec($ch);
				//Mage::log($msg_response,null,'neo_api.log');
				//Mage::log("Result ".$msg_response,null,'neo_billdeskapi.log');
				if(!empty($msg_response)) {
					$msg_params = explode('|',$msg_response);
					Mage::log($msg_params,null,'neo_api.log');
					$auth_status = $msg_params['15'];
					Mage::log("Auth Status ".$auth_status,null,'neo_api.log');
					if($auth_status == "0300"):
						$status_reason = "Success";
					elseif($auth_status == "0399"):
						$status_reason = "Invalid Authentication at Bank";
					elseif($auth_status == "NA"):
						$status_reason = "Invalid Input in the Request Message";
					elseif($auth_status == "0002"):
						$status_reason = "BillDesk is waiting for Response from Bank";
					elseif($auth_status == "0001"):
						$status_reason = "Error at BillDesk";
					endif;
					
					$validated;
			        if($auth_status == "0300"):
			            $validated = "success";
			        elseif($auth_status == "0399" || $auth_status == "NA" || $auth_status == "0001" || $auth_status == "0002"):
			            $validated = "error";
			        endif;

					// $order_data = Mage::getModel('sales/order');
					// $order_data->loadByIncrementId($order_id);
					$payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
					Mage::log("Payment Code ".$payment_method_code,null,'neo_api.log');
					if($validated == "success"){
						if($payment_method_code == 'cashondelivery'){
							$paid = $msg_params['5'];
							/*$order_data->setTotalPaid($paid);
							$order_data->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Successful Transaction.');
							$order_data->setStatus('pendingcod');*/
						}else{
							/*$order_data->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Successful Transaction.');
							$order_data->setStatus('pendingbilldesk');*/
						}
						/*$order_data->addStatusToHistory($order_data->getStatus(),'BillDesk Transaction ID '.$msg_params['3'], false);
						$order_data->sendNewOrderEmail();
						$order_data->setEmailSent(true);
						$order_data->save();*/
						//$td_data .= '<td style="text-align:center;border-bottom:1px solid #ccc;padding:5px 0;width:175px">'.$msg_params['3'].'</td>';
						$td_data .= '<td style="text-align:center;border-bottom:1px solid #ccc;padding:5px 0;width:175px">'.$status_reason.'</td>';
						$td_data .= '<td style="text-align:center;border-bottom:1px solid #ccc;padding:5px 0;width:250px"><a href='.Mage::getBaseUrl().'billdesk/billdeskapi/check/?order_id='.$order_id.'>Financial Approval Pending</a></td></tr>';
					}elseif($validated == "error"){
						try {
							if(!$order->canCancel()) {
								//Mage::log("Cannot Cancel",null,'neo_billdeskapi.log');
							}
							else{
								//Mage::log("Can Cancel",null,'neo_billdeskapi.log');
								/*$order_data->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Cancel Transaction.');
								$order_data->addStatusToHistory($order_data->getStatus(),'BillDesk Transaction ID', false);
								$order_data->save();*/
								//$td_data .= '<td style="text-align:center;border-bottom:1px solid #ccc;padding:5px 0;width:175px">'.$msg_params['3'].'</td>';
								$td_data .= '<td style="text-align:center;border-bottom:1px solid #ccc;padding:5px 0;width:175px">'.$status_reason.'</td>';
								if($auth_status == "0002"){
									$td_data .= '<td style="text-align:center;border-bottom:1px solid #ccc;padding:5px 0;width:250px">Pending</td></tr>';
								}else{
									$td_data .= '<td style="text-align:center;border-bottom:1px solid #ccc;padding:5px 0;width:250px"><a href='.Mage::getBaseUrl().'billdesk/billdeskapi/check/?order_id='.$order_id.'>Canceled</a></td></tr>';	
								}
							}
						}
						catch (Exception $e) {
							Mage::log("ERROR: ".$e,null,'neo_api.log');
						}
					}
				}
				curl_close($ch);
			}
		}

		$td_data .= '</table>';
		if($td_data !='') {
			$this->_sendMail($td_data);
		}
	}
	
	protected function _sendMail($data = FALSE)
    {
        if($data) {
			$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
			$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			//$emailids = 'pradeep.sanku4magento@gmail.com,gayatri@electronicsbazaar.com,mandar@electronicsbazaar.com,sandeep.mukherjee@wwindia.com';
			$emailids = 'pradeep.sanku4magento@gmail.com,sandeepmukherjee14.com,gayatri@electronicsbazaar.com';
			$emailids_array = explode(',', $emailids);
			$current_datetime = date("YmdHis");
			$subject = 'Billdesk '.$current_datetime;
			//Sending E-Mail to Customers.
			//foreach($emailids_array as $emailids_arr){
				$mail = Mage::getModel('core/email')
						 ->setToName('Pradeep')
						 ->setToEmail($emailids_array)
						 ->setBody($data)
						 ->setSubject($subject)
						 ->setFromEmail($senderEmail)
						 ->setFromName($senderName)
						 ->setType('html');
				try{
					$mail->send();
					}catch(Exception $error){
						Mage::getSingleton('core/session')->addError($error->getMessage());
						return false;
				}	
			//}
        }
    }
}
?>