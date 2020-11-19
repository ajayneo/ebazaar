<?php
class Neo_Customapiv3_Model_Catalog extends Mage_Core_Model_Abstract
{

	public function getSearchProductList($keyword, $user_id, $page_no = 1, $page_size = 10)
	{
		$keyword_array = explode(" ", $keyword);

		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection->addAttributeToSelect(array('price','name','image','description','special_price'));
		// checking if product are in stock
		//$collection->joinField('qty','cataloginventory/stock_item','qty','product_id=entity_id','{{table}}.stock_id=1','left');
    //$collection->addAttributeToFilter('qty', array("gt" => 0));
    $collection->addAttributeToFilter('status', array('eq' => 1));
		// keyword array iterated and added for filteration
		foreach ($keyword_array as $key => $value) {
			$collection->addAttributeToFilter('name', array('like' => '%'.$value.'%'));
		}
		$collection->setCurPage($page_no);
		$collection->setPageSize($page_size);

		return $this->prepareProductsCollectionArray($collection, $user_id);
	}

	public function prepareProductsCollectionArraySearch($collection,$user_id=null)
	{
		$final_result = array();
		$productcollection_array = array();
		$model = Mage::getModel('catalog/product'); //getting product model
		foreach($collection as $product) {
			$_product = $model->load($product->getId());
			$discount = $this->getDealsDiscountPercent($_product->getId());
			$specialprice = $_product->getSpecialPrice();
			$deal_mrpprice = $_product->getDealMrpprice();
			$price = (double)$_product->getPrice();
			$product_id = $_product->getId();
			$productcollection_array['product_id'] = $_product->getId();
			$productcollection_array['name'] = $_product->getName();
			$productcollection_array['image'] = (string)Mage::helper('catalog/image')->init($_product, 'thumbnail');
			$productcollection_array['price'] = $price;
			//$productcollection_array['description'] = array_values(array_filter(explode('</li>',str_replace(array('<ul>','</ul>','<li>'),'',$product->getDescription()))));
			$productcollection_array['description'] = $_product->getDescription();
			if($user_id != null) {
				$productcollection_array['is_favourite'] = $this->checkIsInWishlist($user_id,$product_id);
			} else {
				$productcollection_array['is_favourite'] = false;
			}
			if($specialprice){
				$productcollection_array['special_price'] = $_product->getSpecialPrice();
			}
			if($deal_mrpprice){
				$productcollection_array['deal_mrpprice'] = $_product->getDealMrpprice();
			}
			if($discount > 0){
				$productcollection_array['discount'] = $discount;
			}
			$final_result[] = $productcollection_array;
		}

		return $final_result;
	}
	public function getProductDetails($product,$user_id=null)
	{

		$product_id = $product->getId();
		$product_attributeset_id = $product->getAttributeSetId();
		$images = $product->getMediaGalleryImages();
		$array = $urls = array();
		$array['status'] = 1;
		$base_image_url = (string)Mage::helper('catalog/image')->init($product,'image');
		$urls[] = $base_image_url;
		foreach($images as $image) {
			$image_name = basename($image->getUrl());
			if($image_name != basename($base_image_url)) {
				$urls[] = $image->getUrl();
			}
		}
		$reviews = Mage::getModel('review/review')
					->getResourceCollection()
					->addEntityFilter('product',$product->getId())
					->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
					->setDateOrder()
					->addRateVotes();
		$review = array();
		foreach($reviews as $review_item) {
			$ary['created_at'] = $review_item->getData('created_at');
			$ary['title'] = $review_item->getData('title');
			$ary['detail'] = $review_item->getData('detail');
			$ary['nickname'] = $review_item->getData('nickname');
			$ratings = array();
			foreach($review_item->getRatingVotes() as $vote) {
				$tmp = $vote->getData('value');
				$ratings[] = $tmp;
			}
			$ary['rating_vote'] = $ratings;
			$review[] = $ary;
		}
		$review_summery = Mage::getModel('review/review_summary')->setStoreId(1)->load($product_id);
		$avg_rating = (double)$review_summery['rating_summary'];
		$array['data']['average_rating'] = (int)(($avg_rating*5)/100);
		$array['data']['product_id'] = $product->getId();
		$array['data']['name'] = $product->getData('name');
		$array['data']['sku'] = $product->getData('sku');
		$array['data']['price'] = number_format($product->getData('price'),2,".","");
		$array['data']['product_images'] = $urls;
		$array['data']['emi'] = (int)$product->getEmiOption();
		$array['data']['cod'] = (int)$product->getCashOnDelivery();
		$array['data']['home_delivery'] = (int)$product->getFreeHomeDelivery();
		$array['data']['free_bag'] = (int)$product->getLaptopFreebag();
		$array['data']['verify_ondelivery'] = 1;
		$array['data']['special_price'] = $product->getSpecialPrice();
		$array['data']['is_favourite'] = $this->checkIsInWishlist($user_id,$product_id);
		// add instock flag
		$product_qty_eb = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
		$stock_eb = $product->getStockItem();
		if ($stock_eb->getIsInStock()) {
      if($product_qty_eb == 0) {
	        $array['data']['is_instock'] = false;
	    } else {
	        $array['data']['is_instock'] = true;
	    }
		} else {
		    $array['data']['is_instock'] = false;
		}
		$array['data']['warrenty'] = $product->getWarrenty();

		$array['reviews'] = $review;
		$array['specifications'] = $this->getProductSpecification($product_attributeset_id,$product); //$specifications;
		$array['accessories_recommended'] = $this->getAccessoriesRecommended($product,$user_id);
		$array['related_products'] = $this->getRelatedProducts($product,$user_id);
		$array['product_url'] = $product->getProductUrl();
		return $array;
	}

