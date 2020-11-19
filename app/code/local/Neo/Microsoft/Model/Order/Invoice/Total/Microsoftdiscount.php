<?php
class Neo_Microsoft_Model_Order_Invoice_Total_Microsoftdiscount
extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$order=$invoice->getOrder();
        $orderMicrosoftdiscountTotal = $order->getMicrosoftdiscountTotal();
        if ($orderMicrosoftdiscountTotal&&count($order->getInvoiceCollection())==0) {
            $invoice->setGrandTotal($invoice->getGrandTotal()+$orderMicrosoftdiscountTotal);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$orderMicrosoftdiscountTotal);
        }
        return $this;
    }
}