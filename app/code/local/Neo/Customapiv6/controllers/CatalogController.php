<?php
class Neo_Customapiv6_CatalogController extends Neo_Customapiv6_Controller_HttpAuthController
{
	public function searchProductsAction()
	{
		$customer_id = $_REQUEST['user_id'];
		$keyword = $_REQUEST['keyword'];

		//mage::log($keyword,null,'search.log'); 
		$page_size = $this->getRequest()->getParam('pagesize');
		$page_no = $this->getRequest()->getParam('pageno');

		//$this->validateCustomer($customer_id);
		try {
			$data = Mage::getModel('customapiv6/catalog')->getSearchProductList($keyword, $customer_id, $page_no, $page_size);


			echo json_encode(array('status' => 1, 'data' => $data)); 
			exit;
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}

	public function getCategoryProductAction()
	{
		$this->getResponse()->setHeader('Content-type', 'application/json', true);

		$category_id = $this->getRequest()->getParam('category_id');
		$customer_id = $this->getRequest()->getParam('user_id');

		$ibmsale_cat_id = Mage::getStoreConfig('settings/category/id');
	    if($ibmsale_cat_id == $category_id){
	        $success = Mage::getModel('customer/customer')->checkpasswordExpire($customer_id);
	        if(!$success){
	        	$result = array('status' => 0, 'message' => 'Your password is expired. Please check your email for new password', 'do_logout'=> 'Yes');
				echo Mage::helper('core')->jsonEncode($result);
				exit;
	        }
	    }

		$category = Mage::getModel('catalog/category')->load($category_id);
		$category_data = $category->getData();
		$page_size = $this->getRequest()->getParam('page_size');
		$page_no = $this->getRequest()->getParam('page_no');
		if(!isset($category_data['entity_id'])) {
			$result = array('status' => 0, 'message' => 'Please provide valid category id');
			echo Mage::helper('core')->jsonEncode($result);
			exit;
		} else if(empty($page_size) || empty($page_no)) {
			$result = array('status' => 0, 'message' => 'Please provide valid page size and page no.');
			echo Mage::helper('core')->jsonEncode($result);
			exit;
		}

		try {
			$_GET['p'] = $page_no;
			$_GET['limit'] = $page_size;

			$sortby = $this->getRequest()->getParam('sortby');
			// $sortby = $this->getRequest()->getParam('order');
			$user_id = $this->getRequest()->getParam('user_id');
			//$filter = '{"price":["100000-120000","150000-"]}';
			//$filter = '{"brands":[7,4],"laptop_processor":[1578,1397]}';  
			//$filter = '{"laptop_processor":[1578,1397]}';
			$result = array();
			$tmp = Mage::getModel('customapiv6/catalog')->getProductCollection($category_id,$sortby,$page_no,$page_size,$user_id);
			$tmp['status'] = 1;
			$tmp['do_logout'] = 'No';
			$result = $tmp;
		} catch (Exception $e) {
			$result['status'] = 0;
			$result['message'] = $e->getMessage();
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	public function homepageViewAllAction()
	{
		$this->getResponse()->setHeader('Content-type', 'application/json', true);
		$type = $this->getRequest()->getParam('type');
		if(in_array($type, array('hotdeals','bestsellers','featuredproducts'))){
			$page_size = $this->getRequest()->getParam('page_size');
			$page_no = $this->getRequest()->getParam('page_no');
			if(!empty($page_size) && !empty($page_no)){
				try {
					$user_id = $this->getRequest()->getPost('user_id');
					$result = array();
					$tmp = Mage::getModel('customapiv6/catalog')->homepageViewAllCollection($type,$page_no,$page_size,$user_id);
					$result['status'] = 1;
					$result['count'] = $tmp['size']['count'];
					if (is_array($tmp)) {
						$result['data'] = $tmp['data'];
					} else {
						$result['message'] = $tmp;
					}
				} catch (Exception $e) {
					$result['status'] = 0;
					$result['message'] = $e->getMessage();
				}
			}else{
				$result = array('status' => 0, 'message' => 'Please provide Page Size and Page Number',);
			}
		}else{
			$result = array('status' => 0, 'message' => 'Please provide valid listing type',);
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	public function homepagebannernewAction()
	{
		$return = array();


		try{

			$return[0]['keyword']       = 'albright';
			$return[0]['category_id']   = '7'; 
			$return[0]['category_name'] = 'Accessories';
			$return[0]['banner_type']   = 'product';
			$return[0]['event_click_label']   = 'Rockman-S';
			$return[0]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/albright/rockman-s.jpg';
			//$return[0]['category_url']  = 'http://www.electronicsbazaar.com/catalogsearch/result/?q=albright+l4';

			$return[1]['category_id']   = 60;
			$return[1]['banner_type']   = 'category';
			$return[1]['category_name']   = 'Open Box';
			$return[1]['event_click_label']   = 'open-box-laptop';
			// $return[1]['banner_image']  = 'http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/openbox-laptop-new.jpg';
			$return[1]['banner_image']  = 'http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app060417/open-box-laptop.png';
			//$return[1]['category_url']  = 'http://www.electronicsbazaar.com/refurbished';

			$return[2]['category_id']   = 94; 
			$return[2]['banner_type']   = 'category';
			$return[2]['category_name']   = 'Open Box';
			$return[2]['event_click_label']   = 'open-box-mobile';
			// $return[2]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/openbox-mobile-new.jpg';
			$return[2]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app060417/open-box-mobile.png';
			//$return[2]['category_url']  = 'http://www.electronicsbazaar.com/unboxed-units';

			//http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/merry-christmas.jpg
			//$return[3]['category_id']   = 3;
			$return[3]['banner_type']   = 'product';
			$return[3]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/samsung-banner.jpg';
			$return[3]['category_url']  = 'dealsproductscollection';
    
			$return[4]['banner_type']   = 'register'; 
			// $return[4]['banner_image']  = 'http://www.electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/200-off-small.jpg';
			$return[4]['category_url']  = 'login';
			$return[4]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app060417/register-now.png'; 

			$return[5]['banner_type']   = 'home-popup1';
			$return[5]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/republic-day-new1.jpg';
			$return[5]['category_url']  = 'popup';   

  			$return[6]['category_id']   = 11; 
			$return[6]['banner_type']   = 'category';
			$return[6]['category_name']   = 'Pre Owned'; 
			$return[6]['event_click_label']   = 'pre-owned-laptop';
			// $return[6]['banner_image']  = 'http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/preowned-laptop.jpg';
			$return[6]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app060417/pre-owned-laptop.png';

			$return[7]['category_id']   		= 97; 
			$return[7]['banner_type']   		= 'category';
			$return[7]['category_name']         = 'Pre Owned';
			$return[7]['event_click_label']     = 'pre-owned-mobile';
			// $return[7]['banner_image']  		= 'http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/preowned-mobile.jpg';
			$return[7]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app060417/pre-owned-mobile.png';

			$return[8]['banner_type']   = 'home-page';
			$return[8]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app060417/deals.png';
			// $return[8]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/new-banner/25-04-2017/mobile-sticker-25-april-2017.png';
			// $return[8]['event_click_label']     = 'Big-Day-Sale-25Apr17';
			$return[8]['text']  		= 'exciting-offers'; 

			$return[9]['banner_type']   = 'hourly-deal';
			// $return[9]['banner_image']  = 'https://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app/hourly-deals-app.jpg';
			$return[9]['text']  		= 'coming-soon'; 

			$return[10]['banner_type']   = 'partner-program1'; 
			// $return[10]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app/partner-program.jpg';
			$return[10]['text']  		= 'Partner Program';
			$return[10]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app060417/partner-program.png'; 
   			
   			$return[11]['banner_type']   = 'sell-your-gadget';
			$return[11]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/gadget/images/sell-your-gadget-banner.jpg';
			$return[11]['text']  		 = 'Sell Your Gadget'; 
   
			$return[12]['banner_type']   = 'lenovo-program1';  //add 1 for not showing in app
			// $return[12]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/gadget/images/lenovo_banner.jpg';
			$return[12]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/gadget/images/lenovo_program.jpg';
			$return[12]['text']  		 = 'Lenovo Program'; 
			
			$return[13]['banner_type']   = 'warranty';
			$return[13]['banner_image']  = 'https://www.electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app/1-yr-warranty.jpg';
			$return[13]['event_click_label']     = 'warranty';     
			$return[13]['text']  		= 'Warranty'; 

			echo json_encode(array('status' => 1, 'data' => $return));  
			exit;

		}catch(Exception $e){
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
		
	}

	public function homepageAction()
	{    
		try {
			$type = $this->getRequest()->getPost('type');
			$user_id = $this->getRequest()->getPost('user_id');
			$push_id = $this->getRequest()->getPost('push_id');
			$device_type = $this->getRequest()->getPost('device_type');

			//$tmp = Mage::getModel('customapiv6/catalog')->getHomePageMainProducts(1,15,$user_id);
			$tmp['banners'] = Mage::getModel('customapiv6/catalog')->getHomePageBanners();			
			//$tmp['categories'] = Mage::getModel('customapiv6/catalog')->getHomePageShopByCategory();
			if (is_array($tmp)) {
				$result['data'] = $tmp;
			} else {
				$result['message'] = $tmp;
			}
			$result['status'] = 1;
		} catch (Exception $e) {
			$result['status'] = 0;
			$result['error'] = $e->getCode();
			$result['message'] = $e->getMessage();
		}
		// update push id within notification table if user_id is passed
		if(!empty($user_id)) {
			$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($device_type, $push_id, $user_id);
		}

		$this->getResponse()->setHeader('Content-type', 'application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	public function getProductAction()
	{
		$this->getResponse()->setHeader('Content-type', 'application/json', true);
		$product_id = $_REQUEST['product_id'];
		$user_id = $_REQUEST['user_id'];
		//$product_id = 790; // test value later delete it
		$product = Mage::getModel('catalog/product')->load($product_id);
		$sku = $product->getSku();

		if(empty($sku)) {
			$result = array('status' => 0, 'message' => 'Invalid product');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		} else {
			$result = Mage::getModel('customapiv6/catalog')->getProductDetails($product,$user_id);

			//set product as viewed 17th Jan 2018 Mahesh Gurav
			// $view_result = Mage::getModel('customapiv6/customer')->setProductViewed($product_id,$user_id);

			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}

	}

	/* 
	* @author : Sonali Kosrabe 
	* @purpose to get approx delivery of product
	* @date : 28-10-2017
	*/

	public function getPincodeCheckerAction()
	{
		$this->getResponse()->setHeader('Content-type', 'application/json', true);
		$pincode = $_REQUEST['pincode'];
		if(empty($pincode)) {
			$result = array('status' => 0, 'message' => 'No Pincode Entered');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}elseif($pincode){
		 	$response = Mage::getModel('operations/serviceablepincodes')->getProductDeliveryOnDetailsPage($pincode);
		 	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}else{
			$response['status'] = 0;
			$response['message'] = $this->__('Delivery not available in your location yet.');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
	}

	/*
	* @param userid
	  * get the menu listing
	  */
	  public function getmenuitemsAction() 
	  {

	    echo '{  
		   "menu_item":[  
		      	{  
		         "id":"4",
		         "is_ibm":"0",
		         "name":"Mobiles",
		         "url":"http://www.electronicsbazaar.com/mobiles/",
		         "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/mobile-icon.png",
		         "sub_cat":[  
		            {  
		               "id":"14",
		               "is_ibm":"0",
		               "name":"Brand New Mobiles",
		               "url":"http://www.electronicsbazaar.com/mobiles/brand-new-mobiles/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            },{  
		               "id":"94",
		               "is_ibm":"0",
		               "name":"Open Box Mobiles",
		               "url":"http://www.electronicsbazaar.com/mobiles/open-box-mobiles/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            }
		         ]
		      },
		      	{  
		         "id":"3",
		         "is_ibm":"0",
		         "name":"Laptops",
		         "url":"http://www.electronicsbazaar.com/laptops/",
		         "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/laptop_icon.png",
		         "sub_cat":[  
		            {  
		               "id":"76",
		               "is_ibm":"0",
		               "name":"Brand New Laptops",
		               "url":"http://www.electronicsbazaar.com/laptops/brand-new-laptops/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            },
		            {  
		               "id":"60",
		               "is_ibm":"0",
		               "name":"Open Box Laptops",
		               "url":"http://www.electronicsbazaar.com/laptops/open-box-laptops/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            },
		            {  
		               "id":"11",
		               "is_ibm":"0",
		               "name":"Pre-Owned Laptops",
		               "url":"http://www.electronicsbazaar.com/laptops/pre-owned-laptops/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            },
		            {  
		               "id":"99",
		               "is_ibm":"0",
		               "name":"Brand Refurbished Laptops",
		               "url":"http://www.electronicsbazaar.com/laptops/brand-refurbished-laptops/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            },
		            {  
		               "id":"148",
		               "is_ibm":"0",
		               "name":"Pre-Owned Laptops Grade B",
		               "url":"http://www.electronicsbazaar.com/laptops/pre-owned-laptops-grade-b/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            }
		         ]
		      },
		      {  
		         "id":"113",
		         "is_ibm":"0",
		         "name":"Desktops",
		         "url":"http://www.electronicsbazaar.com/desktops/",
		         "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/laptop_icon.png",
		         "sub_cat":[  
		            {  
		               "id":"115",
		               "is_ibm":"0",
		               "name":"Brand New Desktops",
		               "url":"http://www.electronicsbazaar.com/desktops/brand-new-desktops/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            },{  
		               "id":"147",
		               "is_ibm":"0",
		               "name":"Preowned Desktops",
		               "url":"http://www.electronicsbazaar.com/desktops/preowned-desktops/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/"
		            },{  
		               "id":"117",
		               "name":"Brand Refurbished Desktops",
		               "url":"http://www.electronicsbazaar.com/desktops/brand-refurbished-desktops/",
		               "icon":"http://cdn.electronicsbazaar.com/media/catalog/category/",
		               "is_ibm":"0"
		            }]

		        }, {  
	         	"id":"147",
	         	"name":"EPP Program",
	         	"url":"http://www.electronicsbazaar.com/epp-program/",
	         	"icon":"http://cdn.electronicsbazaar.com/media/catalog/category/laptop_icon.png",
	         	"is_ibm":"1"
		        } 

		   ],
		   "static_links":[  
		      {  
		         "id":"hotdeals",
		         "name":"Deals"
		      },
		      {  
		         "id":"bestsellers",
		         "name":"Best Sellers"
		      },
		      {  
		         "id":"featuredproducts",
		         "name":"Featured Products"
		      }
		   ]
		}';

		exit;

	  }  

	/*
	* @param userid
  * get the menu listing
  */
  public function getMenuItemsAction_backup_for_flat()
  {
  	/*$customerId = $_REQUEST['userid'];*/
  	$rootcatId= Mage::app()->getStore()->getRootCategoryId();
  	//$categories = Mage::getModel('catalog/category')->getCategories($rootcatId);

	$_category = Mage::getModel('catalog/category')->load($rootcatId);    
    $_categories = $_category
                    ->getCollection()  
                    ->addAttributeToFilter('position', array('neq' => 19))
                    ->addAttributeToSelect(array('name', 'image', 'description'))
                    ->addIdFilter($_category->getChildren());  
	

    $result["menu_item"] = Mage::getModel('customapiv6/catalog')->getCategoriesRecursiveCall($_categories);

    $total_count = count($result["menu_item"]);

    $result["static_links"][0]["id"] = "hotdeals";
    $result["static_links"][0]["name"] = "Deals";
    $result["static_links"][1]["id"] = "bestsellers";
    $result["static_links"][1]["name"] = "Best Sellers";
    $result["static_links"][2]["id"] = "featuredproducts";
    $result["static_links"][2]["name"] = "Featured Products";
    /*$customerData = Mage::getModel('customer/customer')->load($customerId)->getData();
    $result["customer_info"] = $customerData;*/

    //categories interchange
    $result['menu_item'][1]['sub_cat'] = $result['menu_item'][0]['sub_cat'];

    $this->getResponse()->setHeader('Content-type', 'application/json', true);
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

  }

  /*
  * @desc : get the shop by category list with image
  */
  public function homepageShopByCategoriesAction()
  {
  	$result = Mage::getModel('customapiv6/catalog')->getHomePageShopByCategory();
    $this->getResponse()->setHeader('Content-type', 'application/json', true);
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  /*
  * @desc : add review to system
  */
	public function addProductReviewAction()
	{
		$userid = $_REQUEST['userid'];
		$product_id = $_REQUEST['product_id'];
		$title = $_REQUEST['title'];
		$detail = $_REQUEST['detail'];
		$nick_name = $_REQUEST['nick_name'];
		if(empty($nick_name)) {
			$nick_name = $userid;
		}
		$rating_value = $_REQUEST['rating_value'] + 15;
		$store = Mage::app()->getStore()->getId();

		try {

			$_review = Mage::getModel('review/review')
				->setEntityPkValue($product_id)
				->setStatusId(2)
				->setTitle($title)
				->setDetail($detail)
				->setEntityId(1)
				->setStoreId($store)
				->setStores(array($store))
				->setCustomerId($userid)
				->setCreatedAt(date('Y-m-d h:i:s'))
				->setNickname($nick_name)
				->save();

				if($rating_value != 0) {
					Mage::getModel('rating/rating')
		                       ->setRatingId(4)
		                       ->setReviewId($_review->getId())
		                       ->addOptionVote(19, $product_id);
				}
				$_review->aggregate();

			echo json_encode(array('status' => 1, 'message' => 'Review is added successfully'));
		} catch (Exception $e) {
			echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
		}
	}

	//this function is discontinued from this controller
	//copied and active in controller home for ios delay issue 
	public function dealsproductscollectionAction()
	{
		
		//$return['open_box_mobile'][0] = '';
		//$return['seal_packed_mobile'][0] = '';
		// if($_REQUEST['tt'] == 12){
		// 	echo "Deb <br>";
		// }

		$openBox = Mage::helper('neo_sidebanner')->getopenBoxCollection();
		foreach ($openBox as $key => $value) {
			if($value > 0)
			{
				$productId['open_box_mobile'][] = $value;
			}
		}

		$sealPack = Mage::helper('neo_sidebanner')->getsealPackCollection();
		foreach ($sealPack as $key => $value) {
			if($value > 0)
			{
				$productId['seal_packed_mobile'][] = $value;
			}
		}

		$preOwnedMob = Mage::helper('neo_sidebanner')->getPreOwned();
		foreach ($preOwnedMob as $key => $value) {
			if($value > 0)
			{
				$productId['pre_owned_mobile'][] = $value;
			}
		}

		$preOwnedLap = Mage::helper('neo_sidebanner')->getPreOwnedLap();
		foreach ($preOwnedLap as $key => $value) {
			if($value > 0)
			{
				$productId['pre_owned_laptops'][] = $value;
			}
		}

		$Accessories = Mage::helper('neo_sidebanner')->getAccessoriesCollection();
		foreach ($Accessories as $key => $value) {
			if($value > 0)
			{
				$productId['accessories'][] = $value;
			}
		}		

		$return = array();
   
		try{
			$return['big_bang_sales_image'] = Mage::helper('neo_sidebanner')->getDealsHeaderImage();

			$productModel = Mage::getModel('catalog/product');
			$i = 0; $k = 0; $l = 0; $n = 0;$m = 0;
			foreach ($productId as $key => $value) {  
				foreach ($value as $id) {
					if($key == 'open_box_mobile'){
						$productdata = $productModel->load($id);
						if($productdata->isSaleable()){
							// $price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getFinalPrice()));
							$price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice()));
							$special_price = $productdata->getSpecialPrice();
							if($special_price){
								$price = (int) round(Mage::helper('tax')->getPrice($productdata, $special_price));
							}

							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($productdata, 'image'); 
							$return[$key][$i]['name'] = $productdata->getName();
							$return[$key][$i]['product_id'] = $id;
							$return[$key][$i]['price'] = $price;
							$return[$key][$i]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$i]['product_url'] = $productdata->getProductUrl();
							$return[$key][$i]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($productdata);
							$i++;
						}
					}elseif($key == 'seal_packed_mobile'){
						$productdata = $productModel->load($id);
						// $price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getFinalPrice()));
						$price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice()));
						$special_price = $productdata->getSpecialPrice();
						if($special_price){
							$price = 0;
							$price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getSpecialPrice()));
						}


						if($productdata->isSaleable()){
							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($productdata, 'image');
							$return[$key][$k]['name'] = $productdata->getName();
							$return[$key][$k]['product_id'] = $id;
							$return[$key][$k]['price'] = $price;
							$return[$key][$k]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$k]['product_url'] = $productdata->getProductUrl();
							$return[$key][$k]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($productdata);
							$k++; 
						}
					}elseif($key == 'pre_owned_mobile'){

						$productdata = $productModel->load($id);
						// $price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getFinalPrice()));
						$price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice()));
						// $price = Mage::helper('tax')->getPrice($productdata, $productdata->getPrice(), true, null,null,null,null,null,true,false,true);
						$special_price = $productdata->getSpecialPrice();
						if($special_price){
							$price = (int) round(Mage::helper('tax')->getPrice($productdata, $special_price));
						}
						// if($_REQUEST['tt'] == 12){
						// 	echo $id.' -- '.$productdata->getPrice().' ---  '.$price.' '.$productdata->getName().'<br/>';
						// }
						if($productdata->isSaleable()){
							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($productdata, 'image');
							$return[$key][$l]['name'] = $productdata->getName();
							$return[$key][$l]['product_id'] = $id;
							$return[$key][$l]['price'] = $price;
							$return[$key][$l]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$l]['product_url'] = $productdata->getProductUrl();
							$return[$key][$l]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($productdata);
							$l++; 
						}

					}elseif($key == 'pre_owned_laptops'){

						$productdata = $productModel->load($id);
						// $price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getFinalPrice()));
						$price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice()));
						$special_price = $productdata->getSpecialPrice();
						if($special_price){
							$price = (int) round(Mage::helper('tax')->getPrice($productdata, $special_price));
						}

						if($productdata->isSaleable()){
							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($productdata, 'image');
							$return[$key][$n]['name'] = $productdata->getName();
							$return[$key][$n]['product_id'] = $id;
							$return[$key][$n]['price'] = $price;
							$return[$key][$n]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$n]['product_url'] = $productdata->getProductUrl();
							$return[$key][$n]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($productdata);
							$n++; 
						} 
  
					}elseif($key == 'accessories'){
						$_product = Mage::getModel('catalog/product')->load($id);
						// $productdata = $productModel->load($id);
						// $price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getFinalPrice()));
						// $price = (int) round(Mage::helper('tax')->getPrice($productdata, $productdata->getFinalPrice()));
						// $special_price = $productdata->getSpecialPrice();
						// if($special_price){
						// 	$price = (int) round(Mage::helper('tax')->getPrice($productdata, $special_price));
						// }
						$price_1 = (int) round(Mage::helper('tax')->getPrice($_product, $_product->getPrice()));
						if($_product->isSaleable()){
							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($_product, 'image');
							$return[$key][$m]['name'] = $_product->getName();
							$return[$key][$m]['product_id'] = $id;
							$return[$key][$m]['price'] = $price_1;
							// $return[$key][$m]['price1'] = $price_1;
							$return[$key][$m]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$m]['product_url'] = $_product->getProductUrl();
							$return[$key][$m]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($_product);
							$m++; 
						} 
  
					}
				}
			}

			echo json_encode(array('status' => 1, 'data' => $return));
			//echo "<pre>";  
			//print_r($return);
			exit;

		}catch(Exception $e){
			echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
			exit;
		}
	}

