<?php
class Neo_Microsoft_Model_Quote_Address_Total_Microsoftdiscount 
extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
     public function __construct()
    {
         $this ->setCode('microsoftdiscount_total');
    }
    /**
     * Collect totals information about microsoftdiscount
     * 
     * @param Mage_Sales_Model_Quote_Address $address 
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping 
     */
     public function collect(Mage_Sales_Model_Quote_Address $address)
    {
         parent :: collect($address);
     
         $payment = Mage::app()->getRequest()->getPost('payment');

        if($payment['method'] == 'innoviti' && Mage::getSingleton('checkout/session')->getMicrosoftDiscountRedirect() == 1)
        { 
             
             $items = $this->_getAddressItems($address);  
             if (!count($items)) {
                return $this;
             }
             $quote= $address->getQuote();
 
             //amount definition

             $microsoftdiscountAmount = -$payment['microsoft-discount'];

             //amount definition

             $microsoftdiscountAmount = $quote -> getStore() -> roundPrice($microsoftdiscountAmount);
             $this -> _setAmount($microsoftdiscountAmount) -> _setBaseAmount($microsoftdiscountAmount);
             $address->setData('microsoftdiscount_total',$microsoftdiscountAmount);
        }

         return $this;
     }
    
    /**
     * Add microsoftdiscount totals information to address object
     * 
     * @param Mage_Sales_Model_Quote_Address $address 
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping 
     */
     public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
         parent :: fetch($address);
         $amount = $address -> getTotalAmount($this -> getCode());
         if ($amount != 0){
             $address -> addTotal(array(
                     'code' => $this -> getCode(),
                     'title' => $this -> getLabel(),
                     'value' => $amount
                    ));
         }
        
         return $this;
     }
    
    /** 
     * Get label
     * 
     * @return string 
     */
     public function getLabel()
    {
         return Mage :: helper('microsoft') -> __('DISCOUNT ( SURFACE PRO 4 )');
    }
}