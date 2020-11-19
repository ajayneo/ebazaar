<?php
    class Neo_CancelOrderNotify_Model_Observer
    {
    	public function notifyCustomerOnOrderCancel($observer){
    		$order = $observer->getOrder();
			$orderStatus = $order->getStatus();
			if($orderStatus == Mage_Sales_Model_Order::STATE_CANCELED)
				$this->_sendCancelOrderNotifyMail($order);
    	}
		
		private function _sendCancelOrderNotifyMail($order){
			$emailTemplate = Mage::getModel('core/email_template');
			$emailTemplate->loadDefault('custom_order_tpl');
			$emailTemplate->setTemplateSubject('Cancellation of your Order '.$order->getIncrementId().' with ElectronicsBazaar.com');
			
			$salesData['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
			$salesData['name'] = Mage::getStoreConfig('trans_email/ident_general/name');

			$eb_bcc_emails = Mage::getStoreConfig('neo_cancelordernotify_section/neo_cancelordernotify_group/notify_emails_field');
			$eb_bcc_emails_array = explode(',',$eb_bcc_emails);

			Mage::log($eb_bcc_emails,null,'NEOCON.log');
			
			//$copyTo = array_merge($asm_bcc_emails_array,$eb_bcc_emails_array);
			
			$emailTemplateVariables['username'] = $order->getCustomerFirstname().''.$order->getCustomerLastname();
			$emailTemplateVariables['order_id'] = $order->getIncrementId();
			$emailTemplateVariables['store_name'] = $order->getStoreName();
			$emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

			$emailTemplate->setSenderName($salesData['name']);
			$emailTemplate->setSenderEmail($salesData['email']);
			//$emailTemplate->setToName($emailTemplateVariables['username']);
			$emailTemplate->addBcc($eb_bcc_emails_array);

			$emailTemplate->send($order->getCustomerEmail(),$emailTemplateVariables['username'],$emailTemplateVariables);
		}
    }
?>