	public function savesupersaleAction()
	{
		try {
			$post_data['name'] = trim($_REQUEST['name']);
			$post_data['mobile'] = trim($_REQUEST['mobile']);

			if(empty($post_data['name']) || !preg_match("#^[a-z\s\.]+$#i",$post_data['name'])) {
				echo json_encode(array('status' => 0, 'message' => 'Please provide valid name.'));
				exit;
			}
			if(empty($post_data['mobile']) || !preg_match("/^[0-9]{5,10}$/",$post_data['mobile']) || strlen($post_data['mobile']) != 10) { 
				echo json_encode(array('status' => 0, 'message' => 'Please provide unique and valid Mobile.'));
				exit;
			}

			$model = Mage::getModel("supersale/super")
				->addData($post_data)
				->save();

			echo json_encode(array('status' => 1, 'message' => 'Message is saved successfully'));

		} catch (Exception $e) {
			echo json_encode(array('status' => 0, 'message' => 'Your Number is already register'));
			//echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
		}
	}

	//new home page design changes
	public function getbrandsAction(){
		try{
		$attributeId = 142;
		$attributeCode = 'brands';
		
		$products = Mage::getResourceModel('catalog/product_collection')
        ->addAttributeToFilter($attributeCode, array('notnull' => true))
        ->addAttributeToFilter($attributeCode, array('neq' => ''))
        ->addAttributeToSelect($attributeCode);

        // get all distinct attribute values
		$usedAttributeValues = array_unique($products->getColumnValues($attributeCode));
		
 		// print_r($usedAttributeValues);
 			$product = Mage::getModel('catalog/product')
		     ->setStoreId(1);
		$brands = array();
 		foreach ($usedAttributeValues as $key => $value) {
		     $product->setData($attributeCode,$value); 
			$optionLabel = $product->getAttributeText($attributeCode);
			$brands[$key]['brand'] = $optionLabel;
			$brands[$key]['image'] = '';
			$brands[$key]['url'] = '';
 		}
 		$brands = array_values($brands);
 		$data = array();
 		$data['brands'] = $brands;

			echo json_encode(array('status' => 1, 'message' => 'List of all brands', 'data'=> $data));
		}catch(Exception $e){
			echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
		}
	}

