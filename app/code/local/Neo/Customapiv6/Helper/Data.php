<?php
class Neo_Customapiv6_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getPriceWithTax($_price, $taxClassId)
	{   //echo "good";exit;
        //$productModel = Mage::getModel('catalog/product')->load($id);
        if($_price != '' && $taxClassId != '')
        {
	        $price = '';        
			$store = Mage::app()->getStore('default');
			$taxCalculation = Mage::getModel('tax/calculation');
			$request = $taxCalculation->getRateRequest(null, null, null, $store);		
			$percent = $taxCalculation->getRate($request->setProductClassId($taxClassId)); 
			$price = round($_price + ($_price*$percent/100));
			return $price;    
	    }
	}
}