	public function getPincodeChecker($pincode){
		$response = $pincode_array = array();
		$configValue = Mage::getStoreConfig('pincode_options/pincode_check/pincode');
		$pincode_array = explode(',',$configValue);

		if(in_array($pincode,$pincode_array)){
			$response['status'] = 1;
			$response['message'] = 'Delivery available in your location.';
		}else{
			$response['status'] = 0;
			$response['message'] = 'Delivery not available in your location yet.';
		}
		return $response;
	}

	public function getRelatedProducts($product, $user_id){
		$relatedproducts_array = array();
		$customblocks_index = new Neo_Customblocks_Block_Index();
		$toplevel_categoryid = $customblocks_index->getTopLevelCategoryIfNoRelatedProducts($product->getCategoryIds());
		$products_fromcategory_related = $customblocks_index->getProductsFromCategoryForRelatedProducts($toplevel_categoryid->getId(),$product->getBrands(),$product->getPrice(),$product->getAttributeSetId());
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('entity_id', array('in' => $products_fromcategory_related));
		$relatedproducts_array = $this->prepareProductsCollectionArray($collection, $user_id);
		return $relatedproducts_array;
	}

	public function getAccessoriesRecommended($product, $user_id){
		$accessoriesrecommended_array = array();
		$related_prods = Mage::getModel('catalog/product')->getCollection()
                    	->addAttributeToSelect('*')
                    	->addAttributeToFilter('entity_id',array('in'=>$product->getRelatedProductIds()));

        $accessoriesrecommended_array = $this->prepareProductsCollectionArray($related_prods, $user_id);
        return $accessoriesrecommended_array;
	}

