<?php

class InfoDesires_CashOnDelivery_Model_Invoice_Total extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();

        /*if ($order->getPayment()->getMethodInstance()->getCode() != 'cashondelivery'){
            return $this;
        }

        if (!$order->getCodFee()){
            return $this;
        }*/
        
        if (($order->getPayment()->getMethodInstance()->getCode() != 'cashondelivery' || !$order->getCodFee()) && $order->getIsCod() == '0') {
            return $this;
        }

        foreach ($invoice->getOrder()->getInvoiceCollection() as $previusInvoice) {
            if ($previusInvoice->getCodAmount() && !$previusInvoice->isCanceled()) {
                $includeCodTax = false;
            }
        }

		$orderSubtot 		= $order->getSubtotal();
		$orderBaseSubtot 	= $order->getBaseSubtotal();
		
        $baseCodFee = $order->getBaseCodFee();
        $baseCodFeeInvoiced = $order->getBaseCodFeeInvoiced();
        $baseInvoiceTotal = $invoice->getBaseGrandTotal();
        $codFee = $order->getCodFee();
        $codFeeInvoiced = $order->getCodFeeInvoiced();
        $invoiceTotal = $invoice->getGrandTotal();

        if (!$baseCodFee || $baseCodFeeInvoiced==$baseCodFee) {
            return $this;
        }

        $baseCodFeeToInvoice = $baseCodFee - $baseCodFeeInvoiced;
        $codFeeToInvoice = $codFee - $codFeeInvoiced;

        $baseInvoiceTotal = $baseInvoiceTotal - $baseCodFeeToInvoice;
        $invoiceTotal = $invoiceTotal - $codFeeToInvoice;

        $invoice->setBaseGrandTotal($baseInvoiceTotal);
        $invoice->setGrandTotal($invoiceTotal);

        $invoice->setBaseCodFee($baseCodFeeToInvoice);
        $invoice->setCodFee($codFeeToInvoice);
		if($codFee){
			$invoice->setIsCod(1);
		}

        $order->setBaseCodFeeInvoiced($baseCodFeeInvoiced+$baseCodFeeToInvoice);
        $order->setCodFeeInvoiced($codFeeInvoiced+$codFeeToInvoice);
		
		
		/*$order->setBaseCodFeeInvoiced($baseCodFeeInvoiced + $baseCodFeeToInvoice);
        $order->setCodFeeInvoiced($codFeeInvoiced         + $codFeeToInvoice);*/

        return $this;
    }
}