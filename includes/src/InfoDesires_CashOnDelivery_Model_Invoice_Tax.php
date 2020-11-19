<?php
class InfoDesires_CashOnDelivery_Model_Invoice_Tax extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $codTax = 0;
        $baseCodTax = 0;
        $order = $invoice->getOrder();

        $includeCodTax = true;
        /**
         * Check Cod amount in previus invoices
         */
        foreach ($invoice->getOrder()->getInvoiceCollection() as $previusInvoice) {
            if ($previusInvoice->getCodFee() && !$previusInvoice->isCanceled()) {
                $includeCodTax = false;
            }
        }

        if ($includeCodTax) {
            $codTax += $invoice->getOrder()->getCodTaxAmount();
            $baseCodTax += $invoice->getOrder()->getBaseCodTaxAmount();
            $invoice->setCodTaxAmount($invoice->getOrder()->getCodTaxAmount());
            $invoice->setBaseCodTaxAmount($invoice->getOrder()->getBaseCodTaxAmount());
            $invoice->getOrder()->setCodTaxAmountInvoiced($codTax);
            $invoice->getOrder()->setBaseCodTaxAmountInvoice($baseCodTax);
        }

        /**
         * Not isLast() invoice case handling
         * totalTax adjustment
         * check Mage_Sales_Model_Order_Invoice_Total_Tax::collect()
         */
        $allowedTax     = $order->getTaxAmount() - $order->getTaxInvoiced();
        $allowedBaseTax = $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced();
        $totalTax = $invoice->getTaxAmount();
        $baseTotalTax = $invoice->getBaseTaxAmount();
        if (!$invoice->isLast()
                && $allowedTax > $totalTax) {
            $newTotalTax           = min($allowedTax, $totalTax + $codTax);
            $newBaseTotalTax       = min($allowedBaseTax, $baseTotalTax + $baseCodTax);

            $invoice->setTaxAmount($newTotalTax);
            $invoice->setBaseTaxAmount($newBaseTotalTax);

            $invoice->setGrandTotal($invoice->getGrandTotal() - $totalTax + $newTotalTax);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalTax + $newBaseTotalTax);
        }

        return $this;
    }
}