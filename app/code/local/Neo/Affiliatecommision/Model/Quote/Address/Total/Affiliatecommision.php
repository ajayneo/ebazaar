<?php
class Neo_Affiliatecommision_Model_Quote_Address_Total_Affiliatecommision 
extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
     public function __construct()
    {
         $this -> setCode('affiliatecommision_total');
    }

    /**
     * Collect totals information about affiliatecommision
     * 
     * @param Mage_Sales_Model_Quote_Address $address 
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping 
     */

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
         parent :: collect($address);
         $items = $this->_getAddressItems($address);
         if (!count($items)) {
            return $this;
         }
         $quote = $address->getQuote();

		 //amount definition 

        $billing = Mage::app()->getRequest()->getPost('billing');//$billing['commision'] == 'b2b2c'

         

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if($billing['commision'] == 'b2b' && $customer->getEmail() == 'sandeep.mukherjee123@gmail.com')
        {
            foreach ($quote->getAllItems() as $item) {
                if ($item->getParentItem()) {
                     $item = $item->getParentItem();    
                 }

                 $product = Mage::getModel('catalog/product')->load($item->getProductId());
                 

                 $affiliateCommissionPer = $product->getAffiliateCommision();
                 $orignalprice = $product->getPrice();

                 $affiliateCommission = ($orignalprice * $affiliateCommissionPer) / 100;
  
                 $specialPrice = $orignalprice - $affiliateCommission;

                // Make sure we don't have a negative  
                if ($specialPrice > 0) {
                     $item->setCustomPrice($specialPrice);
                     $item->setOriginalCustomPrice($specialPrice);
                     $item->getProduct()->setIsSuperMode(true);
                }

                $ammountDiscount = $ammountDiscount + $affiliateCommission * $item->getQty(); 
            } 

            Mage::getSingleton('checkout/session')->setAffiliatecommisionTotal($ammountDiscount);
        }
        elseif($billing['commision'] == 'b2b2c' && $customer->getEmail() == 'sandeep.mukherjee123@gmail.com')
        {
            foreach ($quote->getAllItems() as $item) {
                if ($item->getParentItem()) {
                     $item = $item->getParentItem();    
                 } 

                 $product = Mage::getModel('catalog/product')->load($item->getProductId());
                 

                 $affiliateCommissionPer = $product->getAffiliateCommision();
                 $orignalprice = $product->getPrice();

                 $affiliateCommission = ($orignalprice * $affiliateCommissionPer) / 100;

                // Make sure we don't have a negative  
                if ($orignalprice > 0) {
                     $item->setCustomPrice($orignalprice);
                     $item->setOriginalCustomPrice($orignalprice);
                     $item->getProduct()->setIsSuperMode(true); 
                }

                $ammountDiscount = $ammountDiscount + $affiliateCommission * $item->getQty();
            }   

             Mage::getSingleton('checkout/session')->setAffiliatecommisionTotal($ammountDiscount);
        }
        else
        {
            /*foreach ($quote->getAllItems() as $item) {
                if ($item->getParentItem()) {
                     $item = $item->getParentItem();    
                 } 

                 $product = Mage::getModel('catalog/product')->load($item->getProductId());
                 
                 $orignalprice = $product->getPrice();

                // Make sure we don't have a negative  
                if ($orignalprice > 0) {
                     $item->setCustomPrice($orignalprice);
                     $item->setOriginalCustomPrice($orignalprice);
                     $item->getProduct()->setIsSuperMode(true); 
                }
            }*/ 
        }

        return $this;
     }
    
    /**
     * Add affiliatecommision totals information to address object
     * 
     * @param Mage_Sales_Model_Quote_Address $address 
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping 10101010
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
         return Mage :: helper('affiliatecommision') -> __('Affiliate Commision');
    }
}