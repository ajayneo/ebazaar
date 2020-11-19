<?php

class InfoDesires_CashOnDelivery_Block_Invoice_Totals_Cod extends Mage_Core_Block_Abstract
{
    
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_invoice   = $parent->getInvoice();
        if($this->_invoice->getCodFee()){
            $cod = new Varien_Object();
            $cod->setLabel($this->__('Advance Amount')); // Cash on Delivery fee to Advance Amount
            $cod->setValue($this->_invoice->getCodFee());
            $cod->setBaseValue($this->_invoice->getBaseCodFee());
            $cod->setCode('cod_fee');

            if (Mage::helper('cashondelivery')->displayCodBothPrices()){
                $cod->setLabel($this->__('Advance Amount (Excl.Tax)')); // Cash on Delivery fee to Advance Amount

                $codIncl = new Varien_Object();
                $codIncl->setLabel($this->__('Advance Amount (Incl.Tax)')); // Cash on Delivery fee to Advance Amount
                $codIncl->setValue($this->_invoice->getCodFee()+$this->_invoice->getCodTaxAmount());
                $codIncl->setBaseValue($this->_invoice->getBaseCodFee()+$this->_invoice->getBaseCodTaxAmount());
                $codIncl->setCode('cod_fee_incl');

                $parent->addTotalBefore($cod,'tax');
                $parent->addTotalBefore($codIncl,'tax');
            }elseif(Mage::helper('cashondelivery')->displayCodFeeIncludingTax()){
                $cod->setValue($this->_invoice->getCodFee()+$this->_invoice->getCodTaxAmount());
                $cod->setBaseValue($this->_invoice->getBaseCodFee()+$this->_invoice->getBaseCodTaxAmount());
                $parent->addTotalBefore($cod,'tax');
            }else{
                $parent->addTotalBefore($cod,'tax');
            }
        }

        return $this;
    }

}