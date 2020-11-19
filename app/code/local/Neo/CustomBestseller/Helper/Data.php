<?php
class Neo_CustomBestseller_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCustomImageLabel($sku)
	{
		$label = '';
		if(substr( $sku, 0, 2 ) === "OP"){
			$label = 'open-box';
		}elseif(substr( $sku, 0, 2 ) === "QC") {
			$label = 'pre-owned';
		}elseif(substr( $sku, 0, 3 )=== "REF") {
			$label = 'refurbished';
		}elseif(substr( $sku, 0, 6 )=== "SYGNBQ") {
			$label = 'pre-owned';
		}else{
			$label = '';
		}
        // $label = '';
		return $label;
	}


	public function getTopAccessories(){

		$collection = Mage::getModel('catalog/product')
                             ->getCollection()
                             ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                             ->addAttributeToSelect('*')
                             ->addAttributeToFilter('magebuzz_featured_product','1')
                             ->addAttributeToFilter('category_id', 7);
	    				
	    Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
	    Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

		$collection->joinField('is_in_stock',
		        'cataloginventory/stock_item',
		        'is_in_stock',
		        'product_id=entity_id',
		        'is_in_stock=1',
		        '{{table}}.stock_id=1',
		        'left');   	    

	    return $collection->setPageSize(10);
	}
}
	 