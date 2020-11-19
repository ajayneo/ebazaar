<?php   
class Neo_Customblocks_Block_Index extends Mage_Core_Block_Template{


	protected function _construct()
	{		
        $this->addData(array('cache_lifetime' => 86400));
        $this->addCacheTag(Mage_Catalog_Model_Product::CACHE_TAG);
	} 

	 /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'CATALOG_PRODUCT_HOME_BEST_SELLER',
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
           100
        );
    }
	
	public function getIsHomeCategories()
	{
		$categories = Mage::getModel('catalog/category')->getCollection()
    				->addAttributeToSelect('*')
    				->addAttributeToFilter('level', 2)
    				->addAttributeToFilter('is_active', 1)
				->addAttributeToFilter('is_home',1);
		 
		return $categories;
	}
	
	public function getAllCategories()
	{
		$categories = Mage::getModel('catalog/category')->getCollection()
    				->addAttributeToSelect('*')
    				->addAttributeToFilter('level', 2)
    				->addAttributeToFilter('is_active', 1);
		
		return $categories;
	}
	
	public function getCategoryDetails($id)
	{
		$categorydetails = Mage::getModel('catalog/category')->load($id)
					->getImage();
		$final_url = Mage::getBaseUrl('media').'catalog/category/'.$categorydetails;
		return $final_url;
	}
	
	public function getCategoryProducts($id)
	{
		$productCollection = Mage::getModel('catalog/category')->load($id)
 					->getProductCollection()
 					->addAttributeToSelect('*') // add all attributes - optional
 					->addAttributeToFilter('status', 1) // enabled
 					->addAttributeToFilter('visibility', 4) //visibility in catalog,search
					->setPageSize(4) // added
 					->setOrder('price', 'ASC');//sets the order by price
 		return $productCollection;
	}

	public function getCategoryProductsForIpad($id){
		$productCollection = Mage::getModel('catalog/category')->load($id)
 					->getProductCollection()
 					->addAttributeToSelect('*') // add all attributes - optional
 					->addAttributeToFilter('status', 1) // enabled
 					->addAttributeToFilter('visibility', 4) //visibility in catalog,search
 					->setOrder('price', 'ASC') //sets the order by price
 					->setPageSize(3);
 		return $productCollection;	
	}
	
	public function getProductsDetails($sku)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		return $product;
	}
	
	public function getNewArrivals()
	{
		$productsCollection = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToFilter('status', 1) // added enabled
					->addAttributeToFilter('visibility', 4)
					->addAttributeToSort("entity_id","DESC")
					->setPageSize(15);

		return $productsCollection;
	}

	// Featured Products
	public function getCustomeFeaturedProducts()
	{
		// optimised query added by pradeep on 17th April 2015
		$productCollection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect('*')
						->addAttributeToFilter('price',array('gt' => '7000'));

		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($productCollection);

		$this->_productCollection = Mage::getSingleton('cataloginventory/stock')
                                ->addInStockFilterToCollection($productCollection);

        $productCollection->getSelect()->order('RAND()');
		$productCollection->getSelect()->limit(15);
		return $productCollection;

		// can be deleted if above code is dnt have any issues
		// before optimized query
		/*$customfeapro = Mage::getModel('catalog/product')->getCollection()
						->addAttributeToFilter('price',array('gt' => '7000'))
						->addAttributeToFilter('status',1);

		// code to have filter to show only instock products
		$customfeapro->joinField(
			'is_in_stock',
            'cataloginventory/stock_item',
			'is_in_stock',
			'product_id=entity_id',
			'{{table}}.stock_id=1',
			'left'
		)->addAttributeToFilter('is_in_stock', array('eq' => 1));

		$customfeapro->getSelect()->order('Rand()');
		$customfeapro->getSelect()->limit(15);

		return $customfeapro;*/
	}
	
	public function getProductDetails($id)
	{
		$product = Mage::getModel('catalog/product')->load($id);
		return $product;
	}
	
	public function getBestsellerProducts()
	{
		$storeId = Mage::app()->getStore()->getId();

		$products = Mage::getResourceModel('reports/product_collection')
				->addOrderedQty()
				->addAttributeToSelect('*')
				->addAttributeToFilter('status', 1) // added enabled
				->addAttributeToFilter('visibility', 4) // added
				->addAttributeToFilter('price',array('gt' => '1000')) //added for high value product
				->setStoreId($storeId)
				->addStoreFilter($storeId)
				->setOrder('ordered_qty', 'desc')
				->setPageSize(15);

		// code to have filter to show only instock products
		$products->joinField(
			'is_in_stock',
            'cataloginventory/stock_item',
			'is_in_stock',
			'product_id=entity_id',
			'{{table}}.stock_id=1',
			'left'
		)->addAttributeToFilter('is_in_stock', array('eq' => 1));
		
		return $products;
		
	}
	
	public function getRandomProducts()
	{
		$_productCollection = Mage::getModel('catalog/product')->getCollection()
								->addAttributeToFilter('status', 1) // added enabled
								->addAttributeToFilter('visibility',4);
                $_productCollection->getSelect()->order(new Zend_Db_Expr('RAND()'));
                $_productCollection->setPage(1, 5);
		
		return $_productCollection;
	}
	
	public function getDiscountPercentage($id){
		$product = Mage::getModel('catalog/product')->load($id);
		$price = $product->getPrice();
		$specialprice = $product->getSpecialPrice();
		/*if(!empty($specialprice) && $specialprice < $price){
			$discountpercent = (($price-$specialprice)/$price)*100;
			return round($discountpercent);
		}*/
		if(!empty($specialprice) && $specialprice < $price){
			$saveprice = $price - $specialprice;
			return $saveprice;
		}
		
		
	}

	public function getDiscountPercent($id){
		$product = Mage::getModel('catalog/product')->load($id);
		$price = $product->getPrice();
		$specialprice = $product->getSpecialPrice();
		$nowprice = $product->getFinalPrice();
		if(!empty($specialprice) && $specialprice < $price){
			$beforeprice = $price;
		}else{
			$beforeprice = $price;
		}
		
		//if(!empty($specialprice) && $specialprice < $price){
			$discountpercent = (($beforeprice-$nowprice)/$beforeprice)*100;
			return round($discountpercent);
		//}
	}
	
	public function getDealsDiscountPercent($id){
		$product = Mage::getModel('catalog/product')->load($id);
		$price = $product->getPrice();
		$specialprice = $product->getSpecialPrice();
		$nowprice = $product->getFinalPrice();
		
		if(!empty($specialprice) && $specialprice < $price){
			$price_to = $specialprice; 
		}elseif(!empty($specialprice) && $specialprice > $price){
			$price_to = $price;
		}elseif(empty($specialprice)){
			$price_to = $price;
		}
		
		$discountpercent = (($price_to-$nowprice)/$price_to)*100;
		return round($discountpercent);
	}
		
	public function getTopLevelCategoryIfNoRelatedProducts($ids){
		$topCategory = Mage::getResourceModel('catalog/category_collection')
				->addIdFilter($ids)
    				->setOrder('level', 'ASC')
    				->setPage(1,1)
    				->getFirstItem();
		return $topCategory;
	}
	
	/*public function getProductsFromCategoryForRelatedProducts($id){
		$products = Mage::getModel('catalog/category')->load($id)
				->getProductCollection()
				->addAttributeToSelect('*')
 				->addAttributeToFilter('status', 1)
 				->addAttributeToFilter('visibility', 4)
				->setOrder('price', 'ASC')
 				->setPageSize(5);
		$product_ids = array();
		foreach($products as $product):
			$product_ids[] = $product->getId();
		endforeach;
		return $product_ids;
	}*/
	
	public function getProductsFromCategoryForRelatedProducts($id, $brands, $price, $attribute_set_id){
		if($price <= 1000) {
			$add = 2000;
		}
		elseif($price > 1000 && $price <= 99999) {
			$add = 5000;
		}
		else {
			$add = 10000;
		}
		$new_price = $price + $add;
		$product_ids = array();
		$product_ids_same_brand = array();
		$product_ids_diff_brand = array();
		/*Get Product of same brands with price nearby */
		$products_same_brands = Mage::getModel('catalog/category')->load($id)
				->getProductCollection()
				->addAttributeToSelect('*')
 				->addAttributeToFilter('status', 1)
 				->addAttributeToFilter('visibility', 4)
				->addAttributeToFilter('brands', $brands)
				->addAttributeToFilter('attribute_set_id', $attribute_set_id)
				->addAttributeToFilter('price',array('from' => $price+1,'to' => $new_price))
 				->setOrder('price', 'ASC')
 				->setPageSize(3);
		
		foreach($products_same_brands as $products_same_brand):
			$product_ids_same_brand[] = $products_same_brand->getId();
		endforeach;
		
		/*Get Product of different brands with price nearby */
		$products_diff_brands = Mage::getModel('catalog/category')->load($id)
				->getProductCollection()
				->addAttributeToSelect('*')
 				->addAttributeToFilter('status', 1)
 				->addAttributeToFilter('visibility', 4)
				->addAttributeToFilter('brands', array('neq'=>$brands))
				->addAttributeToFilter('attribute_set_id', $attribute_set_id)
				->addAttributeToFilter('price',array('from' => $price,'to' => $new_price))
 				->setOrder('price', 'ASC')
 				->setPageSize(2);
		
		foreach($products_diff_brands as $products_diff_brand):
			$product_ids_diff_brand[] = $products_diff_brand->getId();
		endforeach;
		
		$product_ids = array_merge($product_ids_same_brand, $product_ids_diff_brand);
		return $product_ids;
	}
	
	
	public function getReviewsForProduct($id){
		$reviews = Mage::getModel('review/review')
				->getResourceCollection()
				->addStoreFilter(Mage::app()->getStore()->getId()) 
				->addEntityFilter('product',$id)
				->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
				->setDateOrder()
				->addRateVotes();
		return $reviews;
	}
	
	/*
	///// Deal module is disabled
	public function getDealProducts() {
		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>1));
		$dealproductIds = array();
		if (count($dealCollection)) {
			foreach ($dealCollection as $deal) {
				$dealproductIds[] = $deal->getProductId();
			}
		}
		return $dealproductIds;
	}*/

	/*
	* getProductsfromCompare() is used to get products from the compare session
	* 7th April 2015
	* Pradeep Sanku
	*/
	public function getProductsfromCompare(){
		$compare_ids = array();
		$compare_collection = Mage::helper('catalog/product_compare')->getItemCollection();
		foreach ($compare_collection as $comp_prod) {
		    $compare_ids[] = $comp_prod->getId();
		}
		return $compare_ids;
	}

	/*
	* getIsProductInWishlist() is used to tell wther current product is in the wishlist session of the logged in customer
	* 7th April 2015
	* Pradeep Sanku
	*/
	public function getIsProductInWishlist($id){
		$hasProduct = 0;
		if(Mage::getSingleton('customer/session')->isLoggedIn()){
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
			$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
			$collection = Mage::getModel('wishlist/item')->getCollection()->addFieldToFilter('wishlist_id', $wishlist->getId())->addFieldToFilter('product_id', $id);
			$item = $collection->getFirstItem();
			$hasProduct = !!$item->getId();
		}
		return $hasProduct;
	}

	/*public function getProductFromBestSellingCategory(){
		$category = Mage::getModel('catalog/category')->load(8);
		$bestsellingproducts = $category->getProductCollection()->addAttributeToSort('position')->addAttributeToFilter('status', 1);
		Mage::getModel('catalog/layer')->prepareProductCollection($bestsellingproducts);
		return $bestsellingproducts;
	}*/

	public function getProductCompareUrlonHome($product){
		$_compareUrl = Mage::helper('catalog/product_compare')->getAddUrl($product);
		return $_compareUrl;
	}
}
