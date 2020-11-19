<?php
class Neo_Customapiv4_CatalogController extends Neo_Customapiv4_Controller_HttpAuthController
{
	public function searchProductsAction()
	{
		$customer_id = $_REQUEST['user_id'];
		$keyword = $_REQUEST['keyword'];

		$page_size = $this->getRequest()->getParam('page_size');
		$page_no = $this->getRequest()->getParam('page_no');

		//$this->validateCustomer($customer_id);
		try {
			$data = Mage::getModel('customapiv4/catalog')->getSearchProductList($keyword, $customer_id, $page_no, $page_size);
			echo json_encode(array('status' => 0, 'data' => $data));
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
			$user_id = $this->getRequest()->getParam('user_id');
			//$filter = '{"price":["100000-120000","150000-"]}';
			//$filter = '{"brands":[7,4],"laptop_processor":[1578,1397]}';
			//$filter = '{"laptop_processor":[1578,1397]}';
			$result = array();
			$tmp = Mage::getModel('customapiv4/catalog')->getProductCollection($category_id,$sortby,$page_no,$page_size,$user_id);
			$tmp['status'] = 1;
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
					$tmp = Mage::getModel('customapiv4/catalog')->homepageViewAllCollection($type,$page_no,$page_size,$user_id);
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

	public function homepageAction()
	{
		try {
			$type = $this->getRequest()->getPost('type');
			$user_id = $this->getRequest()->getPost('user_id');
			$push_id = $this->getRequest()->getPost('push_id');
			$device_type = $this->getRequest()->getPost('device_type');

			$tmp = Mage::getModel('customapiv4/catalog')->getHomePageMainProducts(1,15,$user_id);
			$tmp['banners'] = Mage::getModel('customapiv4/catalog')->getHomePageBanners();
			$tmp['categories'] = Mage::getModel('customapiv4/catalog')->getHomePageShopByCategory();
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
			$saved_flag = Mage::getModel('customapiv4/customer')->addPushIdAndDeviceType($device_type, $push_id, $user_id);
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
			$result = Mage::getModel('customapiv4/catalog')->getProductDetails($product,$user_id);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}

	}

	public function getPincodeCheckerAction()
	{
		$this->getResponse()->setHeader('Content-type', 'application/json', true);
		$pincode = $_REQUEST['pincode'];
		if(empty($pincode)) {
			$result = array('status' => 0, 'message' => 'No Pincode Entered');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		} else {
			$result = Mage::getModel('customapiv4/catalog')->getPincodeChecker($pincode);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}
	/*
	* @param userid
  * get the menu listing
  */
  public function getMenuItemsAction()
  {
  	/*$customerId = $_REQUEST['userid'];*/
  	$rootcatId= Mage::app()->getStore()->getRootCategoryId();
		$categories = Mage::getModel('catalog/category')->getCategories($rootcatId);
		//echo  get_categories($categories);
    $result["menu_item"] = Mage::getModel('customapiv4/catalog')->getCategoriesRecursiveCall($categories);
    $total_count = count($result["menu_item"]);

    $result["static_links"][0]["id"] = "hotdeals";
    $result["static_links"][0]["name"] = "Deals";
    $result["static_links"][1]["id"] = "bestsellers";
    $result["static_links"][1]["name"] = "Best Sellers";
    $result["static_links"][2]["id"] = "featuredproducts";
    $result["static_links"][2]["name"] = "Featured Products";
    /*$customerData = Mage::getModel('customer/customer')->load($customerId)->getData();
    $result["customer_info"] = $customerData;*/
    $this->getResponse()->setHeader('Content-type', 'application/json', true);
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

  }

  /*
  * @desc : get the shop by category list with image
  */
  public function homepageShopByCategoriesAction()
  {
  	$result = Mage::getModel('customapiv4/catalog')->getHomePageShopByCategory();
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
}
