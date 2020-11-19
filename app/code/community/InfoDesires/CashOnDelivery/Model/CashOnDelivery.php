<?php

class InfoDesires_CashOnDelivery_Model_CashOnDelivery extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'cashondelivery';
    protected $_canUseForMultishipping  = false;
    protected $_canCapture  = true;
    protected $_canCapturePartial = true; 

    protected $_formBlockType = 'cashondelivery/form';
    protected $_infoBlockType = 'cashondelivery/info';
	
	public function getOrderPlaceRedirectUrl() {
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        $order_grand_total = $quote->getGrandTotal();
        //$cod_maxlimit = Mage::getStoreConfig('payment/cashondelivery/cod_maxlimit');
		$order_cod_fee = $quote->getCodFee();
		if($order_cod_fee){
			return Mage::getUrl('billdesk/payment/redirect', array('_secure' => true));
		}
        /*if($order_grand_total > $cod_maxlimit){
            return Mage::getUrl('billdesk/payment/redirect', array('_secure' => true));
        }*/
    }

    public function getCODTitle()
    {
        return $this->getConfigData('title');
    }

    public function getInlandCosts()
    {
        return floatval($this->getConfigData('inlandcosts'));
    }

    public function getForeignCountryCosts()
    {
        return floatval($this->getConfigData('foreigncountrycosts'));
    }

    public function getCustomText()
    {
        return $this->getConfigData('customtext');
    }

    /**
     * Returns COD fee for certain address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return decimal
     *
     */
    public function getAddressCosts(Mage_Customer_Model_Address_Abstract $address)
    {
        if ($address->getCountry() == Mage::getStoreConfig('shipping/origin/country_id')) {
            return $this->getInlandCosts();
        } else {
            return $this->getForeignCountryCosts();
        }
    }

    public function getAddressCodFee(Mage_Customer_Model_Address_Abstract $address, $value = null, $alreadyExclTax = false)
    {
        if (is_null($value)) {
            $value = $this->getAddressCosts($address);
        }
        if (Mage::helper('cashondelivery')->codPriceIncludesTax()) {
            if (!$alreadyExclTax) {
                $value = Mage::helper('cashondelivery')->getCodPrice($value, false, $address, $address->getQuote()->getCustomerTaxClassId());
            }
        }
        return $value;
    }

    public function getAddressCodTaxAmount(Mage_Customer_Model_Address_Abstract $address, $value = null, $alreadyExclTax = false)
    {
        if (is_null($value)) {
            $value = $this->getAddressCosts($address);
        }
        if (Mage::helper('cashondelivery')->codPriceIncludesTax()) {
            $includingTax = Mage::helper('cashondelivery')->getCodPrice($value, true, $address, $address->getQuote()->getCustomerTaxClassId());
            if (!$alreadyExclTax) {
                $value = Mage::helper('cashondelivery')->getCodPrice($value, false, $address, $address->getQuote()->getCustomerTaxClassId());
            }
            return $includingTax - $value;
        }
        return 0;
    }

    /**
     * Return true if the method can be used at this time
     *
     * @return bool
     */
    public function isAvailable($quote=null)
    {
        if (!parent::isAvailable($quote)) {
            return false;
        }
        if (!is_null($quote)) {
            if($this->getConfigData('shippingallowspecific', $quote->getStoreId()) == 1) {
                $country = $quote->getShippingAddress()->getCountry();
                $availableCountries = explode(',', $this->getConfigData('shippingspecificcountry', $quote->getStoreId()));
                if(!in_array($country, $availableCountries)){
                    return false;
                }

            }
            if ($this->getConfigData('disallowspecificshippingmethods', $quote->getStoreId()) == 1) {
                $shippingMethodCode = explode('_',$quote->getShippingAddress()->getShippingMethod());
                $shippingMethodCode = $shippingMethodCode[0];
                if (in_array($shippingMethodCode, explode(',', $this->getConfigData('disallowedshippingmethods', $quote->getStoreId())))) {
                    return false;
                }
            }
        }
        return true;
    }
}