	public function getProductSpecification($setId,$product){
		$categorywise_attributegroup_collections = $attributegroupcollectionswithname = $specification = array();
		if($setId == '9'){
			// this is for the laptops
			$attribute_groups_to_show = array('Product Details', 'Processor', 'RAM (Memory)', 'Storage(HDD)', 'Storage(HDD)', 'Display', 'Graphic Card Details', 'OPERATING SYSTEM', 'WARRANTY', 'Optical Drive', 'Power', 'PORTS/SLOTS');
        }elseif($setId == '10'){
			// this is for the mobile
			$attribute_groups_to_show = array('General Features', 'Camera', 'RAM & Storage', 'Display', 'Internet & Connectivity', 'Battery', 'Platform', 'Other Details', 'Dimensions', 'Warranty');
		}elseif($setId == '11'){
			// this is for the cameras
            $attribute_groups_to_show = array('General Features', 'Technical Spec', 'Lens', 'LCD', 'Sensor', 'Battery', 'Dimensions', 'Warranty', 'In the Box');
        }elseif($setId == '19'){
			// this is for the all-in-one-desktop
			$attribute_groups_to_show = array('Product Details', 'Processor & Chipset', 'RAM (Memory)', 'Storage(HDD)', 'Display', 'Graphics', 'Input Devices', 'Network', 'Operating System', 'Warranty', 'Optical Drive', 'In the Box', 'Webcam', 'PORTS/SLOTS', 'Dimensions');
		}elseif($setId == '13'){
			// this is for the tablets
			$attribute_groups_to_show = array('Storage', 'General Features', 'Platform', 'Display', 'Camera', 'Battery', 'Multimedia', 'Internet & Connectivity', 'Warranty', 'Dimensions');
		}elseif($setId == '22'){
			// this is for the headsets
			$attribute_groups_to_show = array('General Features', 'Microphone Features','Connectivity','Sound Features','Dimensions','Warranty');
		}elseif($setId == '23'){
			// this is for the storage media
			$attribute_groups_to_show = array('General Features', 'Dimensions' ,'Warranty');
		}elseif($setId == '25'){
			// this is for the monitors
			$attribute_groups_to_show = array('General Features', 'Display Features', 'Warranty', 'Power Features', 'Weight', 'Connectivity', 'Mounting Features', 'In the Box', 'System Requirements');
        }elseif($setId == '27'){
			// this is for the keyboard and mouse
			$attribute_groups_to_show = array('General Features', 'Dimensions', 'Warranty');
		}elseif($setId == '28'){
			// this is for the speakers
			$attribute_groups_to_show = array('General Features','Power','Dimensions','Audio Features','Connectivity','Warranty','In the Box');
		}elseif($setId == '30'){
			// this is for the keyboard and mmc
			$attribute_groups_to_show = array('General Features');
		}elseif($setId == '32'){
			// this is for the power bank
			$attribute_groups_to_show = array('General Features', 'Dimensions', 'Warranty', 'In the Box');
		}elseif($setId == '33'){
			// this is for the tripod
			$attribute_groups_to_show = array('General Features', 'Dimensions', 'LEG FEATURES');
		}elseif($setId == '34'){
			// this is for the camera memory card
			$attribute_groups_to_show = array('General Features');
		}elseif($setId == '24'){
			// this is for the scanner
            $attribute_groups_to_show = array('General Feature', 'Scan', 'Copy', 'Dimensions', 'In the Box', 'Warranty', 'System Requirements');
		}elseif($setId == '29'){
			// this is for the battery adapter
			$attribute_groups_to_show = array('General Features', 'Dimensions', 'Warranty');
		}elseif($setId == '36'){
			// this is for the nd
			$attribute_groups_to_show = array('Product Details','Speed','Dimensions','Security','Power','Connectivity','System Requirements','Warranty Summary');
		}elseif($setId == '31'){
			// this is for the battery adapter
			$attribute_groups_to_show = array('General Features', 'Dimensions', 'Warranty', 'In the Box', 'Technical Spec', 'Lens Features');
		}elseif($setId == '26'){
			// this is for the printer
			$attribute_groups_to_show = array('General Features', 'Dimensions & Weight', 'Scan', 'Compatible Inks/Toners', 'Sales Package', 'Paper Handling', 'Warranty', 'System Requirements', 'Connectivity', 'Print', 'Copy');
        }elseif($setId == '37'){
			// this is for the bluetoothe
			$attribute_groups_to_show = array('General Features','Power and Battery','Sound Feature','Controls','Bluetooth','Wireless','Dimensions','Warranty','Microphone Features','Additional Features','In The Box');
		}elseif($setId == '38'){
			// this is for the mobile cases and covers
			$attribute_groups_to_show = array('General Features','Warranty','Dimensions');
		}else{
			$attribute_groups_to_show = array('Specifications');
		}

		$groups = Mage::getModel('eav/entity_attribute_group')
                    ->getResourceCollection()
                    ->setAttributeSetFilter($setId)
                    ->setSortOrder()
                    ->load();

		foreach ($groups as $group){
			if (in_array($group->getAttributeGroupName(), $attribute_groups_to_show)) {
				$categorywise_attributegroup_collections[] = $group->getAttributeGroupId();
			}
		}

        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')->load();
		foreach($categorywise_attributegroup_collections as $attributegroupcollection){
			foreach($attributeSetCollection as $id => $attributeGroup){
				if($attributeGroup->getAttributeGroupId() == $attributegroupcollection){
					$attributegroupcollectionswithname[$attributegroupcollection] = $attributeGroup->getAttributeGroupName();
				}
			}
		}

		$i = 0;

		foreach ($attributegroupcollectionswithname as $key => $value){
			$attributesCollections = Mage::getResourceModel('catalog/product_attribute_collection');
			$attributesCollections->setAttributeGroupFilter($key);
			$j = 0;
			$temp_specification = array();
			foreach ($attributesCollections as $attribute){
				if($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'boolean'){
					$sel_var_val = $product->getAttributeText($attribute->getAttributeCode());
					if (!empty($sel_var_val)){
						$temp_specs = array();
						$temp_specs['key'] = $attribute->getFrontendLabel();
						$temp_specs['value'] = $product->getAttributeText($attribute->getAttributeCode());
						$temp_specification['title'] = $value;
						$temp_specification['specs'][] = $temp_specs;
						/*
						$specification[$i]['title'] = $value;
						$specification[$i]['specs'][$j]['key'] = $attribute->getFrontendLabel();
						$specification[$i]['specs'][$j]['value'] = $product->getAttributeText($attribute->getAttributeCode());
						*/
						//$specification[$value][$attribute->getFrontendLabel()] = $product->getAttributeText($attribute->getAttributeCode());
						$j++;
					}
				}elseif ($attribute->getFrontendInput() == "text"){
					$text_var_val = $product->getData($attribute->getAttributeCode());
					if (!empty($text_var_val)){
						$temp_specs = array();
						$temp_specs['key'] = $attribute->getFrontendLabel();
						$temp_specs['value'] = $product->getData($attribute->getAttributeCode());
						$temp_specification['title'] = $value;
						$temp_specification['specs'][] = $temp_specs;
						/*
						$specification[$i]['title'] = $value;
						$specification[$i]['specs'][$j]['key'] = $attribute->getFrontendLabel();
						$specification[$i]['specs'][$j]['value'] = $product->getData($attribute->getAttributeCode());
						*/
						//$specification[$value][$attribute->getFrontendLabel()] = $product->getData($attribute->getAttributeCode());
						$j++;
					}
				}
			}
			if(!empty($temp_specification) && count($temp_specification) > 0) {
				$specification[] = $temp_specification;
			}
			$i++;
		}
		return $specification;
	}

