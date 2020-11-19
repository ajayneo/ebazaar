<?php
class Neo_Pdf_Block_Order_Invoice_Items extends Mage_Sales_Block_Order_Invoice_Items
{
    public function getPrintInvoiceUrl($invoice)
    {
		$addParams = array('invoice_id' => $invoice->getId());
		if(Mage::app()->getRequest()->getParam('receipt') == "1"){
			$addParams = array('invoice_id' => $invoice->getId(),'receipt'=>1);
		}
        return Mage::getUrl('*/*/printInvoice', $addParams);
    }

    public function getPrintAllInvoicesUrl($order)
    {
		$addParams = array('order_id' => $order->getId());
		if(Mage::app()->getRequest()->getParam('receipt') == "1"){
			$addParams = array('order_id' => $order->getId(),'receipt'=>1);
		}
        return Mage::getUrl('*/*/printInvoice', $addParams);
    }
}
