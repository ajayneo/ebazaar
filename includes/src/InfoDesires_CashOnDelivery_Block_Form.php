<?php

class InfoDesires_CashOnDelivery_Block_Form extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cashondelivery/form.phtml');
    }

    public function getQuote(){
        return $this->getMethod()->getInfoInstance()->getQuote();
    }

    public function getShippingAddress(){
        return $this->getQuote()->getShippingAddress();
    }

    public function convertPrice($price, $format=false, $includeContainer = true)
    {
        return $this->getQuote()->getStore()->convertPrice($price, $format, $includeContainer);
    }

}