	public function getHomePageMainProducts($page_no = 1, $page_size= 10, $user_id=null){
		$collection = array();
		$hotdeals = $this->getHotdeals($page_no,$page_size,$user_id);
		$bestsellers = $this->getBestsellerProducts($page_no,$page_size,$user_id);
		$featuredproducts = $this->getFeaturedProducts($page_no,$page_size,$user_id);
		$collection['hotdeals'] = $hotdeals['data'];
		$collection['bestsellers'] = $bestsellers['data'];
		$collection['featuredproducts'] = $featuredproducts['data'];
		return $collection;
	}

	public function homepageViewAllCollection($type,$page_no = 1,$page_size = 10,$user_id = null){
		$collection = array();
		if($type == 'hotdeals'){
			return $this->getHotdeals($page_no,$page_size,$user_id);
		}else if($type =='bestsellers'){
			return $this->getBestsellerProducts($page_no,$page_size,$user_id);
		}else if($type == 'featuredproducts'){
			return $this->getFeaturedProducts($page_no,$page_size,$user_id);
		}
		return $collection;
	}

	public function getProductCollection($category_id,$sortby=null,$page_no=1,$page_size=10,$user_id=null)
	{
		$collection = $order_direction_array = array();

		if(!empty($sortby)) {
			$order_direction_array = $this->getOrderAndDirectionOfSort($sortby);
			$_GET['order'] = $order_direction_array['order'];
			$_GET['dir'] = $order_direction_array['direction'];
		}

		$layer = Mage::getModel('catalog/layer');
		$category = Mage::getModel('catalog/category')->load($category_id);
		Mage::register('current_category',$category);
		Mage::register('current_layer',$layer);
		$attributes = $layer->getFilterableAttributes();
		$filter_attributes = array();
		foreach ($attributes as $attribute) {
			$filter_attr = array();
			$filter_attr['title'] = $attribute->getFrontendLabel();
			$filter_attr['code'] = $attribute->getAttributeCode();
			if ($attribute->getAttributeCode() == 'price') {
				$filterBlockName = 'catalog/layer_filter_price';
			} elseif ($attribute->getBackendType() == 'decimal') {
				$filterBlockName = 'catalog/layer_filter_decimal';
			} else {
				$filterBlockName = 'catalog/layer_filter_attribute';
			}
			//$attribute->getAttributeCode()."\n";
			$result = Mage::app()->getLayout()->createBlock($filterBlockName)->setLayer($layer)->setAttributeModel($attribute)->init();
			foreach($result->getItems() as $option) {
				$attr_option = array();
				if ($attribute->getAttributeCode() == 'price') {
					$attr_option['label'] = str_replace(array('<span class="price">','</span>'),'',$option->getLabel());
				} else {
					$attr_option['label'] = $option->getLabel();
				}
				$attr_option['value'] = $option->getValue();
				$attr_option['count'] = $option->getCount();
				$filter_attr['options'][] = $attr_option;
			}
			$filter_attributes[] = $filter_attr;
		}

		$layout = Mage::app()->getLayout();
		$list_block = $layout->createBlock('catalog/product_list');
		$collection = $list_block->getLoadedProductCollection();
		$collection = $list_block->getToolbarBlock()->setCollection($collection)->getCollection();
		$collection_array = $this->prepareProductsCollectionArray($collection,$user_id);

		// rearranging the sort_array parameters
		$sort_attributes_array = $this->getSortByFilterOptions();
		$sort_attributes_reframed_array = array();
		foreach ($sort_attributes_array as $key => $value) {
			array_push($sort_attributes_reframed_array, array("name"=>$value,"value"=> $key));
		}

		return array('count' => $collection->getSize(), 'result' => $collection_array, 'filter_attributes' => $filter_attributes,'sort_attributes' => $sort_attributes_reframed_array);
	}

