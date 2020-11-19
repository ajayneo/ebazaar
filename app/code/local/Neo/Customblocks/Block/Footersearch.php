<?php   
class Neo_Customblocks_Block_Footersearch extends Mage_Core_Block_Template{
	
	public function getPopularSearchTerms()
	{
		$searchCollectino=Mage::getModel('catalogsearch/query')->getCollection()
		->setPopularQueryFilter()
		->setPageSize(10);
		
		return $searchCollectino;
	}
	
	public function getSearchResultProducts($searchText){
		$query = Mage::getModel('catalogsearch/query')->setQueryText($searchText)->prepare();
        $fulltextResource = Mage::getResourceModel('catalogsearch/fulltext')->prepareResult(
        Mage::getModel('catalogsearch/fulltext'), 
        $searchText, 
        $query
        );

      $collection = Mage::getResourceModel('catalog/product_collection');
      $collection->getSelect()->joinInner(
            array('search_result' => $collection->getTable('catalogsearch/result')),
            $collection->getConnection()->quoteInto(
                'search_result.product_id=e.entity_id AND search_result.query_id=?',
                $query->getId()
            ),
            array('relevance' => 'relevance')
        );
      
      $collection->setPageSize(1);
	  return $collection;
	}
	
	public function getProductBySku($sku){
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		return $product; 
	}
	
	public function getTopLevelCategory(){
		$categories = Mage::getModel('catalog/category')->getCollection()
    		->addAttributeToSelect('*')//or you can just add some attributes
    		->addAttributeToFilter('level', 2)//2 is actually the first level
    		->addAttributeToFilter('is_active', 1);
    	return $categories;
	}
	
	public function getCategoryBrands($id){
		$catbrands = Mage::getModel('catalog/category')->load($id)->getProductCollection()->addAttributeToSelect('brands');
		return $catbrands;
	}
}
?>