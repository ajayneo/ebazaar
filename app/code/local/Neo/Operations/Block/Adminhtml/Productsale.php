<?php
class Neo_Operations_Block_Adminhtml_Productsale extends Mage_Adminhtml_Block_Template {

	public function getRootCategories(){
		$categories = Mage::getModel('catalog/category')->getCollection()
                    ->addAttributeToSelect('*')//or you can just add some attributes
                    ->addAttributeToFilter('level', 2)//2 is actually the first level
                    ->addAttributeToFilter('is_active', 1)//if you want only active categories
                    ->addAttributeToFilter('include_in_menu', 1);//if you want only include in menu
                
        return $categories;
	}
}

