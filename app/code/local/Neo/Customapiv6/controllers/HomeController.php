<?php 
// new homepage design 
// web serverice 02 june 2017
// mahesh gurav
?>
<?php
class Neo_Customapiv6_HomeController extends Neo_Customapiv6_Controller_HttpAuthController{

	

	public function homepageAction()
	{    

		try {
			$type = $this->getRequest()->getPost('type');
			$user_id = $this->getRequest()->getPost('user_id');
			$push_id = $this->getRequest()->getPost('push_id');
			$device_type = $this->getRequest()->getPost('device_type');

			// $tmp = Mage::getModel('customapiv6/catalog')->getHomePageMainProducts(1,15,$user_id);
			$tmp['banners'] = Mage::getModel('customapiv6/catalog')->getHomePageBanners();			
			//$tmp['categories'] = Mage::getModel('customapiv6/catalog')->getHomePageShopByCategory();
			if (is_array($tmp)) {
				$result['data'] = $tmp;
			} else {
				$result['message'] = $tmp;
			}
			$result['status'] = 1;
			$result['time'] = 5;
			
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

	public function brandsAction(){
		
		try{
			$media_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
			// $dataList = array('Apple','asus','BlackBerry','Dell','HP','HTC','Nokia','Toshiba','Samsung');
			$dataList = array('Motorola','Lenovo','Apple','Asus','Dell','HP','HTC','Samsung');
			foreach ($dataList as $key => $brand) {
				$brands[$key]['name'] = $brand;
				$brands[$key]['img_url'] = $media_url.'wysiwyg/brand_thumbnail/'.strtolower($brand).'.png';
				$brands[$key]['banner_img'] = $media_url.'wysiwyg/brand_thumbnail/'.strtoupper($brand).'-BANNER.png';
			}
			$data['brands'] = $brands;
			echo json_encode(array('status' => 1, 'message' => 'List of all brands', 'data'=> $data));
		}catch(Exception $e){
			echo json_encode(array('status' => 0, 'message' => $e->getMessage(), 'data'=> $data));

		}
	}

	public function homepagebannernewAction()
	{
		$return = array();
		try{
			
			$media_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

			$return[0]['keyword']       = 'albright';
			$return[0]['category_id']   = '7'; 
			$return[0]['category_name'] = 'Accessories';
			$return[0]['banner_type']   = 'product';
			$return[0]['event_click_label']   = 'Rockman-S';
			$return[0]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/albright/rockman-s.jpg';

			$return[1]['category_id']   = 60;
			$return[1]['banner_type']   = 'category';
			$return[1]['category_name']   = 'Open Box';
			$return[1]['event_click_label']   = 'open-box-laptop';
			$return[1]['banner_image']  = $media_url.'homepage/open-box-laptop.png';

			$return[2]['category_id']   = 94; 
			$return[2]['banner_type']   = 'category';
			$return[2]['category_name']   = 'Open Box';
			$return[2]['event_click_label']   = 'open-box-mobile';
			$return[2]['banner_image']  = $media_url.'homepage/open-box-mobile.png';

			$return[3]['banner_type']   = 'product';
			$return[3]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/samsung-banner.jpg';
			$return[3]['category_url']  = 'dealsproductscollection';
    
			// $return[4]['banner_type']   = 'register'; 
			$return[4]['banner_type']   = 'register1'; 
			$return[4]['category_url']  = 'login';
			// $return[4]['banner_image']  = $media_url.'homepage/Register-now-new.png'; 
			$return[4]['banner_image']  = $media_url.'homepage/register.png'; 
	
			$return[5]['banner_type']   = 'home-popup1';
			$return[5]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/republic-day-new1.jpg';
			$return[5]['category_url']  = 'popup';   

  			$return[6]['category_id']   = 11; 
			$return[6]['banner_type']   = 'category';
			$return[6]['category_name']   = 'Pre Owned'; 
			$return[6]['event_click_label']   = 'pre-owned-laptop';
			$return[6]['banner_image']  = $media_url.'homepage/pre-owned-laptop.png';

			$return[7]['category_id']   		= 97; 
			$return[7]['banner_type']   		= 'category';
			$return[7]['category_name']         = 'Pre Owned';
			$return[7]['event_click_label']     = 'pre-owned-mobile';
			$return[7]['banner_image']  = $media_url.'homepage/pre-owned-mobile.png';

			$return[8]['banner_type']   = 'home-page';
			// $return[8]['banner_image']  = $media_url.'homepage/deals_app.png';
			$return[8]['banner_image']  = $media_url.'homepage/deal-of-the-day.png';
			$return[8]['text']  		= 'exciting-offers'; 
			$return[9]['banner_type']   = 'hourly-deal';
			$return[9]['text']  		= 'coming-soon'; 

			$return[10]['banner_type']   = 'partner-program1'; 
			$return[10]['text']  		= 'Partner Program';
			$return[10]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/customs/images/app060417/partner-program.png'; 
   			
   			$return[11]['banner_type']   = 'sell-your-gadget';
			$return[11]['banner_image']  = $media_url.'homepage/sell-your-gadget.jpg';
			$return[11]['text']  		 = 'Sell Your Gadget'; 
   
			$return[12]['banner_type']   = 'lenovo-program1';  //add 1 for not showing in app
			$return[12]['banner_image']  = 'http://electronicsbazaar.com/skin/frontend/rwd/electronics/gadget/images/lenovo_program.jpg';
			$return[12]['text']  		 = 'Lenovo Program'; 
			
			$return[13]['banner_type']   = 'warranty';
			$return[13]['banner_image']  = $media_url.'homepage/warranty-050118.png'; 
			$return[13]['event_click_label']     = 'warranty';     
			$return[13]['text']  		= 'Warranty'; 

			$return[14]['banner_type']   = 'eb_advantages';
			$return[14]['banner_image']  = $media_url.'homepage/eb-advantages.jpg';
			$return[14]['text']  		 = 'Eb Advantages';

			$return[15]['banner_type']   = 'recently_viewed';
			$return[15]['text']  		 = 'Recently Viewed';

			$return[16]['banner_type']   = 'preowned_guidelines';
			$return[16]['text']  		 = 'Preowned Guidelines'; 

			// preowned_guidelines_product
			// preowned_guidelines_mobile
			// preowned_guidelines_laptop

			$return[17]['banner_type']   = 'preowned_guidelines_product';
			$return[17]['banner_image']  = $media_url.'homepage/pre-owned-products-guidelines.jpg';
			$return[17]['text']  		 = 'Preowned Guidelines Product';

			$return[18]['banner_type']   = 'preowned_guidelines_mobile';
			$return[18]['banner_image']  = $media_url.'homepage/pre-owned-mobile-guidelines.jpg';
			$return[18]['text']  		 = 'Preowned Guidelines Mobile';

			$return[19]['banner_type']   = 'preowned_guidelines_laptop';
			$return[19]['banner_image']  = $media_url.'homepage/pre-owned-laptop-guidelines.jpg';
			$return[19]['text']  		 = 'Preowned Guidelines Laptop';

			$return[20]['banner_type']   = 'top_brands';
			$return[20]['text']  		 = 'Top Brands'; 
			
			$return[21]['banner_type']   = 'featured_products';
			$return[21]['text']  		 = 'Featured Products'; 
			
			$return[22]['banner_type']   = 'hot_selling1';
			$return[22]['text']  		 = 'Hot Selling';

			$return[23]['banner_type']   = 'gst';
			$return[23]['banner_image']  = $media_url.'homepage/app_gstin.png';
			$return[23]['text']  		 = 'GST';

			// $return[24]['banner_type'] = 'moto_lenovo';
			// $return[24]['category_id']  = 125;
			// $return[24]['category_name'] = 'Motorola & Lenovo';
			// $return[24]['banner_image']  = $media_url.'homepage/Authorised-seller.png'; 

			// $return[25]['banner_type'] = 'webview';
			// $return[25]['code']  = 'Diwali Dhamaka';
			// $return[25]['value']  = 'http://180.149.246.49/warranty-app/moto-diwali-dhamaka.html';
			// $return[25]['banner_image'] = $media_url.'homepage/diwali_dhamaka.jpg';



			echo json_encode(array('status' => 1, 'data' => $return));  
			exit;

		}catch(Exception $e){
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}//end of homepage controller

	public function dealsproductscollectionAction()
	{
		
		//$return['open_box_mobile'][0] = '';
		//$return['seal_packed_mobile'][0] = '';

		$openBox = Mage::helper('neo_sidebanner')->getOpenMobile();
		foreach ($openBox as $key => $value) {
			if($value > 0)
			{
				$productId['open_box_mobile'][] = $value;
			}
		}

		$sealPack = Mage::helper('neo_sidebanner')->getNewMobile();
		foreach ($sealPack as $key => $value) {
			if($value > 0)
			{
				$productId['seal_packed_mobile'][] = $value;
			}
		}

		$preOwnedMob = Mage::helper('neo_sidebanner')->getPreOwnedMobile();
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

		$Accessories = Mage::helper('neo_sidebanner')->getAccessories();
		foreach ($Accessories as $key => $value) {
			if($value > 0)
			{
				$productId['accessories'][] = $value;
			}
		}		

		$return = array();
   
		try{
			$media_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
			$return['big_bang_sales_image'] = $media_url.'dealpage/deal_title.png';
			$return['deals_banner_img'] = $media_url.'dealpage/deals-banner.png';
			$productModel = Mage::getModel('catalog/product');
			$i = 0; $k = 0; $l = 0; $n = 0;$m = 0;
			foreach ($productId as $key => $value) {  
				foreach ($value as $id) {
					if($key == 'open_box_mobile'){
						$productdata = $productModel->load($id);
						$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
						if($productdata->isSaleable()){
							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($productdata, 'image'); 
							$return[$key][$i]['name'] = $productdata->getName();
							$return[$key][$i]['new_launch'] = $productdata->getNewLaunch();
							$return[$key][$i]['product_id'] = $id;
							//$return[$key][$i]['price'] = $productdata->getPrice();
							$return[$key][$i]['price'] = round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice(), true));							
							$specialprice = '';
							$specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($productdata->getSpecialPrice() , $productdata->getTaxClassId()));
							$return[$key][$i]['special_price'] = ($specialprice > 0)?$specialprice:null;
							$return[$key][$i]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$i]['product_url'] = $productdata->getProductUrl();
							$return[$key][$i]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($productdata);
							if(!empty($_REQUEST['user_id'])){
								$return[$key][$i]['is_favourite'] = Mage::getModel('customapiv6/catalog')->checkIsInWishlist($_REQUEST['user_id'],$id);
							}else{
								$return[$key][$i]['is_favourite'] = false;
							}
							$return[$key][$i]['qty'] = (int)$stock->getQty();
							if($stock->getuse_config_min_sale_qty())
								$return[$key][$i]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinQty();
							else
								$return[$key][$i]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinSaleQty();

							
							$return[$key][$i]['max_qty'] = (int)$stock->getMaxSaleQty();
							$return[$key][$i]['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($productdata->getSku());

							$i++;
						}
					}elseif($key == 'seal_packed_mobile'){
						$productdata = $productModel->load($id);
						$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
						if($productdata->isSaleable()){
							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($productdata, 'image');
							$return[$key][$k]['name'] = $productdata->getName();
							$return[$key][$k]['new_launch'] = $productdata->getNewLaunch();
							$return[$key][$k]['product_id'] = $id;
							//$return[$key][$k]['price'] = $productdata->getPrice();
							$return[$key][$k]['price'] = round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice(), true));
							$specialprice = '';
							$specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($productdata->getSpecialPrice() , $productdata->getTaxClassId()));
							$return[$key][$k]['special_price'] = ($specialprice > 0)?$specialprice:null;
							$return[$key][$k]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$k]['product_url'] = $productdata->getProductUrl();
							$return[$key][$k]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($productdata);
							if(!empty($_REQUEST['user_id'])){
								$return[$key][$k]['is_favourite'] = Mage::getModel('customapiv6/catalog')->checkIsInWishlist($_REQUEST['user_id'],$id);
							}else{
								$return[$key][$k]['is_favourite'] = false;
							}
							$return[$key][$k]['qty'] = (int)$stock->getQty();
							if($stock->getuse_config_min_sale_qty())
								$return[$key][$k]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinQty();
							else
								$return[$key][$k]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinSaleQty();

							
							$return[$key][$k]['max_qty'] = (int)$stock->getMaxSaleQty();
							$return[$key][$k]['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($productdata->getSku());

							$k++; 
						}
					}elseif($key == 'pre_owned_mobile'){

						$productdata = $productModel->load($id);
						$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
						if($productdata->isSaleable()){
							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($productdata, 'image');
							
							$store = Mage::app()->getStore('default');
							$taxCalculation = Mage::getModel('tax/calculation');
							$request = $taxCalculation->getRateRequest(null, null, null, $store);
							$taxClassId = $productModel->getTaxClassId();
							$percent = $taxCalculation->getRate($request->setProductClassId($taxClassId));

							$return[$key][$l]['name'] = $productdata->getName();
							$return[$key][$l]['new_launch'] = $productdata->getNewLaunch();
							$return[$key][$l]['product_id'] = $id;
							//$return[$key][$l]['price'] = $productdata->getPrice();
							$return[$key][$l]['price'] = round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice(), true));							
							$specialprice = '';
							$specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($productdata->getSpecialPrice() , $productdata->getTaxClassId()));
							$return[$key][$l]['special_price'] = ($specialprice > 0)?$specialprice:null;
							$return[$key][$l]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$l]['product_url'] = $productdata->getProductUrl();
							$return[$key][$l]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($productdata);
							if(!empty($_REQUEST['user_id'])){
								$return[$key][$l]['is_favourite'] = Mage::getModel('customapiv6/catalog')->checkIsInWishlist($_REQUEST['user_id'],$id);
							}else{
								$return[$key][$l]['is_favourite'] = false;
							}
							$return[$key][$l]['qty'] = (int)$stock->getQty();
							if($stock->getuse_config_min_sale_qty())
								$return[$key][$l]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinQty();
							else
								$return[$key][$l]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinSaleQty();

							
							$return[$key][$l]['max_qty'] = (int)$stock->getMaxSaleQty();
							$return[$key][$l]['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($productdata->getSku());

							$l++; 
						}

					}elseif($key == 'pre_owned_laptops'){

						$productdata = $productModel->load($id);
						$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
						if($productdata->isSaleable()){
							$taxed_price = Mage::helper('tax')->getPrice($productdata, $productdata->getPrice(), true);
							//showing taxed price, helper is not working sometimes
							$_price = $productdata->getPrice();
							$store = Mage::app()->getStore('default');
							$taxCalculation = Mage::getModel('tax/calculation');
							$request = $taxCalculation->getRateRequest(null, null, null, $store);
							$taxClassId = $productModel->getTaxClassId();
							$percent = $taxCalculation->getRate($request->setProductClassId($taxClassId));
							$taxed_price = $_price + ($_price*$percent/100);

							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($productdata, 'image');
							$return[$key][$n]['name'] = $productdata->getName();
							$return[$key][$n]['new_launch'] = $productdata->getNewLaunch();
							$return[$key][$n]['product_id'] = $id;
							$return[$key][$n]['price'] = round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice(), true));							
							$specialprice = '';
							$specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($productdata->getSpecialPrice() , $productdata->getTaxClassId()));
							$return[$key][$n]['special_price'] = ($specialprice > 0)?$specialprice:null;
							$return[$key][$n]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$n]['product_url'] = $productdata->getProductUrl();
							$return[$key][$n]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($productdata);
							if(!empty($_REQUEST['user_id'])){
								$return[$key][$n]['is_favourite'] = Mage::getModel('customapiv6/catalog')->checkIsInWishlist($_REQUEST['user_id'],$id);
							}else{
								$return[$key][$n]['is_favourite'] = false;
							}
							$return[$key][$n]['qty'] = (int)$stock->getQty();
							if($stock->getuse_config_min_sale_qty())
								$return[$key][$n]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinQty();
							else
								$return[$key][$n]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinSaleQty();

							
							$return[$key][$n]['max_qty'] = (int)$stock->getMaxSaleQty();
							$return[$key][$n]['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($productdata->getSku());
							$n++; 
						} 
  
					}elseif($key == 'accessories'){
						$_product = Mage::getModel('catalog/product')->load($id);
						$productdata = $productModel->load($id);
						$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
						if($_product->isSaleable()){
							//showing taxed price, helper is not working sometimes
							$_price = $_product->getPrice();
							$store = Mage::app()->getStore('default');
							$taxCalculation = Mage::getModel('tax/calculation');
							$request = $taxCalculation->getRateRequest(null, null, null, $store);
							$taxClassId = $productModel->getTaxClassId();
							$percent = $taxCalculation->getRate($request->setProductClassId($taxClassId));
							$taxed_price = $_price + ($_price*$percent/100);
							
							$imageCacheUrl = (string) Mage::helper('catalog/image')->init($_product, 'image');
							$return[$key][$m]['name'] = $_product->getName();
							$return[$key][$m]['new_launch'] = $productdata->getNewLaunch();
							$return[$key][$m]['product_id'] = $id;
							//$return[$key][$m]['price'] = $productdata->getPrice();
							$price_1 = (int) round(Mage::helper('tax')->getPrice($_product, $_product->getPrice()));
							$return[$key][$m]['price'] = $price_1;
							// $return[$key][$m]['price'] = round(Mage::helper('tax')->getPrice($productdata, $productdata->getPrice(), true));
							// $return[$key][$m]['price'] = round(Mage::helper('tax')->getPrice($productdata, $productdata->getFinalPrice(), true));							
							$specialprice = '';
							$specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($_product->getSpecialPrice() , $_product->getTaxClassId()));
							$return[$key][$m]['special_price'] = ($specialprice > 0)?$specialprice:null;
							$return[$key][$m]['product_image'] = (string) $imageCacheUrl;
							$return[$key][$m]['product_url'] = $_product->getProductUrl();
							$return[$key][$m]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($_product);
							if(!empty($_REQUEST['user_id'])){
								$return[$key][$m]['is_favourite'] = Mage::getModel('customapiv6/catalog')->checkIsInWishlist($_REQUEST['user_id'],$id);
							}else{
								$return[$key][$m]['is_favourite'] = false;
							}
							$return[$key][$m]['qty'] = (int)$stock->getQty();
							if($stock->getuse_config_min_sale_qty())
								$return[$key][$m]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinQty();
							else
								$return[$key][$m]['min_qty'] = $stock->getMinQty() == 0 ? 1 : (int)$stock->getMinSaleQty();

							
							$return[$key][$m]['max_qty'] = (int)$stock->getMaxSaleQty();
							$return[$key][$m]['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($productdata->getSku());
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


	//maintenance flag added by Mahesh Gurav on 21 Sept 2017
	public function flagAction(){

		$ip = $_SERVER['REMOTE_ADDR'];
		// $allowed = array('113.193.83.252');
		//27.106.105.214
		// $allowed = array('27.106.105.218','49.248.210.138','111.119.217.101');
		// $allowed = array('27.106.105.214','206.183.111.25');
		$allowed = array('27.106.105.203'); //KKOC

		//for app
		$maintenanceFile = 'maintenance.flag';
		$array_status = array();
		$array_status['status'] = 1;
		if(file_exists($maintenanceFile) && !in_array($ip, $allowed) ){
		    $array_status['status'] = 0;
		    $array_status['message'] = "This APP is under maintenance mode.";
		    $array_status['url'] = "https://www.electronicsbazaar.com/website_under_construction.png";
		    echo json_encode($array_status);
		    exit;
		}

		if(!empty($_REQUEST['push_id']) && !empty($_REQUEST['device_type'])){
			$device_type = strtolower($_REQUEST['device_type']);
			$push_id = $_REQUEST['push_id'];
			if(!empty($_REQUEST['version'])){
				$version = $_REQUEST['version'];
				$device_fcm = Mage::getModel('customapiv6/customer')->addFcmIdVersion($device_type, $push_id, 0, $version);
			}else{
				$device = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($device_type, $push_id, 0);
			}
		}

	    echo json_encode($array_status);
	    exit;
	}


	public function bestsellerAction()
	{ 
		try{

			$storeId = (int) Mage::app()->getStore()->getId();
	        $toDate = date("Y-m-d");
	        $fromDate = date('Y-m-d', strtotime('-1 month'));

	        $collection = Mage::getResourceModel('catalog/product_collection')
	            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
	            ->addStoreFilter()
	            ->addPriceData()
	            ->addTaxPercents()
	            ->addUrlRewrite()
	            ->setPageSize(10);
	 
	        $collection->getSelect()
	            ->joinLeft(
	                array('aggregation' => $collection->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
	                "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND aggregation.period BETWEEN '{$fromDate}' AND '{$toDate}'",
	                array('SUM(aggregation.qty_ordered) AS sold_quantity')
	            )
	            ->group('e.entity_id')
	            ->order(array('sold_quantity DESC', 'e.created_at'));
	 
	        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
	        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

			//code for remove out of stock product.(Code by JP.)
			$collection->joinField('is_in_stock',
			'cataloginventory/stock_item',
			'is_in_stock',
			'product_id=entity_id',
			'is_in_stock=1',
			'{{table}}.stock_id=1',
			'left');	        
	 		
	 	    $product = Mage::getModel('catalog/product')->load(19775); //custom code by JP to show Vow product.
            $collection->addItem($product); 

	 		$data = array();
	 		if(!empty($collection) && count($collection) > 0){
	 			$ij = 0;
	 			$media_url = Mage::getBaseUrl('media');
		        foreach ($collection as $key => $product) {  
		            $specialprice = '';      	
		        	$_product = Mage::getModel('catalog/product')->load($product['entity_id']);
					$price = (int) Mage::helper('tax')->getPrice($_product, $_product->getPrice());
					$specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($_product->getSpecialPrice() , $_product->getTaxClassId()));
					$data[$ij]['product_id'] = $product['entity_id'];
					$data[$ij]['name'] = $product['name'];
					$data[$ij]['new_launch'] = $product['new_launch'];
					$data[$ij]['price'] = (int) round(Mage::helper('customapiv6')->getPriceWithTax($_product->getPrice() , $_product->getTaxClassId()));
					$data[$ij]['special_price'] = ($specialprice > 0)?$specialprice:null;
					$data[$ij]['image'] = $media_url.'catalog/product'.$product['image'];
					$data[$ij]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($_product);
					if(!empty($_REQUEST['user_id'])){
						$data[$ij]['is_favourite'] = Mage::getModel('customapiv6/catalog')->checkIsInWishlist($_REQUEST['user_id'],$product['entity_id']);
					}
					$data[$ij]['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($_product->getSku());
					$ij ++;
		        }
		        echo json_encode(array('status'=>1,'data'=>$data,'message'=>'successfully generated bestseller products'));
	 		}else{
	 			echo json_encode(array('status'=>0,'data'=>'','message'=>'not found featured products'));
	 		}
	 		exit;

	 	}catch(Exception $e){
	 		echo json_encode(array('status'=>0,'data'=>'','message'=>$e->getMessage()));
				exit;
	 	}
	}

	//revamp changes onn 15 Jan 2018 Mahesh Gurav
	public function getPaymentMethodsBannersAction(){
		// $methodsBanners = array('cod','credit-checkout','master','Paytm_logo','pay-u-bizz','visa');
		$methodsBanners = array('cod','credit-checkout','master','Paytm_logo','visa');
		$data = array();
		$media_url = Mage::getUrl('media/paymethods');
		foreach ($methodsBanners as $key => $value) {
			if(in_array($value, array('visa'))){
				$data[$key]['payment_img'] = $media_url.$value.'_v2.png';
			}else if(in_array($value, array('master'))){
				$data[$key]['payment_img'] = $media_url.$value.'_v3.png';
			}else{
				$data[$key]['payment_img'] = $media_url.$value.'.png';
			}
		}

		echo json_encode(array('status'=>1,'data'=>$data,'message'=>'successfully generated payment methods banners'));
	}

	public function getTopAccessoriesAction(){
		$collection = Mage::getModel('catalog/product')
                             ->getCollection()
                             ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                             ->addAttributeToSelect('*')
                             ->addAttributeToFilter('magebuzz_featured_product','1')
                             ->addAttributeToFilter('category_id', 7);
         
	    Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
	    Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        //code for remove out of stock product.(Code by JP.)
		$collection->joinField('is_in_stock',
		        'cataloginventory/stock_item',
		        'is_in_stock',
		        'product_id=entity_id',
		        'is_in_stock=1',
		        '{{table}}.stock_id=1',
		        'left');

	    $media_url = Mage::getBaseUrl('media');
	    $top_accessories = $collection->setPageSize(10);
	    if(count($top_accessories) > 0){

		    $ij = 0;
		    foreach ($top_accessories as $_product) {
		    	$specialprice = '';
		    	$price = (int) Mage::helper('tax')->getPrice($_product, $_product->getPrice());
		    	$specialprice = (int) round(Mage::helper('customapiv6')->getPriceWithTax($_product->getSpecialPrice() , $_product->getTaxClassId()));
				$data[$ij]['product_id'] = $_product->getEntityId();
				$data[$ij]['name'] = $_product->getName();
				$data[$ij]['new_launch'] = $_product->getNewLaunch();
				$data[$ij]['price'] = (int) round(Mage::helper('customapiv6')->getPriceWithTax($_product->getPrice() , $_product->getTaxClassId()));
				$data[$ij]['special_price'] = ($specialprice > 0)?$specialprice:null;
				$data[$ij]['image'] = $media_url.'catalog/product'.$_product->getSmallImage();
				$data[$ij]['is_instock'] = Mage::getModel('customapiv6/catalog')->checkInStock($_product);
				if(!empty($_REQUEST['user_id'])){
					$data[$ij]['is_favourite'] = Mage::getModel('customapiv6/catalog')->checkIsInWishlist($_REQUEST['user_id'],$_product->getEntityId());
				}else{
					$data[$ij]['is_favourite'] = false;
				}
				$data[$ij]['category_type_img'] = Mage::getModel('customapiv6/catalog')->getCustomImageLabel($_product->getSku());
				$ij++;
		    }

		    echo json_encode(array('status'=>1,'data'=>$data,'message'=>'successfully generated top accessories products'));
		    exit;
		}else{
			echo json_encode(array('status'=>0,'data'=>'','message'=>'not found top accessories products'));
		    exit;
		}
	}//end of top accessories

	//revamp changes onn 16 Jan 2018 Mahesh Gurav
	public function getServiceBannersAction(){
		$methodsBanners = array('bulk-order','eb-cert-preowned','eb-service-center','eb-warranty','free-shipping','eb-mar');
		$data = array();
		$media_url = Mage::getUrl('media/revamp');
		foreach ($methodsBanners as $key => $value) {
			$data[$key]['img_url'] = $media_url.$value.'-v5.png';
		}

		echo json_encode(array('status'=>1,'data'=>$data,'message'=>'successfully generated homepage banners'));
	}

	//promotional popup for vo mobile 26th Feb 2018 Mahesh Gurav
 	public function popupAction(){
		$data = array();
		$status = 0;
		$skin_url = Mage::getSingleton('core/design_package')->getSkinBaseUrl();
		$popup_image = $skin_url.'images/vow104.jpg';
		$product_id = 20440;
		$event_label = 'app_vow_popup_29March18';
		$duration = 5;

		$data['status'] = $status;
		$data['img_url'] = $popup_image;
		$data['product_id'] = $product_id;
		$data['event_click_label'] = $event_label;
		$data['duration'] = $duration;

		echo json_encode($data);
		exit;
	}
}?>
