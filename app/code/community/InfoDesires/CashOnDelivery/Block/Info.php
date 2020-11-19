<?php

class InfoDesires_CashOnDelivery_Block_Info extends Mage_Payment_Block_Info
{

    protected $_dataObject;
    protected $_priceModel;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cashondelivery/info.phtml');
    }

    public function toPdf()
    {
        $this->setTemplate('cashondelivery/pdf/info.phtml');
        return $this->toHtml();
    }

    public function getRawCodFee(){
        if ($_dataObject = $this->_getDataObject()){
            return $_dataObject->getCodFee();
        }
        return null;
    }

    public function getCodFeeExclTax(){
        if ($_dataObject = $this->_getDataObject()){
            $_extra_fee_excl = $_dataObject->getCodFee() ?
                   $this->_getPriceModel()->formatPrice($_dataObject->getCodFee()) : null;
            return $_extra_fee_excl;
        }
        return null;
    }

    public function getCodFeeInclTax(){
        if ($_dataObject = $this->_getDataObject()){
            $_extra_fee_incl = $_dataObject->getCodFee() ?
                   $this->_getPriceModel()->formatPrice($_dataObject->getCodFee()+$_dataObject->getCodTaxAmount()) : null;
            return $_extra_fee_incl;
        }
        return null;
    }

    protected function _getDataObject(){
        if (!isset($this->_dataObject)){
            if ($this->_dataObject = $this->getInfo()->getQuote()) {
            }elseif($this->_dataObject = $this->getInfo()->getOrder()){
            }
        }
        return $this->_dataObject;
    }

    protected function _getPriceModel(){
        if (!isset($this->_priceModel)){
            if($this->getInfo()->getQuote()){
                $this->_priceModel = $this->getInfo()->getQuote()->getStore();

            }elseif($this->getInfo()->getOrder()){
                $this->_priceModel = $this->getInfo()->getOrder();
            }
        }
        return $this->_priceModel;
    }

}
