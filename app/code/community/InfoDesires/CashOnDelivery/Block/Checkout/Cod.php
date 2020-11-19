<?php

class InfoDesires_CashOnDelivery_Block_Checkout_Cod extends Mage_Checkout_Block_Total_Default
{
    protected $_template = 'cashondelivery/checkout/cod.phtml';

    /**
     * Check if we need display COD fee include and exlude tax
     *
     * @return bool
     */
    public function displayBoth()
    {
        return Mage::helper('cashondelivery')->displayCodBothPrices();
    }

    /**
     * Check if we need display COD fee include tax
     *
     * @return bool
     */
    public function displayIncludeTax()
    {
        return Mage::helper('cashondelivery')->displayCodFeeIncludingTax();
    }

    /**
     * Get COD fee include tax
     *
     * @return float
     */
    public function getCodFeeIncludeTax()
    {
        $codFeeInclTax = 0;
        foreach ($this->getTotal()->getAddress()->getQuote()->getAllShippingAddresses() as $address){
            $codFeeInclTax += $address->getCodFee() + $address->getCodTaxAmount();
        }
        return $codFeeInclTax;
    }

    /**
     * Get COD fee exclude tax
     *
     * @return float
     */
    public function getCodFeeExcludeTax()
    {
        $codFeeExclTax = 0;
        foreach ($this->getTotal()->getAddress()->getQuote()->getAllShippingAddresses() as $address){
            $codFeeExclTax += $address->getCodFee();
        }
        return $codFeeExclTax;
    }

    /**
     * Get label for COD fee include tax
     *
     * @return float
     */
    public function getIncludeTaxLabel()
    {
        return $this->helper('cashondelivery')->__('Advance Amount (Incl.Tax)'); // Cash on Delivery fee to Advance Amount
    }

    /**
     * Get label for COD fee exclude tax
     *
     * @return float
     */
    public function getExcludeTaxLabel()
    {
        return $this->helper('cashondelivery')->__('Advance Amount (Excl.Tax)'); // Cash on Delivery fee to Advance Amount
    }
}
