<?php
class Neo_Customblocks_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("customblocks"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("customblocks", array(
                "label" => $this->__("customblocks"),
                "title" => $this->__("customblocks")
		   ));

      $this->renderLayout(); 
	  
    }
    
    public function compareAction() {
    	$productId = (int) $this->getRequest()->getParam('product');

	$response = array();
	$session = Mage::getSingleton('core/session');
	$session_var_arr = $session->getData('my_cookie_compare');
	if(!empty($session_var_arr)){
		if(in_array($productId, $session_var_arr)) {
			$response['status'] = 'ERROR';
			$response['message'] = 'Product Already Added To Compare List.';
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
	}
	$_productCollection = Mage::helper('catalog/product_compare')->getItemCollection();

	$comp_ids = array();

	// get current adding pro
	$product = Mage::getModel('catalog/product');

	$product_current = $product->setStoreId(Mage::app()->getStore()->getId())
            	->load($productId);
            	//Mage::log($productId,null,'productid.log');

	if(count($_productCollection) == 0){
		$session->setData('my_cookie_compare',null);
		//$ids = $product_current->getCategoryIds();
		//Mage::log($ids,null,'pcatid.log');
	}
	
	if(count($_productCollection) > 0){
		$productss = Mage::getModel('catalog/product');
		foreach($_productCollection as $_product){
			$productd = $productss->setStoreId(Mage::app()->getStore()->getId())
            			->load($_product->getProductId());
			
			//Mage::log($productd->getName(),null,'cu.log');

        	foreach($productd->getCategoryIds() as $cat_ids){
        		$comp_ids[] = $cat_ids;
        	}
			//Mage::log($comp_ids,null,'cu.log');
		}
		//Mage::log($comp_ids,null,'cu.log');
		$comp_ids = array_unique($comp_ids);
		$cu_ids = $product_current->getCategoryIds();
		//Mage::log($cu_ids,null,'cu_id.log');
		foreach($cu_ids as $cu_id){
			//Mage::log($cu_id,null,'cu_id.log');
			if(!in_array($cu_id,$comp_ids)){
				$response['status'] = 'SameCatError';
				$response['message'] = $this->__('Please add Products from Same Category');
	    		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	    		return;	
     	   	}
     	}
	}

	if(count($_productCollection) == 4)
	{
	    $response['status'] = 'ERROR';
	    $response['message'] = $this->__('There cannot be more than 4 products for comparing');
	    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	    return;
	}
	
        if($productId) {
            $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
 
			$session_var = $session->getData('my_cookie_compare');
            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getModel('catalog/product_compare_list')->addProduct($product);

				if(count($session_var) >= 4){
					//Mage::log('aaaaa',null,'helloCA.log');
					$session->setData('my_cookie_compare',null);
				}

				if(!empty($session_var)){
					$session_var[] = $productId;
					$session->setData('my_cookie_compare',$session_var);
					Mage::log(count($session_var),null,'hellocount.log');
				}else{
					$session->setData('my_cookie_compare', array($productId));	
					Mage::log(count($session_var),null,'emptycount.log');
				}

                //$session = Mage::getSingleton('core/session');				

                $response['status'] = 'SUCCESS';
                $response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
                Mage::register('referrer_url', $this->_getRefererUrl());
                Mage::helper('catalog/product_compare')->calculate();
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
		
		        $this->loadLayout();
                $sidebar_block = $this->getLayout()->getBlock('ajax-addtocompare');
                $sidebar_block->setTemplate('customblocks/addtocompare.phtml');
                $sidebar = $sidebar_block->toHtml();
                $response['sidebar'] = $sidebar;
		
		//$response['sidebar'] = "<div class='block-compare'><p>Hello Pradeep</p></div>";
		
                //$this->loadLayout();
                //$sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
                //$sidebar_block->setTemplate('customblocks/addtocompare.phtml');
                //$sidebar = $sidebar_block->toHtml();
                //$response['sidebar'] = $sidebar;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

    public function removeproAction(){
		$productId = (int) $this->getRequest()->getParam('product');
		$product = Mage::getModel('catalog/product')->load($productId);
		$response = array();
		$session = Mage::getSingleton('core/session');
		$session_var = $session->getData('my_cookie_compare');
		
		//Mage::log("Before Unset",null,'atcancelitemsfromcomparelist.log');
		//Mage::log($session_var,null,'atcancelitemsfromcomparelist.log');
			
		if(($key = array_search($productId,$session_var))!== false) {
			unset($session_var[$key]);
		}
		$session->setData('my_cookie_compare',$session_var);
	
		//Mage::log("After Unset",null,'atcancelitemsfromcomparelist.log');
		//Mage::log($session_var,null,'atcancelitemsfromcomparelist.log');
	
		Mage::getModel('catalog/product_compare_list')->removeProduct($product);
		
		if(count($session_var) == 0){
			$response['status'] = 'empty';
	    	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	    	return;	
		}
		/*
			$cancel_itemid = $this->getRequest()->getParam('cancel_itemid');
			$session = Mage::getSingleton('core/session');
			$session_var = $session->getData('my_cookie_compare');
	
			Mage::log("Before Unset",null,'atcancelitemsfromcomparelist.log');
			Mage::log($session_var,null,'atcancelitemsfromcomparelist.log');
			
			if(($key = array_search($cancel_itemid,$session_var))!== false) {
			    unset($session_var[$key]);
			}
			$session->setData('my_cookie_compare',$session_var);
	
			Mage::log("After Unset",null,'atcancelitemsfromcomparelist.log');
			Mage::log($session_var,null,'atcancelitemsfromcomparelist.log');		
		*/
		//echo 'succ'; exit;
		/*$response = array();
		$session = Mage::getSingleton('core/session');
		$_productCollection = Mage::helper('catalog/product_compare')->getItemCollection();

		$comp_ids = array();

		// get current adding pro
		$product = Mage::getModel('catalog/product');
		Mage::getModel('catalog/product_compare_list')->addProduct($product);*/
	}
    
    public function getIndianCitiesAction(){
	//$regionCollection = Mage::getModel('directory/region_api')->items('IN')->toOptionArray(false);
	//echo "<pre>"; print_r($regionCollection); exit;
	$statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter('IN')->load()->toOptionArray(false);
	echo $statearray; exit;
	//echo json_encode($statearray); exit;
	
	echo "<pre>"; print_r($statearray); exit;
	$responsearray = array();
	foreach($regionCollection as $region){
	    $responsearray[]['region_id'] = $region['region_id'];
	    $responsearray[]['code'] = $region['code'];
	}
	echo json_encode($responsearray); exit;
    }
    
    public function getcategorybrandsAction(){
	$brand = $this->getRequest()->getParam('brand');
	$parent_category_id = $this->getRequest()->getParam('parent_category_id');
	$collections = Mage::getModel('catalog/category')->load($parent_category_id)->getProductCollection()->addAttributeToSelect('*');
	$brands_array = array();
	foreach($collections as $product){
	    $brands_array[] = $product->getBrands();
	}
	$final_brand_id = array_unique($brands_array);
	$brands_label = array();
	$_product = Mage::getModel('catalog/product');
	    $attr = $_product->getResource()->getAttribute("brands");
	    if($attr->usesSource()){
		foreach($final_brand_id as $final_brand_id){
		    $brands_label[$final_brand_id] = $attr->getSource()->getOptionText($final_brand_id);
		}
		//$brands_label = $attr->getSource()->getOptionText($brand_no);
	    }
	    echo json_encode($brands_label); exit;
	//echo "<pre>"; print_r($brands_label); exit;
	
    }
    
    public function getbrandproductsAction(){
	$brand = $this->getRequest()->getParam('brand');
	$parent_category_id = $this->getRequest()->getParam('parent_category_id');
	$products = Mage::getModel('catalog/category')->load($parent_category_id)->getProductCollection()->addAttributeToFilter('brands',$brand)->addAttributeToSelect('*');
	//$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('brands',$brand)->addAttributeToSelect('*');
	$response_product = array();
	$_productCollection = Mage::helper('catalog/product_compare')->getItemCollection();
	//echo "<pre>"; print_r($_productCollection->getData()); exit;
	$product_in_compare = array();
	foreach($_productCollection as $product) {
           $product_in_compare[] = $product->getId();
        }
	
	//echo "<pre>"; print_r($product_in_compare); exit;
	foreach($products as $product){
	    if(!in_array($product->getId(),$product_in_compare)){
		$response_product[$product->getId()] = $product->getName();
	    }
	}
	echo json_encode($response_product); exit;
    }
    
    public function getbrandproductdetailsAction(){
	$response = array();
	$brandproid = $this->getRequest()->getParam('brandproid');
	Mage::log($brandproid,null,'atgetbrandproductdetails.log');
	//$products = Mage::getModel('catalog/product')->load($brandproid);
	//$productId = $products->getId();
	//Mage::log('After Product Model Load',null,'atgetbrandproductdetails.log');
	//Mage::log($productId,null,'atgetbrandproductdetails.log');

	$session = Mage::getSingleton('core/session');
	$session_var = $session->getData('my_cookie_compare');
	//Mage::log($session_var,null,'hellossss.log');
	//Mage::log('a',null,'hellossss.log');
	$session_var[] = $brandproid;
	Mage::log('Before Array Unique',null,'atgetbrandproductdetails.log');
	Mage::log($session_var,null,'atgetbrandproductdetails.log');
	$session_var = array_unique($session_var);
	$session->setData('my_cookie_compare',$session_var);
	Mage::log($session_var,null,'atgetbrandproductdetails.log');
	Mage::log('After Array Unique',null,'atgetbrandproductdetails.log');
	//Mage::log($session_var,null,'hellossss.log');

	//Mage::log($session_var,null,'hello.log');
	/*if(count($session_var) > 4){
	    $session->setData('my_cookie_compare',null);
	}*/

	Mage::getModel('catalog/product_compare_list')->addProducts($session_var);
	
	//$img = Mage::helper('catalog/image')->init($products,'small_image')->resize(125, 125);
	//$product_url = $products->geturlPath();
	$this->loadLayout();
	$sidebar_blocks = $this->getLayout()->getBlock('catalog.compare.list');
	$sidebar_blocks->setTemplate('catalog/product/compare/list.phtml');
	$sidebar = $sidebar_blocks->toHtml();
	$response['sidebar'] = $sidebar;
	
	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
    
    public function addBundledProducttToCartAction(){
    	$response = array();
	//$id = $this->getRequest()->getParam('proid');
	$cart = Mage::getModel('checkout/cart');
	$cart->init();

	//$params = $this->getRequest()->getParams();
	$productId = $this->getRequest()->getParam('proid');
	$selectionids = $this->getRequest()->getParam('selectionids');
	$option_id = $this->getRequest()->getParam('option_id');
	$product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
	
	if(empty($selectionids)){
	    //Mage::log('empty',null,'bundle.log');
	    $response['status'] = 'EmptySelectionIds';
		$response['message'] = $this->__('Please Select Products');
	    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	    return;	
	}
	/*foreach($selectionids as $selectionid){
	    Mage::log("Selection Ids".$selectionid,null,'bundle.log');   
	}
	foreach($option_id as $option){
	    Mage::log("Option ids".$option,null,'bundle.log');   
	}*/

    if($product->getTypeId() == "bundle"){

    $bundled_items = array();
    /*$optionCollection = $product->getTypeInstance()->getOptionsCollection();
    $selectionCollection = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds());
    $options = $optionCollection->appendSelections($selectionCollection);
    */
    
    foreach($option_id as $option) {
	foreach($selectionids as $selectionid){
	    $bundled_items[$option][] = $selectionid;
	}
    }
    /*foreach($options as $option) {
        $_selections = $option->getSelections();

        foreach($_selections as $selection) {
            $bundled_items[$option->getOptionId()][] = $selection->getSelectionId();
        }
    }*/

    $params = array('bundle_option' => $bundled_items,'qty' => 1,'product'=>$productId);
}

if (isset($params['qty'])) {
    $filter = new Zend_Filter_LocalizedToNormalized(
        array('locale' => Mage::app()->getLocale()->getLocaleCode())
        );
    $params['qty'] = $filter->filter($params['qty']);
}

$product = new Mage_Catalog_Model_Product();
$product->load($productId);

$cart->addProduct($product, $params);
$cart->save();

Mage::dispatchEvent('checkout_cart_add_product_complete',
    array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
);

		$response['status'] = 'Success';
		$response['message'] = $this->__('Combo Product(s) added to cart');
	    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	    return;
	
	//header("Location: ".$add2CartUrl);
     
	
    }

	public function updateBundlepropriceAction(){
		$bundproprice = $this->getRequest()->getParam('bundproprice');
		$selectionprice = $this->getRequest()->getParam('selectionprice');
		$selectionfinalprice;
		foreach($selectionprice as $selection){
			$selectionfinalprice += $selection;
		}
		
		$finalprice = $bundproprice + $selectionfinalprice;
		echo Mage::helper('core')->currency($finalprice); exit;
		//echo $finalprice; exit;   
	}
    
    public function setproinregistryAction(){
	$a = $this->getRequest()->getParam('a');
	Mage::register('product_id',$a);
	$this->loadLayout();
	$sidebar_blocks = $this->getLayout()->createBlock('customblocks/index');
	$sidebar_blocks->setTemplate('catalog/product/customblocks/shareyourpurchase.phtml');
	$sidebar = $sidebar_blocks->toHtml();
	$response['sidebar'] = $sidebar;
	
	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
    
    public function cancelitemfromcomparelistAction(){
	$cancel_itemid = $this->getRequest()->getParam('cancel_itemid');
	$session = Mage::getSingleton('core/session');
	$session_var = $session->getData('my_cookie_compare');
	
	Mage::log("Before Unset",null,'atcancelitemsfromcomparelist.log');
	Mage::log($session_var,null,'atcancelitemsfromcomparelist.log');
	
	if(($key = array_search($cancel_itemid,$session_var))!== false) {
	    unset($session_var[$key]);
	}
	$session->setData('my_cookie_compare',$session_var);
	
	Mage::log("After Unset",null,'atcancelitemsfromcomparelist.log');
	Mage::log($session_var,null,'atcancelitemsfromcomparelist.log');
	
	Mage::getModel('catalog/product_compare_list')->addProducts($session_var);
	
	//$img = Mage::helper('catalog/image')->init($products,'small_image')->resize(125, 125);
	//$product_url = $products->geturlPath();
	$this->loadLayout();
	$sidebar_blocks = $this->getLayout()->getBlock('catalog.compare.list');
	$sidebar_blocks->setTemplate('catalog/product/compare/list.phtml');
	$sidebar = $sidebar_blocks->toHtml();
	$response['sidebar'] = $sidebar;
	
	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
    
    public function loginPostAction()
    {      
	$result = array();     
	$session = Mage::getSingleton('customer/session');
	
	$login = $this->getRequest()->getPost('login');  
	if (!empty($login['username']) && !empty($login['password'])) {
	    try {
		if ($session->login($login['username'], $login['password'])){
		    $result['logined'] = 1;
		    $result['user'] = $session->getCustomer()->getName();
		}
		    
		// If this user just confirmed registration, it need to send him/her a confirmation email.
	    } catch (Exception $e) {
		// Error handling
		$result['resp'] = 'Username or password is wrong';
	    }
	}
	else{
	    // Username or password is wrong.
	    //$result['resp'] = 'Username or password is wrong';
	}
	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function htdeAction(){
		$isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {
        	$repcode = Mage::app()->getRequest()->getParam('repcode');
        	$customer = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('repcode',$repcode)->getFirstItem();
        	$response_array = array();
        	if($customer->getId()){
        		$response_array['used_available'] = 'Hi Admin How are You? Repcode '.$repcode.' already used for '.$customer->getName();
        	}else{
        		$response_array['used_available'] = 'Hi Admin How are You? Repcode '.$repcode.' is available for use';
        	}
        	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response_array));
            //$layout = $this->getLayout();
            //$update = $layout->getUpdate();
        }
	}

	public function addShoppingPromoProToCartAction(){
		$response = array();
		$selectionskus = $this->getRequest()->getPost('selectionskus');
		foreach($selectionskus as $selectionsku){
			$_productId = Mage::getModel('catalog/product')->getIdBySku($selectionsku);
			$_product = Mage::getModel('catalog/product')->load($_productId);
			$qty = '1';
			try {
				$cart = Mage::getModel('checkout/cart');
				$cart->init();
				$cart->addProduct($_product, array('qty' => $qty));
				$cart->save();
				Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
				$response['status'] = 'success';
				$response['message'] = $this->__('Successfully Added to cart');
				//$result['success'] = 'Successfully Added to cart';
			}
			catch(Exception $e) {
				$response['status'] = 'error';
				$response['message'] = $e->getMessage();
				//$result['success'] = $e->getMessage();
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		}	
	}
	
	public function bestsellerAction() {
		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>1));
		$dealproductIds = array();
		if (count($dealCollection)) {
			foreach ($dealCollection as $deal) {
					$dealproductIds[] = $deal->getProductId();
			}
		}
		#print_r($dealproductIds);
		#echo 'success';
		$product_list_block = new Mage_Catalog_Block_Product_List();
		$customblocks_index = new Neo_Customblocks_Block_Index();
		$cat_id = 8;
		$category = Mage::getModel('catalog/category')->load($cat_id);
		#$bestsellingproducts = $category->getProductCollection()->addAttributeToSort('position')->addAttributeToFilter('status', 1)->addUrlRewrite($cat_id);
		$bestsellingproducts = $category->getProductCollection()->addAttributeToSort('position')->addAttributeToFilter('status', 1);
		Mage::getModel('catalog/layer')->prepareProductCollection($bestsellingproducts);
		#echo count($bestsellingproducts);
		if(count($bestsellingproducts) > 0):
			#$html = '<h2 class="home-slider-heading">Best Sellers</h2>';
			#$html .= '<ul class="products-grid products-grid--max-4-col first last odd" id="homeproslider1">';
			foreach($bestsellingproducts as $bestsellerproductdetail):
				$html .= '<li class="item">';
				$html .= '<div class="product-view" data-pid="' . $bestsellerproductdetail->getId() .'"><span><a href="javascript:void(0)">Quick View</a></span></div><div class="clr"></div>';
				$html .= '<a href="'. $bestsellerproductdetail->getProductUrl() . '" title="'.$bestsellerproductdetail->getName().'" class="product-image">';
				$special_price = $bestsellerproductdetail->getSpecialPrice();
				$now = date("Y-m-d");
				$newsFrom = substr($bestsellerproductdetail->getData('news_from_date'),0,10);
				$newsTo =  substr($bestsellerproductdetail->getData('news_to_date'),0,10);
				if(!empty($special_price)):
					$html .= '<div class="product-offer"><span>Offer</span></div>';
				elseif(!empty($newsFrom) || !empty($newsTo)):
					if($now>=$newsFrom && $now<=$newsTo): 
						$html .= '<div class="product-new"><span>New</span></div>';
					endif;
				endif;
				$_imgSize = 210;
				$stockItem = $bestsellerproductdetail->getStockItem();
                    if(!$stockItem->getIsInStock()):
                        $html .= '<span class="out-of-stock"><span>Sold Out</span></span>';
                    endif;
					$html .= '<img id="product-collection-image-'.$bestsellerproductdetail->getId().'" src="'.Mage::helper('catalog/image')->init($bestsellerproductdetail,'small_image')->resize($_imgSize).'" width="'.$_imgSize.'" height="'.$_imgSize.'" alt="'.$bestsellerproductdetail->getName().'" />';
					$html .= '<div class="mask"></div>';
				$html .= '</a>';
				
				
				$html .= '<div class="product-info">';
				if(strlen($bestsellerproductdetail->getName()) > 24) 
				{
					 $end_str = '...'; 
				}
				$html .= '<h2 class="product-name"><a href="'.$bestsellerproductdetail->getProductUrl().'" title="'.$bestsellerproductdetail->getName().'">'.substr($bestsellerproductdetail->getName(),0,24).''.$endstr.'</a></h2>';
				$specialprice = $bestsellerproductdetail->getSpecialPrice();
				$finalprice = $bestsellerproductdetail->getFinalPrice();
				$product_id = $bestsellerproductdetail->getId();
				if(in_array($product_id, $dealproductIds)):
					if(!empty($finalprice) && $finalprice>0):
						$html .= '<div class="price-info"><div class="price-box">';
						$html .= '<p class="old-price">';
						$html .= '<span class="price-label">Regular Price:</span>';
						$html .= '<span class="price">'. Mage::helper('core')->currency($bestsellerproductdetail->getPrice()).'</span>';
						$html .= '</p>';
						$html .= '<p class="special-price"><span class="price-label">Special Price</span>';
						$html .= '<span class="price">'.Mage::helper('core')->currency($bestsellerproductdetail->getFinalPrice()).'</span>';
						$html .= '</p>';
						$html .= '</div></div>';
					else:
						$html .= '<div class="price-box"><span class="regular-price">';
						$html .= '<span class="price">'. Mage::helper('core')->currency($bestsellerproductdetail->getPrice()).'</span>';
						$html .= '</span></div>';
					endif;
					
				else:
					if(!empty($specialprice)):
						$html .= '<div class="price-info"><div class="price-box">';                              
						$html .= '<p class="old-price">';
						$html .= '<span class="price-label">Regular Price:</span>';
						$html .= '<span class="price">'. Mage::helper('core')->currency($bestsellerproductdetail->getPrice()) .'</span>';
						$html .= '</p>';
						$html .= '<p class="special-price">';
						$html .= '<span class="price-label">Special Price</span>';
						$html .= '<span class="price">'. Mage::helper('core')->currency($bestsellerproductdetail->getFinalPrice()).'</span>';
						$html .= '</p>';
						$html .= '</div></div>';
					else:
						$html .= '<div class="price-box">';
						$html .= '<span class="regular-price">';
						$html .= '<span class="price">'. Mage::helper('core')->currency($bestsellerproductdetail->getPrice()).'</span>';
						$html .= '</span>';
						$html .= '</div>';
					endif;
				endif;
			$html .= '</div>';
			$html .= '<div class="short-description">';
			$html .= '<div class="c-prod-desc">'.$bestsellerproductdetail->getImpDesc().'</div>';
			$html .= '</div>';	
			$html .= '<div class="bottom-gadget">';
			$html .= '<div class="gadets-addtocomare">';
			$_compareUrl = Mage::helper('catalog/product_compare')->getAddUrl($bestsellerproductdetail);
			$html .= '<div class="checkbox_sample sample">';
			$html .= '<input type="checkbox" class="checkbox" id="products_'.$bestsellerproductdetail->getId().'" onclick="ajaxCompare(\''.$_compareUrl . '\',' . $bestsellerproductdetail->getId().');" class="link-compare" /><label class="css-label" for="products_'.$bestsellerproductdetail->getId().'">Add to Compare</label>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="actions">';
			$html .= '<button class="button btn-cart" title="Buy now" type="button" onclick="setLocation(\''.$product_list_block->getAddToCartUrl($bestsellerproductdetail).'\')"><span><span>Buy now</span></span></button>';
			$html .= '<ul class="add-to-links">';
			$html .= '<li><a class="link-wishlist" onclick="setLocation('.$product_list_block->getAddToWishlistUrl($bestsellerproductdetail). ')">Add to Wishlist</a></li>';
			$html .= '</ul>';
			$html .= '<div class="clr"></div>';
			$html .= '</div>';
			$html .= '</div>';
			
			if(in_array($product_id, $dealproductIds)) {
				$discount_percent = $customblocks_index->getDealsDiscountPercent($product_id);
				if($discount_percent > 0):
					$html .= '<div class="tag"><p>'.$discount_percent.'% <span>Off</span></p></div>';
				endif;
			}
				
				
			$html .= '</li>';
			endforeach;
			#$html .= '</ul>';
			echo $html;
		endif;
	}

	public function hotdealsAction() {
		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>1));
		$dealproductIds = array();
		if (count($dealCollection)) {
			foreach ($dealCollection as $deal) {
					$dealproductIds[] = $deal->getProductId();
			}
		}
		$product_list_block = new Mage_Catalog_Block_Product_List();
		$customblocks_list = new Neo_Multipledeals_Block_List();
		$customblocks_index = new Neo_Customblocks_Block_Index();
		$weekly_deals = $customblocks_list->getWeeklyDealsCollection();
		$weekly_deal_count = count($weekly_deals);
		if(count($weekly_deal_count) > 0):
			#$html = '<h2 class="home-slider-heading">Best Sellers</h2>';
			#$html .= '<ul class="products-grid products-grid--max-4-col first last odd" id="homeproslider1">';
			//foreach($bestsellingproducts as $bestsellerproductdetail):
			foreach($weekly_deals as $_dealproduct):
				$html .= '<li class="item">';
				$html .= '<div class="product-view" data-pid="' . $_dealproduct->getId() .'"><span><a href="javascript:void(0)">Quick View</a></span></div><div class="clr"></div>';
				$html .= '<a href="'. $_dealproduct->getProductUrl() . '" title="'.$_dealproduct->getName().'" class="product-image">';
				$special_price = $_dealproduct->getSpecialPrice();
				$now = date("Y-m-d");
				$newsFrom = substr($_dealproduct->getData('news_from_date'),0,10);
				$newsTo =  substr($_dealproduct->getData('news_to_date'),0,10);
				if(!empty($special_price)):
					$html .= '<div class="product-offer"><span>Offer</span></div>';
				elseif(!empty($newsFrom) || !empty($newsTo)):
					if($now>=$newsFrom && $now<=$newsTo): 
						$html .= '<div class="product-new"><span>New</span></div>';
					endif;
				endif;
				$_imgSize = 210;
				$stockItem = $_dealproduct->getStockItem();
                    if(!$stockItem->getIsInStock()):
                        $html .= '<span class="out-of-stock"><span>Sold Out</span></span>';
                    endif;
					$html .= '<img id="product-collection-image-'.$_dealproduct->getId().'" src="'.Mage::helper('catalog/image')->init($_dealproduct,'small_image')->resize($_imgSize).'" width="'.$_imgSize.'" height="'.$_imgSize.'" alt="'.$_dealproduct->getName().'" />';
					$html .= '<div class="mask"></div>';
				$html .= '</a>';
				
				
				$html .= '<div class="product-info">';
				if(strlen($_dealproduct->getName()) > 24) 
				{
					 $end_str = '...'; 
				}
				$html .= '<h2 class="product-name"><a href="'.$_dealproduct->getProductUrl().'" title="'.$_dealproduct->getName().'">'.substr($_dealproduct->getName(),0,24).''.$endstr.'</a></h2>';
				$specialprice = $_dealproduct->getSpecialPrice();
				$finalprice = $_dealproduct->getFinalPrice();
				$product_id = $_dealproduct->getId();
				if(in_array($product_id, $dealproductIds)):
					if(!empty($finalprice) && $finalprice>0):
						$html .= '<div class="price-info"><div class="price-box">';
						$html .= '<p class="old-price">';
						$html .= '<span class="price-label">Regular Price:</span>';
						$html .= '<span class="price">'. Mage::helper('core')->currency($_dealproduct->getPrice()).'</span>';
						$html .= '</p>';
						$html .= '<p class="special-price"><span class="price-label">Special Price</span>';
						$html .= '<span class="price">'.Mage::helper('core')->currency($_dealproduct->getFinalPrice()).'</span>';
						$html .= '</p>';
						$html .= '</div></div>';
					else:
						$html .= '<div class="price-box"><span class="regular-price">';
						$html .= '<span class="price">'. Mage::helper('core')->currency($_dealproduct->getPrice()).'</span>';
						$html .= '</span></div>';
					endif;
					
				else:
					if(!empty($specialprice)):
						$html .= '<div class="price-info"><div class="price-box">';                              
						$html .= '<p class="old-price">';
						$html .= '<span class="price-label">Regular Price:</span>';
						$html .= '<span class="price">'. Mage::helper('core')->currency($_dealproduct->getPrice()) .'</span>';
						$html .= '</p>';
						$html .= '<p class="special-price">';
						$html .= '<span class="price-label">Special Price</span>';
						$html .= '<span class="price">'. Mage::helper('core')->currency($_dealproduct->getFinalPrice()).'</span>';
						$html .= '</p>';
						$html .= '</div></div>';
					else:
						$html .= '<div class="price-box">';
						$html .= '<span class="regular-price">';
						$html .= '<span class="price">'. Mage::helper('core')->currency($_dealproduct->getPrice()).'</span>';
						$html .= '</span>';
						$html .= '</div>';
					endif;
				endif;
			$html .= '</div>';
			$html .= '<div class="short-description">';
			$html .= '<div class="c-prod-desc">'.$_dealproduct->getImpDesc().'</div>';
			$html .= '</div>';	
			$html .= '<div class="bottom-gadget">';
			$html .= '<div class="gadets-addtocomare">';
			$_compareUrl = Mage::helper('catalog/product_compare')->getAddUrl($_dealproduct);
			$html .= '<div class="checkbox_sample sample">';
			$html .= '<input type="checkbox" class="checkbox" id="products_'.$_dealproduct->getId().'" onclick="ajaxCompare(\''.$_compareUrl . '\',' . $_dealproduct->getId().');" class="link-compare" /><label class="css-label" for="products_'.$_dealproduct->getId().'">Add to Compare</label>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="actions">';
			$html .= '<button class="button btn-cart" title="Buy now" type="button" onclick="setLocation(\''.$product_list_block->getAddToCartUrl($_dealproduct).'\')"><span><span>Buy now</span></span></button>';
			$html .= '<ul class="add-to-links">';
			$html .= '<li><a class="link-wishlist" onclick="setLocation('.$product_list_block->getAddToWishlistUrl($_dealproduct). ')">Add to Wishlist</a></li>';
			$html .= '</ul>';
			$html .= '<div class="clr"></div>';
			$html .= '</div>';
			$html .= '</div>';
			
			if(in_array($product_id, $dealproductIds)) {
				$discount_percent = $customblocks_index->getDealsDiscountPercent($product_id);
				if($discount_percent > 0):
					$html .= '<div class="tag"><p>'.$discount_percent.'% <span>Off</span></p></div>';
				endif;
			}
				
				
			$html .= '</li>';
			endforeach;
			#$html .= '</ul>';
			echo $html;
		endif;
	}

}