<?php
class Neo_Affiliatecommision_Model_Order_Invoice_Total_Affiliatecommision
extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$order=$invoice->getOrder();
        $orderAffiliatecommisionTotal = $order->getAffiliatecommisionTotal();
        if ($orderAffiliatecommisionTotal&&count($order->getInvoiceCollection())==0) {
            $invoice->setGrandTotal($invoice->getGrandTotal()+$orderAffiliatecommisionTotal);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$orderAffiliatecommisionTotal);
        }
        return $this;
    }
}