	public function getFeaturedProductsAction(){
		
		try{
			$media_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
			$limit = Mage::getStoreConfig('featured_products/homepage/number_products');

			$collection = Mage::getModel('catalog/product')->getCollection();
			$collection->addAttributeToSelect(array('name', 'image', 'price'));
	        $collection->addAttributeToFilter('magebuzz_featured_product',array('eq'=>1));
	        //$collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
				//->addMinimalPrice()
				//->addFinalPrice()
				//->addTaxPercents();
			//Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
			//Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
			//$collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
			$collection->getSelect()->limit($limit);
			$products = $collection->getData();
			$data = array();
			if(!empty($products)){
				foreach ($products as $key => $product) {
					$_product = Mage::getModel('catalog/product')->load($product['entity_id']);
					$price = (int) Mage::helper('tax')->getPrice($_product, $_product->getPrice());
					$data[$key]['product_id'] = $product['entity_id'];
					$data[$key]['name'] = $product['name'];
					// $data[$key]['price'] = (int) $product['price'];
					$data[$key]['price'] = (int) $price;
					$data[$key]['image'] = $media_url.'catalog/product'.$product['image'];
					$data[$key]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($_product);
				}
				echo json_encode(array('status'=>1,'data'=>$data,'messtaage'=>'successfully generated featured products'));
			}else{
			echo json_encode(array('status'=>0,'data'=>'','message'=>'not found featured products'));
			}
			exit;
		}catch(Exception $e){
			echo json_encode(array('status'=>0,'data'=>'','message'=>$e->getMessage()));
			exit;
		}
	
	}


