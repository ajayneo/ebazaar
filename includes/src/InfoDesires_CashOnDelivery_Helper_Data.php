<?php

class InfoDesires_CashOnDelivery_Helper_Data extends Mage_Core_Helper_Data
{

    protected $_codPriceIncludesTax;

    public function codPriceIncludesTax($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if (!isset($this->_codPriceIncludesTax[$storeId])) {
            $this->_codPriceIncludesTax[$storeId] =
                (int)Mage::getStoreConfig(InfoDesires_CashOnDelivery_Model_Quote_TaxTotal::CONFIG_XML_PATH_COD_INCLUDES_TAX, $store);
        }
        return $this->_codPriceIncludesTax[$storeId];
    }

    public function getCodTaxClass($store)
    {
        return (int)Mage::getStoreConfig(InfoDesires_CashOnDelivery_Model_Quote_TaxTotal::CONFIG_XML_PATH_COD_TAX_CLASS, $store);
    }

    public function getCodPrice($price, $includingTax = null, $shippingAddress = null, $ctc = null, $store = null)
    {
        $billingAddress = false;
        if ($shippingAddress && $shippingAddress->getQuote() && $shippingAddress->getQuote()->getBillingAddress()) {
            $billingAddress = $shippingAddress->getQuote()->getBillingAddress();
        }
        
        $calc = Mage::getSingleton('tax/calculation');
        $taxRequest = $calc->getRateRequest(
            $shippingAddress,
            $billingAddress,
            $shippingAddress->getQuote()->getCustomerTaxClassId(),
            $store
        );
        $taxRequest->setProductClassId($this->getCodTaxClass($store));
        $rate = $calc->getRate($taxRequest);
        $tax = $calc->calcTaxAmount($price, $rate, $this->codPriceIncludesTax($store), true);
        
        if ($this->codPriceIncludesTax($store)) {
            return $includingTax ? $price : $price - $tax;
        }
        else {
            return $includingTax ? $price + $tax : $price;
        }
    }

    public function getCodFeeDisplayType($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if (!isset($this->_shippingPriceDisplayType[$storeId])) {
            $this->_shippingPriceDisplayType[$storeId] =
                (int)Mage::getStoreConfig(InfoDesires_CashOnDelivery_Model_Quote_TaxTotal::CONFIG_XML_PATH_DISPLAY_COD, $store);
        }
        return $this->_shippingPriceDisplayType[$storeId];
    }

    public function displayCodFeeIncludingTax()
    {
        return $this->getCodFeeDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function displayCodFeeExcludingTax()
    {
        return $this->getCodFeeDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function displayCodBothPrices()
    {
        return $this->getCodFeeDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

}
