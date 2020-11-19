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
    }
?>