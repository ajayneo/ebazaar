<?php

class InfoDesires_CashOnDelivery_Model_Observer extends Mage_Core_Model_Abstract 
{
    /**
     * Collects codFee from qoute/addresses to quote
     *
     * @param Varien_Event_Observer $observer
     *
     */
    public function sales_quote_collect_totals_after(Varien_Event_Observer $observer) 
    {        
        $quote = $observer->getEvent()->getQuote();
        $data = $observer->getInput();
        //unused paramter
        // $app_order = $_REQUEST['app_order']; // added for app
        $app_cod_fee = $quote->getCodFee(); // added for app

        if(!isset($_REQUEST['app_order'])){ // added for app
            $quote->setCodFee(0);
            $quote->setBaseCodFee(0);
            $quote->setCodTaxAmount(0);
            $quote->setBaseCodTaxAmount(0);
        } // added for app
        
        foreach ($quote->getAllAddresses() as $address) {
            $quote->setCodFee((float) $quote->getCodFee() + $address->getCodFee());
            $quote->setBaseCodFee((float) $quote->getBaseCodFee() + $address->getBaseCodFee());

            $quote->setCodTaxAmount((float) $quote->getCodTaxAmount() + $address->getCodTaxAmount());
            $quote->setBaseCodTaxAmount((float) $quote->getBaseCodTaxAmount() + $address->getBaseCodTaxAmount());
			
			//added
			$quote->setTotalPaid((float)($quote->getCodFee() + $address->getCodFee()));
			$quote->setBaseTotalPaid((float)($quote->getBaseCodFee() + $address->getBaseCodFee()));
        }
		//added
		return $this;
    }

    /**
     * Adds codFee to order
     * 
     * @param Varien_Event_Observer $observer
     */
    public function sales_order_payment_place_end(Varien_Event_Observer $observer) 
    {
        $payment = $observer->getPayment();
        if ($payment->getMethodInstance()->getCode() != 'cashondelivery') {
            return;
        }

        $order = $payment->getOrder();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (!$quote->getId()) {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }

        // code added for app orders which is not able to get the quote session
        if (!$quote->getId()) {
            $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
        }

        $order->setCodFee($quote->getCodFee());
        $order->setBaseCodFee($quote->getBaseCodFee());

        $order->setCodTaxAmount($quote->getCodTaxAmount());
        $order->setBaseCodTaxAmount($quote->getBaseCodTaxAmount());
		
		// added
		$order->setTotalPaid($quote->getCodFee());
		$order->setBaseTotalPaid($quote->getBaseCodFee());	
		//if ($quote->getCodFee()){
			$order->setIsCod(1);	
		//}

        $order->save();
		
		//return $this;
    }

    /**
     * Performs order_creage_loadBlock response update
     * adds totals block to each response
     * This function is depricated, the totals block update is implemented
     * in InfoDesires/cashondelivery/sales.js (SalesOrder class extension)
     * 
     * @param Varien_Event_Observer $observer
     */
    public function controller_action_layout_load_before(Varien_Event_Observer $observer) 
    {        
        $action = $observer->getAction();
        if ($action->getFullActionName() != 'adminhtml_sales_order_create_loadBlock' || !$action->getRequest()->getParam('json')) {
            return;
        }
        $layout = $observer->getLayout();
        $layout->getUpdate()->addHandle('adminhtml_sales_order_create_load_block_totals');
    }
	
	/**
     * When the order gets canceled we put the Cash on Delivery fee and tax also in the canceled columns.
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_CashOnDelivery_Model_Observer
     */
    public function order_cancel_after(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (($order->getPayment()->getMethodInstance()->getCode() != 'phoenix_cashondelivery')   && ($order->getPayment()->getMethodInstance()->getCode() != 'emb_ivr')) {
            return $this;
        }
	
		
        $codFee     = $order->getCodFee();
        $baseCodFee = $order->getBaseCodFee();
        $codTax     = $order->getCodTaxAmount();
        $baseCodTax = $order->getBaseCodTaxAmount();

        $codFeeInvoiced     = $order->getCodFeeInvoiced();

        if ($codFeeInvoiced) {
            $baseCodFeeInvoiced = $order->getBaseCodFeeInvoiced();
            $codTaxInvoiced     = $order->getCodTaxAmountInvoiced();
            $baseCodTaxInvoiced = $order->getBaseCodTaxAmountInvoiced();

            $codFee     = $codFee     - $codFeeInvoiced;
            $baseCodFee = $baseCodFee - $baseCodFeeInvoiced;
            $codTax     = $codTax     - $codTaxInvoiced;
            $baseCodTax = $baseCodTax - $baseCodTaxInvoiced;
        }

        if ($baseCodFee) {
            $order->setCodFeeCanceled($codFee)
                  ->setBaseCodFeeCanceled($baseCodFee)
                  ->setCodTaxAmountCanceled($codTax)
                  ->setBaseCodTaxAmountCanceled($baseCodTax)
                  ->save();
        }

        return $this;
    }

}