<?php
    require_once 'Mage/Checkout/controllers/CartController.php';
    class Neo_Checkout_CartController extends Mage_Checkout_CartController
    {
        /**
        * Shopping cart display action
        */
        public function indexAction()
        {
            $cart = $this->_getCart();
            if ($cart->getQuote()->getItemsCount()) {
                $cart->init();
                $cart->save();
    
                if (!$this->_getQuote()->validateMinimumAmount()) {
                    $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                        ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));
    
                    $warning = Mage::getStoreConfig('sales/minimum_order/description')
                        ? Mage::getStoreConfig('sales/minimum_order/description')
                        : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);
    
                    $cart->getCheckoutSession()->addNotice($warning);
                }
            }
    
            // Compose array of messages to add
            $messages = array();
            foreach ($cart->getQuote()->getMessages() as $message) {
                if ($message) {
                    // Escape HTML entities in quote message to prevent XSS
                    $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                    $messages[] = $message;
                }
            }
            $cart->getCheckoutSession()->addUniqueMessages($messages);
    
            /**
             * if customer enteres shopping cart we should mark quote
             * as modified bc he can has checkout page in another window.
             */
            $this->_getSession()->setCartWasUpdated(true);
    
            Varien_Profiler::start(__METHOD__ . 'cart_display');
            $this
                ->loadLayout()
                ->_initLayoutMessages('checkout/session')
                ->_initLayoutMessages('catalog/session')
                ->getLayout()->getBlock('head')->setTitle($this->__('My Shopping Cart : Electronicsbazaar.com'));
            $this->renderLayout();
            Varien_Profiler::stop(__METHOD__ . 'cart_display');
        }

		/**
     * Minicart delete action
     */
    public function ajaxDeleteAction()
    {
        if (!$this->_validateFormKey()) {
            Mage::throwException('Invalid form key');
        }
        $id = (int) $this->getRequest()->getParam('id');
        $result = array();
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)->save();

                $result['qty'] = $this->_getCart()->getSummaryQty();
				$result['basetotal'] = 'Rs. '. Mage::helper('checkout/cart')->getQuote()->getGrandTotal();
				
                $this->loadLayout();
                $result['content'] = $this->getLayout()->getBlock('minicart_content')->toHtml();

                $result['success'] = 1;
                $result['message'] = $this->__('Item was removed successfully.');
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $this->__('Can not remove the item.');
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /*    protected function _goBack(){
            $returnUrl = $this->getRequest()->getParam('return_url');
            if ($returnUrl) {

                if (!$this->_isUrlInternal($returnUrl)) {
                    throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
                }

                $this->_getSession()->getMessages(true);
                $this->getResponse()->setRedirect($returnUrl);
            } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
                && !$this->getRequest()->getParam('in_cart')
                && $backUrl = $this->_getRefererUrl()
            ) {
                $this->getResponse()->setRedirect($backUrl);
            } else {
                if ((strtolower($this->getRequest()->getActionName()) == 'add') && !$this->getRequest()->getParam('in_cart')) {
                    $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
                }
                //$this->_redirect('indiagst/index/gstin');
                $check_gst = 0;
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $customerData = Mage::getSingleton('customer/session')->getCustomer();
                    $customer_id = $customerData->getId();
                    $check_gst = Mage::helper('indiagst')->getGstDetails($customer_id);
                }
                

                if($check_gst == 0){
                    $this->_redirect('indiagst/index/gstin');
                }else{
                    $this->_redirect('onepage');
                }


            }
            return $this;
        }*/


    }
?>