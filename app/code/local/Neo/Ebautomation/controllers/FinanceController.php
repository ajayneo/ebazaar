<?php
class Neo_Ebautomation_FinanceController extends Mage_Core_Controller_Front_Action{
	/**
	** @Developer: Mahesh Gurav
	** @Date: 06 Feb 2017
	** @Desc: Billdesk payment transaction status
	**/
	public function billdeskAction() {
        $date = date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        $orders = Mage::getModel('sales/order')->getCollection();
        $orders->getSelect()->join(
            array('p' => $orders->getResource()->getTable('sales/order_payment')),
            'p.parent_id = main_table.entity_id',
            array()
        );
        
        $orders->addFieldToFilter('p.method','billdesk_ccdc_net');
        $orders->addFieldToFilter('main_table.state','new');
        $orders->addFieldToFilter('main_table.created_at',array('lteq'=>$date));

        $merchant_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/merchant_id');
        $billdesk_api = 'https://www.billdesk.com/pgidsk/PGIQueryController';
        $checksum_key = Mage::getStoreConfig('payment/billdesk_ccdc_net/checksum_key');
        $security_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/security_id');
        $request_type = '0122';
        $ch = '';
        $str = '';
        $checksum = '';
        foreach ($orders as $order) {
        	$order_id = $order->getIncrementId();
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
                //$msg_labels = "RequestType|MerchantID|CustomerID|TxnReferenceNo|BankReferenceNo|TxnAmount|Bank
				// ID|BankMerchantID|TxnType|CurrencyName|ItemCode|SecurityType|SecurityID|SecurityPa
				// ssword|TxnDate|AuthStatus|SettlementType|AdditionalInfo1|AdditionalInfo2|AdditionalInfo
				// 3|AdditionalInfo4|AdditionalInfo5|AdditionalInfo6|AdditionalInfo7|ErrorStatus|ErrorDescripti
				// on|Filler1|RefundStatus|TotalRefundAmount|LastRefundDate|LastRefundRefNo|QueryStatus
				// |CheckSum";
                
                $msg_params = explode('|',$msg_response);
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

                $refund_status = $msg_params['27'];
                if($refund_status == "NA"):
                    $refund_reason = "Currently not refunded or cancelled";
                elseif($refund_status == "0699"):
                    $refund_reason = "Refunded back to customer";
                elseif($refund_status == "0799"):
                    $refund_reason = "Refund [either partial/full] was initiated for this transaction";
                endif;

                $transaction_id = $msg_params['3'];

                //$log = "Billdesk Order#".$order_id." Auth#".$auth_status." Refund#".$refund_status;
                //Mage::log($log,null,"finance.log",true); 
                #update order status with transaction
                if($auth_status == "0300" && $refund_status == "NA"){
                    $success_message = "Settled - BillDesk Transaction ID ".$transaction_id." [auth_status = ".$auth_status." refund_status = ".$refund_status."]";
                    // $_order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
                    // $_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Billdesk Transaction Settled');
                    // $_order->setStatus('financeapproved');
                    // $_order->addStatusToHistory($_order->getStatus(),$success_message, false);
                    // $_order->save();
                }
			}else{
                // $log = "Billdesk no response";
                // Mage::log($log,null,"finance.log",true); 
            }
            curl_close($ch);
        }
	}

}?>