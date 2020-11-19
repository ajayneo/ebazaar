<?php
/*
HDFC Bank Payment Controller
By: Pradeep Sanku
*/

class Neo_Hdfccdl_PaymentController extends Mage_Core_Controller_Front_Action {
	
	// The redirect action is triggered when someone places an order
	public function redirectAction() {
		$this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','hdfccdl',array('template' => 'hdfccdl/redirect.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
	}

	// The response action is triggered when billdesk sends response back to merchant's website
	public function responseAction(){
		if($this->getRequest()->isPost()){
			$msg = $this->getRequest()->getParam('msg');
			Mage::log('Message as Response',null,'Neo_HDFC.log');
			Mage::log($msg,null,'Neo_HDFC.log');
			$strpos = strrpos($msg,"|");
			$strlength = strlen(substr($msg,$strpos));
			$str_without_checksum = substr($msg, 0, -$strlength);
			$str_withonly_checksum = substr($msg,$strpos+1);

			$checksum_key = Mage::getStoreConfig('payment/hdfc_cdl/checksum_key');

			$checksum = hash_hmac('sha256',$str_without_checksum,$checksum_key,false); 
			$checksum = strtoupper($checksum);

			Mage::log('Checksum from the MSG Param',null,'Neo_HDFC.log');
			Mage::log($str_withonly_checksum,null,'Neo_HDFC.log');
			Mage::log('Calculated Checksum',null,'Neo_HDFC.log');
			Mage::log($checksum,null,'Neo_HDFC.log');

			if($checksum == $str_withonly_checksum){
			    $msg_params = explode('|',$msg);
			    $_orderid = Mage::getSingleton('checkout/session')->getLastRealOrderId();
				$auth_status = $msg_params['14'];

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

			    if($validated == "success"){
			        $order = Mage::getModel('sales/order')->loadByIncrementId($_orderid);
			        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Successful Transaction.');
			        $order->setStatus('hdfccdlfinanceapproved');
			        $order->addStatusToHistory($order->getStatus(),'BillDesk PG Txn Ref Number:-'.$msg_params['2'], false);
			        $order->sendNewOrderEmail();
			        $order->setEmailSent(true);
			        $order->save();
			        Mage::getSingleton('checkout/session')->unsQuoteId();
			        Mage::getSingleton('core/session')->unsChecksum();
			        Mage::getSingleton('core/session')->unsMsg();
			        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
			    }elseif($validated == "error"){
			        if(Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
			            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
			            if($order->getId()) {
			            	// Flag the order as 'cancelled' and save it
			            	$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true,$status_reason);
			            	$order->addStatusToHistory($order->getStatus(),'BillDesk PG Txn Ref Number:-'.$msg_params['2'], false)->save();
			        	}
			    	}
			    	$this->_redirect('checkout/onepage/failure', array('_secure'=>true));
			    }
			}
		}else{
			Mage_Core_Controller_Varien_Action::_redirect('');
		}
	}
}