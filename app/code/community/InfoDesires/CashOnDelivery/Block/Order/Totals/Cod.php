<?php
class InfoDesires_CashOnDelivery_Block_Order_Totals_Cod extends Mage_Core_Block_Abstract
{

   public function initTotals()
    {       
        $parent = $this->getParentBlock();
        $this->_order   = $parent->getOrder();
        if($this->_order->getCodFee()){
            $cod = new Varien_Object();
            $cod->setLabel($this->__('Cash on Delivery'));
            $cod->setValue($this->_order->getCodFee());
            $cod->setBaseValue($this->_order->getBaseCodFee());
            $cod->setCode('cod_fee');

            if (Mage::helper('cashondelivery')->displayCodBothPrices()){
                $cod->setLabel($this->__('Advance Amount (Excl.Tax)')); // Cash on Delivery fee to Advance Amount

                $codIncl = new Varien_Object();
                $codIncl->setLabel($this->__('Advance Amount (Incl.Tax)')); // Cash on Delivery fee to Advance Amount
                $codIncl->setValue($this->_order->getCodFee()+$this->_order->getCodTaxAmount());
                $codIncl->setBaseValue($this->_order->getBaseCodFee()+$this->_order->getBaseCodTaxAmount());
                $codIncl->setCode('cod_fee_incl');
                
                $parent->addTotalBefore($cod,'tax');
                $parent->addTotalBefore($codIncl,'tax');
            }elseif(Mage::helper('cashondelivery')->displayCodFeeIncludingTax()){                
                $cod->setValue($this->_order->getCodFee()+$this->_order->getCodTaxAmount());
                $cod->setBaseValue($this->_order->getBaseCodFee()+$this->_order->getBaseCodTaxAmount());                
                $parent->addTotalBefore($cod,'tax');
            }else{
                $parent->addTotalBefore($cod,'tax');
            }
        }

        return $this;        
    }

}