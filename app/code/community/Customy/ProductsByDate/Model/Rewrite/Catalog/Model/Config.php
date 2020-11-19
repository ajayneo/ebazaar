<?php
class Customy_ProductsByDate_Model_Rewrite_Catalog_Model_Config extends Mage_Catalog_Model_Config{      
	public function getAttributeUsedForSortByArray()    {
        if (!Mage::getStoreConfig("productsbydate/options/active")){
                    return parent::getAttributeUsedForSortByArray();        
                }                
        $options = array('entity_id'  => Mage::helper('productsbydate')->__('New Arrival'),'position'=> Mage::helper('productsbydate')->__('Position'));       
         foreach ($this->getAttributesUsedForSortBy() as $attribute) { 
             $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();        
        }        
        return $options;    
	}
}