		public function getHotSellingAction(){
		
		try{
			$media_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
			$limit = 10;

			$collection = Mage::getModel('catalog/product')->getCollection();
			$collection->addAttributeToSelect(array('entity_id','sku','name', 'image', 'price','is_superdeal'));
	        $collection->addAttributeToFilter('is_superdeal',array('eq'=>1));
	        $collection->addAttributeToFilter('attribute_set_id','10');
	        //$collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
				//->addMinimalPrice()
				//->addFinalPrice()
				//->addTaxPercents();
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
			// $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
			$collection->getSelect()->limit($limit)->order('entity_id', 'DESC');
			$products = $collection->getData();
			// echo "<pre>";
			// print_r($products);
			// echo "</pre>";
			// exit;
			$data = array();
			$new = array();
			$openbox = array();
			$preowned = array();
			$o = 0;
			$p = 0;
			$q = 0;
			if(!empty($products)){
				foreach ($products as $key => $product) {
					$_product = Mage::getModel('catalog/product')->load($product['entity_id']);
					$_isinstock = Mage::getModel('customapiv6/catalog')->checkInStock($_product);

					if($_isinstock){
						$_img = 'instock';
					}
					// else{
					// 	$_img = 'outstock';
					// }
					if (strpos($product['sku'], 'Openbox') !== false || strpos($product['sku'], 'OPMBN') !== false) {
						if($o==2) continue;
						// $image_name = 'new_app_hotselling_'.$o.'.jpg';
						$image_name = 'app-v9-'.$product['entity_id'].'.png';
						$openbox[$o]['product_id'] = $product['entity_id'];
						$openbox[$o]['name'] = $product['name'];
						// $openbox[$o]['price'] = (int) $product['price'];
						$openbox[$o]['price'] = (int) round(Mage::helper('tax')->getPrice($_product, $_product->getFinalPrice()));
						$openbox[$o]['bg_image'] = $media_url.'homepage/'.$image_name;
						$openbox[$o]['image'] = $media_url.'catalog/product'.$product['image'];;
						$openbox[$o]['is_instock'] = $_isinstock;
						$o++;
					}

					if (strpos($product['sku'], 'REF/') !== false) {
						if($p==2) continue;
						$k = $p+2;
						// $image_name = 'new_app_hotselling_'.$k.'.jpg';
						$image_name = 'app-v1-'.$product['entity_id'].'.jpg';
						$preowned[$p]['product_id'] = $product['entity_id'];
						$preowned[$p]['name'] = $product['name'];
						// $preowned[$p]['price'] = (int) $product['price'];
						$preowned[$p]['price'] = (int) round(Mage::helper('tax')->getPrice($_product, $_product->getFinalPrice()));
						$preowned[$p]['bg_image'] = $media_url.'homepage/'.$image_name;
						$preowned[$p]['image'] = $media_url.'catalog/product'.$product['image'];
						$preowned[$p]['is_instock'] = $_isinstock;
						$p++;
					}

					if (strpos(strtolower($product['sku']), 'newmob') !== false) {
						if($q==2) continue;
						// $q = $q+2;
						// $image_name = $_img.'_app_hotselling_'.$k.'.png';
						$image_name = 'app-v14-'.$product['entity_id'].'.png';
						$new[$q]['product_id'] = $product['entity_id'];
						$new[$q]['name'] = $product['name'];
						// $new[$q]['price'] = (int) $product['price'];
						$new[$q]['price'] = (int) round(Mage::helper('tax')->getPrice($_product, $_product->getFinalPrice()));
						$new[$q]['bg_image'] = $media_url.'homepage/'.$image_name;
						$new[$q]['image'] = $media_url.'catalog/product'.$product['image'];
						$new[$q]['is_instock'] = $_isinstock;
						$q++;
					}

				}
			
			if(!empty($new)){
				$data['new'] = $new;
			}
			
			if(!empty($openbox)){
				$data['openbox'] = $openbox;
			}
			
			if(!empty($preowned)){
				$data['preowned'] = $preowned;
			}
				
				echo json_encode(array('status'=>1,'data'=>$data,'message'=>'successfully generated hotselling products'));
			}else{
			echo json_encode(array('status'=>0,'data'=>'','message'=>'not found hotselling products'));
			}
			exit;
		}catch(Exception $e){
			echo json_encode(array('status'=>0,'data'=>'','message'=>$e->getMessage()));
			exit;
		}
	
	}

	//gadget search
	public function searchGadgetsAction()
	{
		$customer_id = $_REQUEST['user_id'];
		$keyword = $_REQUEST['keyword'];

		//mage::log($keyword,null,'search.log'); 
		$page_size = $this->getRequest()->getParam('pagesize');
		$page_no = $this->getRequest()->getParam('pageno');

		//$this->validateCustomer($customer_id);
		try {
			$data = Mage::getModel('customapiv6/catalog')->getSearchGadgetList($keyword, $customer_id, $page_no, $page_size);


			echo json_encode(array('status' => 1, 'data' => $data)); 
			exit;
		} catch(Exception $ex) {
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}

} 
 
