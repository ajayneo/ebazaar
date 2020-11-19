<?php

class InfoDesires_CashOnDelivery_Model_Sales_Pdf_Cod extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getOrder()->getStore();        
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountInclTax = $this->getAmount()+$this->getSource()->getCodTaxAmount();
        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        if (Mage::helper('cashondelivery')->displayCodBothPrices()){
            $totals = array(
                array(
                    'amount'    => $this->getAmountPrefix().$amount,
                    'label'     => Mage::helper('cashondelivery')->__('Advance Amount (Excl.Tax)') . ':', // Cash on Delivery fee to Advance Amount
                    'font_size' => $fontSize
                ),
                array(
                    'amount'    => $this->getAmountPrefix().$amountInclTax,
                    'label'     => Mage::helper('cashondelivery')->__('Advance Amount (Incl.Tax)') . ':', // Cash on Delivery fee to Advance Amount
                    'font_size' => $fontSize
                ),
            );
        } elseif (Mage::helper('cashondelivery')->displayCodFeeIncludingTax()) {
            $totals = array(array(
                'amount'    => $this->getAmountPrefix().$amountInclTax,
                'label'     => Mage::helper('cashondelivery')->__('Pending Amount') . ':', //$this->getTitle()
                'font_size' => $fontSize
            ));
        } else {
            $totals = array(array(
                'amount'    => $this->getAmountPrefix().$amount,
                'label'     => Mage::helper('cashondelivery')->__('Pending Amount') . ':', //$this->getTitle()
                'font_size' => $fontSize
            ));
        }

        return $totals;
    }
}
