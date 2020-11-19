<?php
    class Neo_Checkout_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
    {
    	/**
	     * Save billing address information to quote
	     * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
	     *
	     * @param   array $data
	     * @param   int $customerAddressId
	     * @return  Mage_Checkout_Model_Type_Onepage
	     */
	    public function saveBilling($data, $customerAddressId)
	    {
	        if (empty($data)) {
	            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
	        }
	
	        $address = $this->getQuote()->getBillingAddress();
	        $this->getQuote()->setData("customer_cform",$data['cform']); // added by pradeep
	        //Mage::log('saveBilling',null,'saveBilling.log');
	        /* @var $addressForm Mage_Customer_Model_Form */
	        $addressForm = Mage::getModel('customer/form');
	        $addressForm->setFormCode('customer_address_edit')
	            ->setEntityType('customer_address')
	            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());
	
	        if (!empty($customerAddressId)) {
	            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
	            if ($customerAddress->getId()) {
	                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
	                    return array('error' => 1,
	                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
	                    );
	                }
	
	                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
	                $addressForm->setEntity($address);
	                $addressErrors  = $addressForm->validateData($address->getData());
	                if ($addressErrors !== true) {
	                    return array('error' => 1, 'message' => $addressErrors);
	                }
	            }
	        } else {
	            $addressForm->setEntity($address);
	            // emulate request object
	            $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
	            $addressErrors  = $addressForm->validateData($addressData);
	            if ($addressErrors !== true) {
	                return array('error' => 1, 'message' => array_values($addressErrors));
	            }
	            $addressForm->compactData($addressData);
	            //unset billing address attributes which were not shown in form
	            foreach ($addressForm->getAttributes() as $attribute) {
	                if (!isset($data[$attribute->getAttributeCode()])) {
	                    $address->setData($attribute->getAttributeCode(), NULL);
	                }
	            }
	            $address->setCustomerAddressId(null);
	            // Additional form data, not fetched by extractData (as it fetches only attributes)
	            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
	        }
	
	        // set email for newly created user
	        if (!$address->getEmail() && $this->getQuote()->getCustomerEmail()) {
	            $address->setEmail($this->getQuote()->getCustomerEmail());
	        }
	
	        // validate billing address
	        if (($validateRes = $address->validate()) !== true) {
	            return array('error' => 1, 'message' => $validateRes);
	        }
	
	        $address->implodeStreetAddress();
	
	        if (true !== ($result = $this->_validateCustomerData($data))) {
	            return $result;
	        }
	
	        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
	            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
	                return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
	            }
	        }
	
	        if (!$this->getQuote()->isVirtual()) {
	            /**
	             * Billing address using otions
	             */
	            $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;
	
	            switch ($usingCase) {
	                case 0:
	                    $shipping = $this->getQuote()->getShippingAddress();
	                    $shipping->setSameAsBilling(0);
	                    break;
	                case 1:
	                    $billing = clone $address;
	                    $billing->unsAddressId()->unsAddressType();
	                    $shipping = $this->getQuote()->getShippingAddress();
	                    $shippingMethod = $shipping->getShippingMethod();
	
	                    // Billing address properties that must be always copied to shipping address
	                    $requiredBillingAttributes = array('customer_address_id');
	
	                    // don't reset original shipping data, if it was not changed by customer
	                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
	                        if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
	                            && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
	                        ) {
	                            $billing->unsetData($shippingKey);
	                        }
	                    }
	                    $shipping->addData($billing->getData())
	                        ->setSameAsBilling(1)
	                        ->setSaveInAddressBook(0)
	                        ->setShippingMethod($shippingMethod)
	                        ->setCollectShippingRates(true);
	                    $this->getCheckout()->setStepData('shipping', 'complete', true);
	                    break;
	            }
	        }
	
	        $this->getQuote()->collectTotals();
	        $this->getQuote()->save();
	
	        if (!$this->getQuote()->isVirtual() && $this->getCheckout()->getStepData('shipping', 'complete') == true) {
	            //Recollect Shipping rates for shipping methods
	            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
	        }
	
	        $this->getCheckout()
	            ->setStepData('billing', 'allow', true)
	            ->setStepData('billing', 'complete', true)
	            ->setStepData('shipping', 'allow', true);
	
	        return array();
	    }

		/**
	     * Create order based on checkout type. Create customer if necessary.
	     *
	     * @return Mage_Checkout_Model_Type_Onepage
	     */
	    public function saveOrder()
	    {
	        $this->validate();
	        $isNewCustomer = false;
	        switch ($this->getCheckoutMethod()) {
	            case self::METHOD_GUEST:
	                $this->_prepareGuestQuote();
	                break;
	            case self::METHOD_REGISTER:
	                $this->_prepareNewCustomerQuote();
	                $isNewCustomer = true;
	                break;
	            default:
	                $this->_prepareCustomerQuote();
	                break;
	        }
	
	        //added by pradeep
	        $billing = $this->getQuote()->getBillingAddress();
	        if ($this->getQuote()->getData("customer_cform") && !$billing->getData("cform")) {
	            //Save in the customer
	            $this->getQuote()->getCustomer()->setData("cform", $this->getQuote()->getData("customer_cform"));
	        }
			//Mage::log('saveOrder',null,'saveOrder.log');
	        //end of pradeep
	
	        $service = Mage::getModel('sales/service_quote', $this->getQuote());
	        $service->submitAll();
	
	        if ($isNewCustomer) {
	            try {
	                $this->_involveNewCustomer();
	            } catch (Exception $e) {
	                Mage::logException($e);
	            }
	        }
	
	        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
	            ->setLastSuccessQuoteId($this->getQuote()->getId())
	            ->clearHelperData();
	
	        $order = $service->getOrder();
	        if ($order) {
	            Mage::dispatchEvent('checkout_type_onepage_save_order_after',
	                array('order'=>$order, 'quote'=>$this->getQuote()));
	
	            /**
	             * a flag to set that there will be redirect to third party after confirmation
	             * eg: paypal standard ipn
	             */
	            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
	            /**
	             * we only want to send to customer about new order when there is no redirect to third party
	             */
	            if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
	                try {
	                    $order->sendNewOrderEmail();
	                } catch (Exception $e) {
	                    Mage::logException($e);
	                }
	            }
	
	            // add order information to the session
	            $this->_checkoutSession->setLastOrderId($order->getId())
	                ->setRedirectUrl($redirectUrl)
	                ->setLastRealOrderId($order->getIncrementId());
	
	            // as well a billing agreement can be created
	            $agreement = $order->getPayment()->getBillingAgreement();
	            if ($agreement) {
	                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
	            }
	        }
	
	        // add recurring profiles information to the session
	        $profiles = $service->getRecurringPaymentProfiles();
	        if ($profiles) {
	            $ids = array();
	            foreach ($profiles as $profile) {
	                $ids[] = $profile->getId();
	            }
	            $this->_checkoutSession->setLastRecurringProfileIds($ids);
	            // TODO: send recurring profile emails
	        }
	
	        Mage::dispatchEvent(
	            'checkout_submit_all_after',
	            array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
	        );
	
	        return $this;
	    }	
    }
?>