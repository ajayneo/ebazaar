<?php
    class Neo_Sales_Model_Order extends Mage_Sales_Model_Order
    {
        /*protected function _setState($state, $status = false, $comment = '',$isCustomerNotified = null, $shouldProtectState = false)
        {
            // attempt to set the specified state
            if ($shouldProtectState) {
                if ($this->isStateProtected($state)) {
                    Mage::throwException(
                        Mage::helper('sales')->__('The Order State "%s" must not be set manually.', $state)
                    );
                }
            }
            $this->setData('state', $state);
    
            // add status history
            if ($status) {
                if ($status === true) {
                    $status = $this->getConfig()->getStateDefaultStatus($state);
                }
                $this->setStatus($status);
                $history = $this->addStatusHistoryComment($comment, false); // no sense to set $status again
                $history->setIsCustomerNotified($isCustomerNotified); // for backwards compatibility
            }
            // dispatch an event after status has changed
            Mage::dispatchEvent('sales_order_status_after', array('order' => $this, 'state' => $state, 'status' => $status, 'comment' => $comment, 'isCustomerNotified' => $isCustomerNotified, 'shouldProtectState' => $shouldProtectState));
            
            return $this;
        }*/

        /**
         * Send email with order data
         *
         * @return Mage_Sales_Model_Order
         * @throws Exception
         */
        public function sendNewOrderEmail()
        {
            $storeId = $this->getStore()->getId();

            if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
                return $this;
            }

            $emailSentAttributeValue = $this->hasEmailSent()
                ? $this->getEmailSent()
                : Mage::getModel('sales/order')->load($this->getId())->getData('email_sent');
            $this->setEmailSent((bool)$emailSentAttributeValue);
            if ($this->getEmailSent()) {
                return $this;
            }

            // Get the destination email addresses to send copies to
            $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
            $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

            // Start store emulation process
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

            try {
                // Retrieve specified view block from appropriate design package (depends on emulated store)
                $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
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
            if ($this->getCustomerIsGuest()) {
                $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
                $customerName = $this->getBillingAddress()->getName();
            } else {
                $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
                $customerName = $this->getCustomerName();
            }

            $mailer = Mage::getModel('core/email_template_mailer');
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($this->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);

            // Email copies are sent as separated emails if their copy method is 'copy'
            if ($copyTo && $copyMethod == 'copy') {
                foreach ($copyTo as $email) {
                    $emailInfo = Mage::getModel('core/email_info');
                    $emailInfo->addTo($email);
                    $mailer->addEmailInfo($emailInfo);
                }
            }

            // custom code starts added for differenticating the cst and normal orders
            $isordercstused = '';
            $item_quote = Mage::getModel('sales/quote')->load($this->getQuoteId());

            if($item_quote->getCustomerCform() == "true"){
                $isordercstused = '<tr><td style="margin:0;padding:25px 20px 5px 25px;"><div style="color: #565656; padding: 0px; height: 20px; margin: 15px 0px 0px;">Full CST (Applicable) will be recovered if ‘C’ forms are not submitted within 3 months from the date of the order.</div></td></tr>';
            }
            // custom code completed

            if (strpos($paymentBlockHtml, 'Credit Days Payment') !== false){

                $collectionDelivery = Mage::getModel("banktransferdelivery/delivery")->getCollection()->addFieldToSelect('delivery')->addFieldToFilter('order_num',$item_quote->getReservedOrderId())->getFirstItem()->getData();

       
                $da = $collectionDelivery['delivery'] + 5;
                
                $date = explode(' ',$item_quote->getCreatedAt());
                $NewDays = date ("d-M-Y", strtotime ($date[0] ."+".$da." days")); 

                $new = '<br>Credit '.$collectionDelivery['delivery'] . ' days';
                $new .= '<br>Payment Due On '.$NewDays.'<br>';


                $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
                $mailer->setStoreId($storeId);
                $mailer->setTemplateId($templateId);
                $mailer->setTemplateParams(array(
                        'order'        => $this,
                        'billing'      => $this->getBillingAddress(), 
                        //'payment_html' => $paymentBlockHtml.$new,
                        'payment_html' => '<p style="color:#1168ae; display:inline-block;">Credit Days Payment</p>'.$new, 
                        'isordercstused' => $isordercstused 
                    ) 
                );
                $mailer->send();

                $this->setEmailSent(true);
                $this->_getResource()->saveAttribute($this, 'email_sent');
            }
            else{
                // Set all required params and send emails
                $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
                $mailer->setStoreId($storeId);
                $mailer->setTemplateId($templateId);
                $mailer->setTemplateParams(array(
                        'order'        => $this,
                        'billing'      => $this->getBillingAddress(), 
                        'payment_html' => $paymentBlockHtml,
                        'isordercstused' => $isordercstused
                    )
                );
                $mailer->send();

                $this->setEmailSent(true);
                $this->_getResource()->saveAttribute($this, 'email_sent');
            }


            return $this;
        }

        /**
         * Whether specified state can be set from outside
         * @param $state
         * @return bool
         */
        /*public function isStateProtected($state)
        {
            if (empty($state)) {
                return false;
            }
            //return self::STATE_COMPLETE == $state || self::STATE_CLOSED == $state;
            return self::STATE_CLOSED == $state;
        }*/
    }
?>