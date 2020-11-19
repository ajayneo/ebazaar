<?php
class Neo_Billdesk_IndexController extends Mage_Core_Controller_Front_Action {
    
    public function returnAction() {
        $msg = $this->getRequest()->getParam('msg');
        Mage::log($msg,null,'neo_billdesk_response.log');

        $strpos = strrpos($msg,"|");
        $strlength = strlen(substr($msg,$strpos));
        $str_without_checksum = substr($msg, 0, -$strlength);
        $str_withonly_checksum = substr($msg,$strpos+1);
        $checksum_key = Mage::getStoreConfig('payment/billdesk_ccdc_net/checksum_key');

        $checksum = hash_hmac('sha256',$str_without_checksum,$checksum_key,false); 
        $checksum = strtoupper($checksum);

        Mage::log("Calculated Checksum:-".$checksum,null,'neo_billdesk_response.log');
        Mage::log("Billdesk Checksum:-".$str_withonly_checksum,null,'neo_billdesk_response.log');

        if($checksum == $str_withonly_checksum){
            $msg_params = explode('|',$msg);
            Mage::log($msg_params,null,'neo_billdesk_params.log');
            $getChecksum = Mage::getSingleton('core/session')->getChecksum();
            
            $billdesk_checksum = $msg_params[25];
            
            $_orderid = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            
            $auth_status = $msg_params['14'];
            Mage::log($auth_status,null,'auth_status.log');
            
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
            elseif($auth_status == "0399" || $auth_status == "NA" || $auth_status == "0001"):
                $validated = "error";
            endif;
            
            if($validated == "success")
            {
                Mage::log('if',null,'auth_status.log');
                $order = Mage::getModel('sales/order');
                $order->loadByIncrementId($_orderid);

                $payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
                if($payment_method_code == 'cashondelivery')
                {
                    // comment this 2 lines of code when order was issue in the cod on 5th Dec 2014
                    //$due = $order->getGrandTotal() + $msg_params['4'];
                    //$due = $order->getGrandTotal();
                    //$order->setGrandTotal($due);

                    //$due = $order->getGrandTotal() + $msg_params['4'];
                    $paid = $msg_params['4'];
                    $order->setTotalPaid($paid);
                    //$order->setTotalDue($due);
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Successful Transaction.');
                    $order->setStatus('pendingcod');
                }else{
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Successful Transaction.');
                    $order->setStatus('pendingbilldesk');
                }
                
                $order->addStatusToHistory($order->getStatus(),'BillDesk Transaction ID '.$msg_params['2'], false);
                $order->sendNewOrderEmail();
                $order->setEmailSent(true);
                $order->save();
                Mage::getSingleton('checkout/session')->unsQuoteId();
                Mage::getSingleton('core/session')->unsChecksum();
                Mage::getSingleton('core/session')->unsMsg();
                $this->_redirect('checkout/onepage/success', array('_secure'=>true));
            }elseif($validated == "error"){
                Mage::log('else',null,'auth_status.log');
                if(Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
                    $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
                    if($order->getId()) {
                        $payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
                        if($payment_method_code == 'cashondelivery'){
                            // comment this 2 lines of code when order was issue in the cod on 5th Dec 2014
                            //$due = $order->getGrandTotal() + $msg_params['4'];
                            //$order->setGrandTotal($due);
                        }
                        
                        // Flag the order as 'cancelled' and save it
                        $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Cancel Transaction.');
                        $order->addStatusToHistory($order->getStatus(),'BillDesk Transaction ID '.$msg_params['2'], false)->save();
                    }
                }
               $this->_redirect('checkout/onepage/failure', array('_secure'=>true));
            }
        }
    }
}
?>
