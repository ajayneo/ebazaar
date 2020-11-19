<?php
class Neo_Ebautomation_ProductController extends Mage_Core_Controller_Front_Action{
	function productImportAction(){
	
		if(Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
		  Mage::getModel('ebautomation/product')->productImport();
		}
  
	}

	

	public function notMappedProductsAction(){
		
		
			Mage::getModel('ebautomation/product')->notMappedProducts();
		
	}

	
}
?> 