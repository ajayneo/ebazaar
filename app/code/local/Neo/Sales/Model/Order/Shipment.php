<?php
class Neo_Sales_Model_Order_Shipment extends Mage_Sales_Model_Order_Shipment
{
    /**
     * Send email with shipment data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewShipmentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');

        $asm_bcc_emails_array = array();
        Mage::log('ASM SHIPPING EMAILS',null,'ASM.log');
        $is_enabled = Mage::getStoreConfig('neoasm_section/neoasm_group/neoasm_enabled');
        if($is_enabled){
            $order_groupid = $order->getCustomerGroupId();
            if($order_groupid == 4){
                // asm email code
 
                $customerId = $order->getCustomerId(); 
                $customerModel = Mage::getModel('customer/customer');
                $refferedById = $customerModel->load($customerId)->getAsmMap();  

                //$asm_bcc_emails = Mage::getModel("asmdetail/asmdetail")->getCollection()->addFieldToFilter('name',$refferedById)->getFirstItem()->getEmail();
                $asm_bcc_emails = Mage::getModel("asmdetail/asmdetail")->getCollection()->addFieldToSelect('email')->addFieldToSelect('rsmemail')->addFieldToFilter('name',$refferedById)->getFirstItem()->getData(); 
                
                if($asm_bcc_emails['email'] == 'vivekanand@electronicsbazaar.com'){
                        $asm_bcc_emails[] = 'binod@electronicsbazaar.com';
                    }

                //$asm_bcc_emails_array = explode(',', $asm_bcc_emails);
                $asm_bcc_emails_array = array_values($asm_bcc_emails);

            }   
        }

        if ($copyTo){
            $copyTo = array_merge($copyTo,$asm_bcc_emails_array);
        }

        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'shipment'     => $this,
                'comment'      => $comment,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        $mailer->send();

        return $this;
    }
}
