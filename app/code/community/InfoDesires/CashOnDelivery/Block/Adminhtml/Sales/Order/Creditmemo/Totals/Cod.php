<?php

class InfoDesires extends Mage_Core_Block_Abstract
{
    
    public function initTotals()
    {
        $parent = $this->getParentBlock();        
        if($this->_invoice->getCodFee()){
            $cod = new Varien_Object();
            $cod->setLabel($this->__('Refund Advance Amount')); // Cash on Delivery fee to Advance Amount
            $cod->setValue($parent->getSource()->getCodFee());
            $cod->setBaseValue($parent->getSource()->getBaseCodFee());
            $cod->setCode('cod_fee');            
            $parent->addTotalBefore($cod,'adjustment_positive');
        }

        return $this;
    }

}