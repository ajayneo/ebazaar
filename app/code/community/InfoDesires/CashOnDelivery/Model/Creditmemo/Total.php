<?php

class InfoDesires_CashOnDelivery_Model_Creditmemo_Total extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $cm)
    {

        $order = $cm->getOrder();

        if ($order->getPayment()->getMethodInstance()->getCode() != 'cashondelivery'){
            return $this;
        }

        $baseCmTotal = $cm->getBaseGrandTotal();
        $cmTotal = $cm->getGrandTotal();

        $baseCodFeeCredited = $order->getBaseCodFeeCredited();
        $codFeeCredited = $order->getCodFeeCredited();

        $baseCodFeeInvoiced = $order->getBaseCodFeeInvoiced();
        $codFeeInvoiced = $order->getCodFeeInvoiced();

        if ($cm->getInvoice()){
            $invoice = $cm->getInvoice();
            $baseCodFeeToCredit = $invoice->getBaseCodFee();
            $codFeeToCredit = $invoice->getCodFee();
        }else{
            $baseCodFeeToCredit = $baseCodFeeInvoiced;
            $codFeeToCredit = $codFeeInvoiced;
        }

        if (!$baseCodFeeToCredit > 0){
            return $this;
        }


        // Subtracting invoiced COD fee from Credit memo total
        //$cm->setBaseGrandTotal($baseCmTotal-$baseCodFeeToCredit);
        //$cm->setGrandTotal($cmTotal-$codFeeToCredit);

        //$cm->setBaseCodFee($baseCodFeeToCredit);
        //$cm->setCodFee($codFeeToCredit);

        //$order->setBaseCodFeeCredited($baseCodFeeCredited+$baseCodFeeToCredit);
        //$order->setCodFeeCredited($codFeeCredited+$baseCodFeeToCredit);


        return $this;
    }
}