<?php

class InfoDesires_CashOnDelivery_Block_Adminhtml_Sales_Order_Create_Totals_Cod extends Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    protected $_template = 'cashondelivery/sales/order/create/totals/cod.phtml';

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
        return $this->getTotal()->getAddress()->getCodFee() +
            $this->getTotal()->getAddress()->getCodTaxAmount();
    }

    /**
     * Get COD fee exclude tax
     *
     * @return float
     */
    public function getCodFeeExcludeTax()
    {
        return $this->getTotal()->getAddress()->getCodFee();
    }

    /**
     * Get label for COD fee include tax
     *
     * @return float
     */
    public function getIncludeTaxLabel()
    {
        return $this->helper('cashondelivery')->__('Advance Amount Incl. Tax'); // Cash on Delivery fee to Advance Amount
    }

    /**
     * Get label for COD fee exclude tax
     *
     * @return float
     */
    public function getExcludeTaxLabel()
    {
        return $this->helper('cashondelivery')->__('Advance Amount Excl. Tax'); // Cash on Delivery fee to Advance Amount
    }
}