	public function getSortByFilterOptions(){
		$sort_by_filter_array = array();
		$sort_by_filter_array['popularity']= 'Popularity';
		$sort_by_filter_array['price_low_to_high'] = 'Price -- Low to High';
		$sort_by_filter_array['price_high_to_low'] = 'Price -- High to Low';
		$sort_by_filter_array['newest_first'] = 'Newest First';
		$sort_by_filter_array['deal_products'] = 'Deal Products';
		return $sort_by_filter_array;
	}

	public function getOrderAndDirectionOfSort($sort){
		$result = array();
		if($sort == "popularity"){
			$result['order'] = 'position';
			$result['direction'] = 'asc';
		}elseif($sort == "price_low_to_high"){
			$result['order'] = 'price';
			$result['direction'] = 'asc';
		}elseif($sort == "price_high_to_low"){
			$result['order'] = 'price';
			$result['direction'] = 'desc';
		}elseif($sort == "newest_first"){
			$result['order'] = 'entity_id';
			$result['direction'] = 'desc';
		}elseif($sort == "deal_products"){
			$result['order'] = 'deals';
			$result['direction'] = 'desc';
		}
		return $result;
	}

	public function getHotdeals($page_no = 1, $page_size = 10, $user_id=null){
		$hotdeals_array = array();
		$customblocks_list = new Neo_Multipledeals_Block_List();
		$weekly_deals = $customblocks_list->getWeeklyDealsCollection();
		$result['count'] = $weekly_deals->getSize();
		if($result['count'] < 1) {
				/*echo json_encode(array('status' => 0, 'message' => 'There is no product for this crtiteria'));
				exit;*/
				return array('data' => array('product_collection'=> array()));
		} else {


			$max_page_count = ceil($result['count'] / $page_size);
			if($max_page_count < $page_no) {
				echo json_encode(array('status' => 0, 'message' => 'Page count exceeds max page count'));
				exit;
			}

			$weekly_deals->setCurPage($page_no);
			$weekly_deals->setPageSize($page_size);


			$hotdeals_array['size']['count'] = $result['count'];
			$hotdeals_array['data']['product_collection'] = $this->prepareProductsCollectionArray($weekly_deals,$user_id);
			$hotdeals_array['data']['expiry_date'] = $customblocks_list->getFinalCountDown();
			$hotdeals_array['data']['current_date'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			return $hotdeals_array;
		}
	}

	public function getBestsellerProducts($page_no = 1, $page_size = 10, $user_id=null){
		$bestseller_array = array();
		$storeId = Mage::app()->getStore()->getId();

		$products = Mage::getResourceModel('reports/product_collection')
					->addOrderedQty()
					->addAttributeToSelect('*')
					->addAttributeToFilter('status', 1) // added enabled
					->addAttributeToFilter('visibility', 4) // added
					->addAttributeToFilter('price',array('gt' => '1000')) //added for high value product
					->setStoreId($storeId)
					->addStoreFilter($storeId)
					->setOrder('ordered_qty', 'desc');
		Mage::getSingleton('cataloginventory/stock')
    ->addInStockFilterToCollection($products);
		$result['count'] = $products->getSize();
		if($result['count'] == 0) {
			/*echo json_encode(array('status' => 0, 'message' => 'There is no product for this crtiteria'));
			exit;*/
			return array('data' => array('product_collection'=> array()));
		} else {
			$max_page_count = ceil($result['count'] / $page_size);
			if($max_page_count < $page_no) {
				echo json_encode(array('status' => 0, 'message' => 'Page count exceeds max page count'));
				exit;
			}

			$products->setCurPage($page_no);
			$products->setPageSize($page_size);

			// code to have filter to show only instock products
			/*$products->joinField(
				'is_in_stock',
		        'cataloginventory/stock_item',
				'is_in_stock',
				'product_id=entity_id',
				'{{table}}.stock_id=1',
				'left'
			)->addAttributeToFilter('is_in_stock', array('eq' => 1));*/

			$bestseller_array['size']['count'] = $result['count'];
			$bestseller_array['data']['product_collection'] = $this->prepareProductsCollectionArray($products,$user_id);
			return $bestseller_array;
		}


	}

	public function getFeaturedProducts($page_no = 1, $page_size = 10, $user_id=null){
		$featured_array = array();
		// optimised query added by pradeep on 17th April 2015
		$productCollection = Mage::getResourceModel('catalog/product_collection')
	                        ->addAttributeToSelect('*')
							->addAttributeToFilter('price',array('gt' => '7000'));

		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($productCollection);

		$this->_productCollection = Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);

		// else{
		// 	$productCollection->getSelect()->limit(15);
		// }

	    $productCollection->getSelect()->order('RAND()');

	    $result['count'] = $productCollection->getSize();
		if($result['count'] == 0) {
			/*echo json_encode(array('status' => 0, 'message' => 'There is no product for this crtiteria'));
			exit;*/
			return array('data' => array('product_collection'=> array()));
		} else {
			$max_page_count = ceil($result['count'] / $page_size);
			if($max_page_count < $page_no) {
				echo json_encode(array('status' => 0, 'message' => 'Page count exceeds max page count'));
				exit;
			}

			$productCollection->setCurPage($page_no);
			$productCollection->setPageSize($page_size);

			$featured_array['size']['count'] = $result['count'];
			$featured_array['data']['product_collection'] = $this->prepareProductsCollectionArray($productCollection,$user_id);
			return $featured_array;
		}

	}

