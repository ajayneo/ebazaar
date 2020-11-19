<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../app/Mage.php';
Mage::app();

const CAPTURE_ONLINE   = 'online';
const CAPTURE_OFFLINE  = 'offline';
const NOT_CAPTURE      = 'not_capture';
try{
	$increment_id = 200046277;
	$imeis = array('Mobile3165'=>'54678941234578965');
	$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
	
	if(!$order->canInvoice())
	{
		Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
	}
	
	$capture = Mage_Sales_Model_Order_Invoice::NOT_CAPTURE;
    /** @var Mage_Sales_Model_Order_Invoice $invoice */
    $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

    if (!$invoice->getTotalQty()) {
		Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
	}

    $invoice->setRequestedCaptureCase($capture);
    $invoice->register();

    $transaction = Mage::getModel('core/resource_transaction')
        ->addObject($invoice)
        ->addObject($invoice->getOrder());

    $transaction->save();


}catch(Exception $e){
	echo $e->getMessage();
}