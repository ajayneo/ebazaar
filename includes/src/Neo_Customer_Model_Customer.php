<?php
	class Neo_Customer_Model_Customer extends Mage_Customer_Model_Customer
	{
		/**
		 * Override because to added emailids to get welcome emails of customers to the eb team
	     * Send corresponding email template
	     * @author Pradeep Sanku
	     * @param string $emailTemplate configuration path of email template
	     * @param string $emailSender configuration path of email identity
	     * @param array $templateParams
	     * @param int|null $storeId
	     * @return Mage_Customer_Model_Customer
	     */
	    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null)
	    {
	        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
	        $mailer = Mage::getModel('core/email_template_mailer');
	        $emailInfo = Mage::getModel('core/email_info');
	        $emailInfo->addTo($this->getEmail(), $this->getName());
			/* email ids */
			//$bcc = array("mandar@electronicsbazaar.com","akhilesh@electronicsbazaar.com","keval@electronicsbazaar.com","yogesh@electronicsbazaar.com","sandeep.mukherjee@wwindia.com");

			//$bcc = array("yogesh@electronicsbazaar.com");   
			//$emailInfo->addBcc($bcc);  
			//$emailInfo->addBcc($bcc); 
			
			/* email ids */
	        $mailer->addEmailInfo($emailInfo);
	
	        // Set all required params and send emails
	        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
	        $mailer->setStoreId($storeId);
	        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
	        $mailer->setTemplateParams($templateParams);
	        $mailer->send();
	        return $this;
	    }
	}
?>