	public function getCategoryProductCollection($category_id=null,$order=null,$direction=null,$page_no=null,$page_size=null,$user_id=null){
		$catagory_array = $result = $pro_collection = array();
		$catagory_products_model = Mage::getModel('catalog/category')->load($category_id);
		if($catagory_products_model->getId()){
			$catagory_products = $catagory_products_model->getProductCollection()
								->addAttributeToSelect('*')
								->addAttributeToFilter('status',1)
								->addAttributeToFilter('visibility',4)
								->setCurPage($page_no)
								->setPageSize($page_size);

			if(empty($order) && empty($direction)){
	     		$catagory_products->addAttributeToSort('name','asc');
	     	}else{
	     		$catagory_products->addAttributeToSort($order,$direction);
	     	}

	     	//echo $products->getSelect(); exit;

			$result['count'] = $catagory_products->getSize();
			if($result['count'] == 0) {
				echo json_encode(array('status' => 0, 'message' => 'There is no product for this crtiteria'));
				exit;
			}

			$max_page_count = ceil($result['count'] / $page_size);
			if($max_page_count < $page_no) {
				echo json_encode(array('status' => 0, 'message' => 'Page count exceeds max page count'));
				exit;
			}

			$pro_collection = $this->prepareProductsCollectionArray($catagory_products,$user_id);
			$catagory_array['count'] = $result['count'];
			$catagory_array['collection'] = $pro_collection;
			return $catagory_array;
		}
	}

	// public function getFilteredCategoryProducts($category_id=null,$filter_data=null,$order=null,$direction=null,$page_no=null,$page_size=null,$user_id=null){
	// 	$filtered_prod_array = $result = $pro_collection = array();
	// 	$category_products_model = Mage::getModel('catalog/category')->load($category_id);

	// 	if($category_products_model->getId()){
	// 		$category_products = $category_products_model->getProductCollection()
	// 							->addAttributeToSelect('*')
	// 							->addAttributeToFilter('status',1)
	// 							->addAttributeToFilter('visibility',4);

	// 		if(empty($order) && empty($direction)){
	//      		$category_products->addAttributeToSort('name','asc');
	//      	}else{
	//      		$category_products->addAttributeToSort($order,$direction);
	//      	}

	// 		foreach ($filter_data as $key => $value) {
	// 			if($key == 'price'){
	// 				$from = array();
	// 				$i = 0;
	// 				foreach ($value as $keyone) {
	// 					$from_to = explode('-',$keyone);

	// 					if(empty($from_to['0'])){
	// 						$from_to['0'] = 0;
	// 						$from[$i]['from'] = $from_to['0'];
	// 						$from[$i]['to'] = $from_to['1'];
	// 					}else{
	// 						$from[$i]['from'] = $from_to['0'];
	// 						if(!empty($from_to['1'])){
	// 							$from[$i]['to'] = $from_to['1'];
	// 						}
	// 					}

	// 					$i++;
	// 				}

	// 				$category_products->addFieldToFilter('price',array($from));
	// 			}else{
	// 				$category_products->addAttributeToFilter($key,array('in' => $value));
	// 			}
	// 		}

