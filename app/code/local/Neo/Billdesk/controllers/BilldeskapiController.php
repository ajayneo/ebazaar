<?php
class Neo_Billdesk_BilldeskapiController extends Mage_Core_Controller_Front_Action {

	public function checkAction(){
		$order_id = Mage::app()->getRequest()->getParam('order_id');

		if(!$order_id){
			$this->norouteAction();
			return;
		}

		Mage::log('Order Id:-'.$order_id,null,'neo_billdesk_c_api.log');

		$merchant_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/merchant_id');
		$billdesk_api = 'https://www.billdesk.com/pgidsk/PGIQueryController';
		$checksum_key = Mage::getStoreConfig('payment/billdesk_ccdc_net/checksum_key');
		$security_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/security_id');
		$request_type = '0122';
		
		if($order_id > 0) {
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
			if(!empty($msg_response)) {
				$msg_params = explode('|',$msg_response);
				Mage::log($msg_params,null,'neo_billdesk_c_api.log');
				$auth_status = $msg_params['15'];
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
				$order_data = Mage::getModel('sales/order');
				$order_data->loadByIncrementId($order_id);
				$payment_method_code = $order_data->getPayment()->getMethodInstance()->getCode();
				if($validated == "success"){
					if($payment_method_code == 'cashondelivery'){
						$paid = $msg_params['5'];
						$order_data->setTotalPaid($paid);
						$order_data->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Successful Transaction.');
						$order_data->setStatus('pendingcod');
					}elseif($payment_method_code == 'billdesk_ccdc_net'){
						$order_data->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Successful Transaction.');
						$order_data->setStatus('pendingbilldesk');
					}
					$order_data->addStatusToHistory($order_data->getStatus(),'BillDesk Transaction ID '.$msg_params['3'], false);
					$order_data->addStatusToHistory($order_data->getStatus(),'Updated with Billdesk Api', false);
					$order_data->sendNewOrderEmail();
					$order_data->setEmailSent(true);
					$order_data->save();
					echo 'Order '.$order_id.' has been updated with status Success';
				}elseif($validated == "error"){
					try {
						if(!$order_data->canCancel()) {
							//Mage::log("Cannot Cancel",null,'neo_billdeskapi.log');
						}
						else{
							//Mage::log("Can Cancel",null,'neo_billdeskapi.log');
							$order_data->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Cancel Transaction.');
							$order_data->addStatusToHistory($order_data->getStatus(),'BillDesk Transaction ID:-'.$msg_params['3'], false);
							$order_data->addStatusToHistory($order_data->getStatus(),'Updated with Billdesk Api', false);
							$order_data->save();
							echo 'Order '.$order_id.' has been updated with status Cancelled';
						}
					}
					catch (Exception $e) {
						Mage::log("ERROR: ".$e,null,'neo_billdesk_c_api.log');
					}
				}
			}
			curl_close($ch);
		}
	}
}