	// 		//echo $category_products->getSelect(); exit;
	// 		if(!empty($page_no) && !empty($page_size)){
	// 			$category_products->setCurPage($page_no);
	// 			$category_products->setPageSize($page_size);
	// 		}

	// 		$result['count'] = $category_products->getSize();
	// 		if($result['count'] == 0) {
	// 			echo json_encode(array('status' => 0, 'message' => 'There is no product for this crtiteria'));
	// 			exit;
	// 		}

	// 		$max_page_count = ceil($result['count'] / $page_size);
	// 		if($max_page_count < $page_no) {
	// 			echo json_encode(array('status' => 0, 'message' => 'Page count exceeds max page count'));
	// 			exit;
	// 		}

	// 		$pro_collection = $this->prepareProductsCollectionArray($category_products,$user_id);
	// 		$filtered_prod_array['count'] = $result['count'];
	// 		$filtered_prod_array['collection'] = $pro_collection;
	// 		$filtered_prod_array['raw_collection'] = $category_products;
	// 		return $filtered_prod_array;
	// 	}
	// }

	public function prepareProductsCollectionArray($collection,$user_id=null)
	{
		$productcollection_array = array();
		$i = 0;
		foreach($collection as $product){
			/*$stock = $product->getStockItem();
			if ($stock->getIsInStock()) {*/
					$discount = $this->getDealsDiscountPercent($product->getId());
					$specialprice = $product->getSpecialPrice();
					$deal_mrpprice = $product->getDealMrpprice();
					$price = (double)$product->getPrice();
					$product_id = $product->getId();
					$productcollection_array[$i]['product_id'] = $product->getId();
					$productcollection_array[$i]['name'] = $product->getName();
					$productcollection_array[$i]['image'] = (string)Mage::helper('catalog/image')->init($product, 'thumbnail');
					$productcollection_array[$i]['price'] = $price;
					//$productcollection_array[$i]['description'] = array_values(array_filter(explode('</li>',str_replace(array('<ul>','</ul>','<li>'),'',$product->getDescription()))));
					$productcollection_array[$i]['description'] = $product->getDescription();
					if($user_id != null) {
						$productcollection_array[$i]['is_favourite'] = $this->checkIsInWishlist($user_id,$product_id);
					} else {
						$productcollection_array[$i]['is_favourite'] = false;
					}
      		$product_qty_eb = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
					$stock_eb = $product->getStockItem();
					if ($stock_eb->getIsInStock()) {
			      if($product_qty_eb == 0) {
				        $productcollection_array[$i]['is_instock'] = false;
				    } else {
				        $productcollection_array[$i]['is_instock'] = true;
				    }
					} else {
					    $productcollection_array[$i]['is_instock'] = false;
					}
					//if($specialprice){
						$productcollection_array[$i]['special_price'] = $product->getSpecialPrice();
					//}
					if($deal_mrpprice){
						$productcollection_array[$i]['deal_mrpprice'] = $product->getDealMrpprice();
					}
					if($discount > 0){
						$productcollection_array[$i]['discount'] = $discount;
					}
					$i++;
			/*} // only adding product if its in stock*/
		}

		return $productcollection_array;
	}

	public function checkIsInWishlist($user_id,$product_id){
		$hasProduct = 0;
		$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($user_id, true);
		$collection = Mage::getModel('wishlist/item')->getCollection()->addFieldToFilter('wishlist_id', $wishlist->getId())->addFieldToFilter('product_id',$product_id);
		$item = $collection->getFirstItem();
		$hasProduct = !!$item->getId();
		return $hasProduct;
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

	public function getFilterableAttributesAndOptions($category_id,$raw_collection=null)
	{
		//$count = count($raw_collection);
		// if($count){
		// 	echo $count; exit;
		// }
		$attributes_array = array();
		$layer = Mage::getModel("customapiv3/layer");
		$category = Mage::getModel("catalog/category")->load($category_id);
		$layer->setCurrentCategory($category);
		$attributes = $layer->getFilterableAttributes();

		foreach($attributes as $attribute){
		    if($attribute->getAttributeCode() == 'price'){
				$filterBlockName = 'catalog/layer_filter_price';
		    }elseif($attribute->getBackendType() == 'decimal'){
		        $filterBlockName = 'catalog/layer_filter_decimal';
		    }else{
		    	$filterBlockName = 'catalog/layer_filter_attribute';
			}
			// if($count){

				$result = Mage::app()->getLayout()->createBlock($filterBlockName)->setLayer($layer)->setAttributeModel($attribute)->init();


				//$result = $this->getCount($raw_collection, $attribute->getAttributeCode(), Mage::app()->getStore()->getId());
			//echo "<pre>"; print_r($result); exit;
		// }else{
			// $result = Mage::app()->getLayout()->createBlock($filterBlockName)->setLayer($layer)->setAttributeModel($attribute)->init();
		// }
		    //$result = Mage::app()->getLayout()->createBlock($filterBlockName)->setLayer($layer)->setAttributeModel($attribute)->init();
		    $count = count($result->getItems());
		    if($count > 0){
				$attributes_array[$attribute->getAttributeCode()]['attr_label'][] = $attribute->getFrontendLabel();
		        $attributes_array[$attribute->getAttributeCode()]['attr_code'][] = $attribute->getAttributeCode();
		    }
		    if($count > 0){
				foreach($result->getItems() as $option) {
		        	$products = $this->getProductCollectionCountByAttributeAndOptions($category_id,$attribute->getAttributeCode(),$option->getValue());
		        	$attributes_array[$attribute->getAttributeCode()]['op_id_label'][$option->getValue()] = $option->getLabel();
		        	//$attributes_array[$attribute->getAttributeCode()]['op_id'][] = $option->getValue();
		        	$attributes_array[$attribute->getAttributeCode()]['op_prod_count'][] = $products;
			    }
		    }
		}
		return $attributes_array;
	}

	public function getProductCollectionCountByAttributeAndOptions($id,$code,$value){
		$products = Mage::getModel('catalog/category')->load($id)
							->getProductCollection()
							->addAttributeToFilter('status',1)
							->addAttributeToFilter('visibility',4)
							->addAttributeToFilter($code,$value);
		return count($products);
	}

	public function getHomePageBanners(){
		$banner_block = new Magestore_Bannerslider_Block_Default();
		$banner_model = Mage::getModel('bannerslider/banner')->getCollection()->addFieldToFilter('bannerslider_id','2');
		$banners = array();
		$i = 0;
		foreach($banner_model as $banner){
			$productName = explode('/', $banner['click_url']);
			$productName = end($productName);
			$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('url_path',$productName)->getFirstItem();
			$banners[$i]['product_id'] = $products->getId();
			$banners[$i]['banner_image'] = $banner_block->getBannerImage($banner['image']);
			$i++;
		}
		return $banners;
	}

	public function getHomePageShopByCategory(){
		$categories = Mage::getModel('catalog/category')->getCollection()
    					->addAttributeToSelect('*')
    					->addAttributeToFilter('level', 2)
    					->addAttributeToFilter('is_active', 1);
    	$shopbycategory = array();
    	$category_img_url = Mage::getBaseUrl('media').'catalog/category/';
    	$i = 0;
    	foreach($categories as $category){
    		if($category->getId() == 7){
    			continue;
    		}
    		$shopbycategory[$i]['category_id'] = $category->getId();
    		$shopbycategory[$i]['category_name'] = $category->getName();
    		$shopbycategory[$i]['img_url'] = $category_img_url.$category->getImage();
    		$i++;
    	}
    	return $shopbycategory;
	}

	function getCategoriesRecursiveCall($categories) {
  		$category_container = array();
  		$i=0;
	    foreach($categories as $category) {
	        $cat = Mage::getModel('catalog/category')->load($category->getId());
	        $count = $cat->getProductCount();

	        if($category->hasChildren()) {
          	$children = Mage::getModel('catalog/category')->getCategories($category->getId());
          	$category_container[$i]["id"] = $category->getId();
          	$category_container[$i]["name"] = $category->getName();
          	$category_container[$i]["url"] = Mage::getUrl($cat->getUrlPath());
					  $category_container[$i]["icon"] = Mage::getBaseUrl('media').'catalog/category/'.Mage::getModel('catalog/category')->load($category->getId())->getThumbnail();
          	$category_container[$i]["sub_cat"] = $this->getCategoriesRecursiveCall($children);
          } else {
          	$category_container[$i]["id"] = $category->getId();
          	$category_container[$i]["name"] = $category->getName();
          	$category_container[$i]["url"] = Mage::getUrl($cat->getUrlPath());
          	$category_container[$i]["icon"] = Mage::getBaseUrl('media').'catalog/category/'.Mage::getModel('catalog/category')->load($category->getId())->getThumbnail();
          }
        $i++;
	    }
	    return $category_container;
	}

	function getThumbnailImageUrlEb()
   {
      $url = false;

      if ($image = $this->getThumbnail()) {

         $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
      }
      return $url;
   }

}
