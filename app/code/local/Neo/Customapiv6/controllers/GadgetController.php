<?php

class Neo_Customapiv6_GadgetController extends Neo_Customapiv6_Controller_HttpAuthController

{

	const XML_PATH_EMAIL_RECIPIENT  = 'gadget/gadget/recipient_email';

    const XML_PATH_EMAIL_SENDER     = 'gadget/gadget/sender_email_identity';

    const XML_PATH_EMAIL_TEMPLATE   = 'gadget/gadget/email_template';



	public function categoryAction(){



		$base_url = Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);

		$media_url = $base_url.'media/gadgets/';

		$laptop_image = $media_url.'sell_laptop.png';

		$mobile_image = $media_url.'sell_mobile.png';

		//$mobile_image = $media_url.'mobile.png';

		$other_image = $media_url.'other.png';



		$categories['sell_cats'] = array(

			0 => array('name'=>'Mobiles', 'code'=>'mobile', 'img_url'=>$mobile_image),

			1 =>array('name'=>'Laptops', 'code'=>'laptop', 'img_url'=>$laptop_image) 			

			//2 => array('name'=>'Mobiles', 'code'=>'other', 'img_url'=>$other_image)

			);



		//$json_data = json_encode($categories);

		echo json_encode(array('status' => 1, 'data' => $categories)); 

		//echo $json_data;

		exit;

	}



	public function brandsAction(){



		$base_url = Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);

		$media_url = $base_url.'media/gadgets/mobiles/';



		$apple = $media_url.'apple.png';

		$coolpad = $media_url.'coolpad.png';

		$htc = $media_url.'htc.png';

		$samsung = $media_url.'samsung.png';

		$one_plus = $media_url.'oneplus.png';

		$motorola = $media_url.'motorola.png';

		$sony = $media_url.'sony.png';

		$xiomi = $media_url.'xiomi.png';

		$other = $media_url.'others.png'; 

		

		$laptops = $base_url.'media/gadgets/laptops/';

		$acer = $laptops.'acer.png';

		$asus = $laptops.'asus.png';

		$dell = $laptops.'dell.png';

		$hp = $laptops.'hp.png';

		$others = $laptops.'others.png';

		$lenovo = $media_url.'lenovo.png';



		$code = $this->getRequest()->getParam('code');

		$json_brand = array();

		$laptops = array();

		$mobiles = array();



		if(!empty($code) && $code == 'mobile'){

			$mobiles['brands'] = array(

			0=>array('name'=>'Apple', 'code'=>'SYGMOBAPPLE', 'img_url'=>$apple),

			1=>array('name'=>'Samsung', 'code'=>'SYGMOBSAMSUNG', 'img_url'=>$samsung),

			);

			$json_brand = json_encode(array('status' => 1, 'data' => $mobiles));

		}else if($code == 'laptop'){



			$laptops['brands'] = array(

			0=>array('name'=>'Apple', 'code'=>'SYGLAPAPPLE', 'img_url'=>$apple),

			1=>array('name'=>'Dell', 'code'=>'SYGLAPDELL', 'img_url'=>$dell), 

			2=>array('name'=>'Lenovo', 'code'=>'SYGLAPLENOVO', 'imgg_url'=>$lenovo),

			3=>array('name'=>'HP', 'code'=>'SYGLAPHP', 'img_url'=>$hp),

			4=>array('name'=>'Other', 'code'=>'SYGLAPOTHER', 'img_url'=>$others)

			);



			$json_brand = json_encode(array('status' => 1, 'data' => $laptops));

		}

		echo $json_brand;

		exit;

	}



	public function productsAction(){

		$brand = $this->getRequest()->getParam('brand');

		$code = $this->getRequest()->getParam('code');

		$base_url = Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);



		$collection = Mage::helper('gadget')->getBrandedGadgetsCollection($brand,$code);

		$products = array();

		$prod_cnt = 0;

		foreach ($collection as $product) {

			$product_id = $product->getEntityId();



			//providing custom options

			$_product = Mage::getModel('catalog/product')->load($product_id);

			//$options_array = array();

			if($_product->getHasOptions() == 1){

				$options = '';

				$title = '';

		    		$options_array = array();

				foreach ($_product->getOptions() as $o) {

					//$title = str_replace(" ", "", $o->getTitle())."<br>";

					$title = trim($o->getTitle());

					$options = $o->getValues();

					$opt_cnt = 0;

		            foreach ($options as $k => $v) {

		            	$options_array[$title][$opt_cnt]['option_id'] = $v->getOptionId();

		                $options_array[$title][$opt_cnt]['option_type_id'] = $v->getOptionTypeId();

		                $options_array[$title][$opt_cnt]['option_name'] = $v->getTitle();

		                $option_price = (int) $v->getPrice();

		                if( $v->getPriceType() == 'percent'){

		                	$option_price = (int) $_product->getPrice()*$option_price/100;

		                }

		                $options_array[$title][$opt_cnt]['option_price'] =  $option_price;	

		                $opt_cnt++;

		            }

				}

			}



			$products['products'][$prod_cnt]['product_id'] = $product_id;

			$products['products'][$prod_cnt]['name'] = $product->getName();

			$products['products'][$prod_cnt]['price'] = (int) $product->getPrice();

			$products['products'][$prod_cnt]['image'] = $base_url.'media/catalog/product'.$product->getImage();

			$products['products'][$prod_cnt]['options'] = $options_array;

			//echo "<pre>"; print_r($products); echo "</pre>"; die();

			$prod_cnt++;

		}

		//echo "<pre>"; print_r($products); echo "<pre>";

		echo json_encode(array('status' => 1, 'data' => $products ));

	}



	public function mobileAction(){

		$base_url = Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);

		$product_id = $this->getRequest()->getParam('product_id');

		$sku = $this->getRequest()->getParam('code');

		//$option_id = $this->getRequest()->getParam('option_id');

		//$option_type_id = $this->getRequest()->getParam('option_type_id');

		// var_dump($sku); exit;



		$product = Mage::getModel('catalog/product');

		if(!empty($sku)){

			// $_product = Mage::getModel('catalog/product')->loadBySku($sku);

			// $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

			$_product = $product->load($product->getIdBySku($sku));

		}else if($product_id){

			$_product = Mage::getModel('catalog/product')->load($product_id);

		}



		if($_product){

			// $_product = Mage::getModel('catalog/product')->load($product_id);

			$collection = Mage::getModel('catalog/product')->getCollection();

			$collection->addAttributeToFilter('status', 1);

			$collection->addAttributeToSelect('*');

			$collection->addAttributeToSort('name', 'ASC');

			$collection->addAttributeToFilter('product_department',$_product->getProductDepartment());

			 if($_product->getProductDepartment() == 2731 ){

			    $collection->addAttributeToFilter('gadget',$_product->getGadget());

			 }else{

			    $collection->addAttributeToFilter('gadget_laptop',$_product->getGadgetLaptop());

			 }



			$new_price = (int) $_product->getPrice();



			if($_product->getHasOptions() == 1){

				$options = '';

				$title = '';

	    		$options_array = array();

				foreach ($_product->getOptions() as $o) {

					$title = str_replace(" ", "", $o->getTitle());

					

					if($title == 'Model'){

						$mod_cnt = 0;

						foreach ($collection as $pro){

							$options_array[$title][$mod_cnt]['option_id'] = '0000';

		                	$options_array[$title][$mod_cnt]['option_type_id'] = $pro->getId();

		                	$options_array[$title][$mod_cnt]['option_name'] = $pro->getName();

		                	$options_array[$title][$mod_cnt]['option_price'] = (int) $pro->getPrice();

							$mod_cnt++;

						}

					}else{

						$options = $o->getValues();

						$opt_cnt = 0;

			            foreach ($options as $k => $v) {

			            	//if($v->getOptionId() == $option_id && $v->getOptionTypeId() == $option_type_id){

			            		//$new_price = $new_price + (int) $v->getPrice();

			            		// echo "Skip Option_id and add price";

			            	//	break;

			            	//}else{

			            	// echo $v->getPriceType();



			                $options_array[$title][$opt_cnt]['option_id'] = $v->getOptionId();

			                $options_array[$title][$opt_cnt]['option_type_id'] = $v->getOptionTypeId();

			                $options_array[$title][$opt_cnt]['option_name'] = $v->getTitle();

			                	$option_price = (int) $v->getPrice();

			                if( $v->getPriceType() == 'percent'){

			                	$option_price = (int) $new_price*$option_price/100;

			                }

				                $options_array[$title][$opt_cnt]['option_price'] = (int) round($option_price);	

			                $opt_cnt++;

			            }

					}





				}



			}// end



			$products['products']['product_id'] = $_product->getId();

			$products['products']['name'] = $_product->getName();

			$products['products']['price'] = $new_price;

			$products['products']['image'] = $base_url.'media/catalog/product'.$_product->getImage();

			$products['products']['options'] = $options_array;



		}

		//echo "<pre>"; print_r($products); echo "</pre>";

		echo json_encode(array('status' => 1, 'data' => $products ));

	}

	

	public function saveAction(){

		

		$sample = $this->getRequest()->getParams();

		$options = array();

		if(!empty($sample['gadget_specs'])){

			$gadget_specs =  json_decode($sample['gadget_specs'],1);

			$gadget_specs = $gadget_specs[0];

			// print_r($gadget_specs);

			$product_id = $gadget_specs['brand_product_id'];

			$price = $gadget_specs['price'];

			$options['Brand'] = $gadget_specs['brand'];

			$data['brand'] = $gadget_specs['brand'];

			$product_description = '';

			$product_description .= $gadget_specs['brand'];

			if(!empty($gadget_specs['model'])){

				$options['Model'] = $gadget_specs['model'];

				$product_description .= ' '.$gadget_specs['model'];

			}

			

			if(!empty($gadget_specs['generation'])){

				$options['Generation'] = $gadget_specs['generation'];

				$product_description .= ' '.$gadget_specs['generation'];

			}



			if(!empty($gadget_specs['Processor'])){

				$options['Processor'] = $gadget_specs['Processor'];

				$product_description .= ' '.$gadget_specs['Processor'];

			}



			$options['Memory'] = $gadget_specs['memory'];

			if(!empty($gadget_specs['Condition'])){

				$options['Condition'] = $gadget_specs['Condition'];

			}

			

			$options['IMEI No'] = $gadget_specs['imei_no'];





		}



		

		$gadgetRequest = Mage::getModel('gadget/request');

		if($product_id){

			$_product = Mage::getModel('catalog/product')->load($product_id);

		}

		if($sample['customer_id']){

			$customer = Mage::getModel('customer/customer')->load($sample['customer_id']);

		}

		

		//create data array to add to table

		$data = array();

		

		if(!empty($price)){

			$data['price'] = $price;



			$price = number_format((float)$data['price'], 2, '.', '');      

            $formatPrice = number_format($price);        

		}

		

		if(strlen($product_description) > 0){

			$data['proname'] = $product_description;

		}



		if($_product){

			$data['sku'] = $_product->getSku();

			// $data['proname'] = $_product->getName();

			$data['brand'] = $_product->getAttributeText('brands');

			$data['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();

		}

		if($customer){

			$customer_name = $customer->getFirstname()." ".$customer->getLastname();

			$data['name'] = $customer_name;

			$data['email'] = $customer->getEmail();

			$data['mobile'] = $customer->getMobile();

		}

		

		if(!empty($sample['address_id'])){

			$address_id = $sample['address_id'];

			$_address = Mage::getModel('customer/address')->load($address_id);

			$data['address_id'] = $address_id;

		}

		

		if($_address){

			$data['pincode'] = $_address->getPostcode();

			$data['city'] = $_address->getCity();

			$data['address'] = implode(", ", $_address->getStreet()).", ".$_address->getCity().", ".$_address->getRegion().", ".$_address->getCountryId();

		}



		if(!empty($sample['street']) && !empty($sample['region_id']) && !empty($sample['postcode']) && !empty($sample['country_id']) && !empty($sample['city'])){

			$data['pincode'] = $sample['postcode'];

			$data['city'] = $sample['city'];

			// $street = implode(", ", $sample['street']).", ".implode(", ", $sample['street1']);

			$street = $sample['street'].", ".$sample['street1'];

			$data['address'] = $street.", ".$sample['city'].", ".$sample['region_id'].", ".$sample['country_id'];

			$regionCode =  $sample['region_id'];

			$countryCode = 'IN';

			$regionModel = Mage::getModel('directory/region')->loadByCode($regionCode, $countryCode);

			$regionId = $regionModel->getId();



			$address = Mage::getModel("customer/address");

			$address->setCustomerId($customer->getId())

			        ->setFirstname($customer->getFirstname())

			        ->setLastname($customer->getLastname())

			        ->setCountryId('IN')

					->setRegionId($regionId) //state/province, only needed if the country is USA

			        ->setPostcode($sample['postcode'])

			        ->setCity($sample['city'])

			        ->setTelephone($customer->getMobile())

			        ->setFax($customer->getMobile())

			        // ->setCompany('Inchoo')

			        ->setStreet($street)

			        ->setIsDefaultBilling('1')

			        ->setIsDefaultShipping('1')

			        ->setSaveInAddressBook('1');

			 

			try{

			    $address->save();

			    $data['address_id'] = $address->getId();

			}

			catch (Exception $e) {

			    // Zend_Debug::dump($e->getMessage());

			}

		}



		

		if(!empty($options)){

			

			$data['options'] = json_encode($gadget_specs,1);



			$html = '';

			foreach($options as $label =>$value){

	        	$resultoptionlabel =  $label;

	           	$resultoptionvalue =  $value;

	 			$html.='<tr><td valign="top" align="left" style="padding: 0 0 10px 0;">';

	        	$html.='<h2 style="color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:16px;margin:0;padding:0;margin:0 0 2px 0;">';

	        	$html.=$resultoptionlabel.'</h2>';

	        	$html.='<p style="margin:0;padding:0;font-size:13px;font-family:Arial,Helvetica,sans-serif;color:#ffffff">';

	        	$html.=$resultoptionvalue;

	            $html.='</p></td></tr>'; 

			} 



			$data['switch'] = $html;

			//save html for showing in admin side forms

			$data['option'] = $html;

		}



		$data['imei_no'] = $data['serial_number'] = $gadget_specs['imei_no'];



		if(!empty($sample['bank_details'])){

			$bank = json_decode($sample['bank_details'],1);

			$bank = $bank[0];

		}

		if(!empty($bank)){

			$data['bank_customer_name'] = $bank['customer_name'];

			$data['bank_name'] = $bank['bank_name'];

			$data['bank_ifsc'] = $bank['bank_ifsc'];

			$data['bank_account_no'] = $bank['bank_account_no'];

		}

		

		if(!empty($data)){

			//for user with promo code  provided by LENOVO CORPORATE

			$specs_array = json_decode($data['options'],1);

			if(!empty($specs_array['promo_code'])){

				$applied_promo_code = $specs_array['promo_code'];

				//static promo changes
				$data['used_promo_code'] = $applied_promo_code;


				$promo_collection = Mage::getModel('gadget/request')->getCollection()->addFieldToFilter('promo_code',array('eq'=>$applied_promo_code));



				if(count($promo_collection) > 0 ){

					foreach ($promo_collection as $promoModel) {

						# code...

						$corporate_offer_options = $promoModel->getOptions();

						$corp_options = json_decode($corporate_offer_options,1); 

				

						//validate processor or generation

						$basic_validation_fail = false;

						$promo_message = '';



						if($specs_array['brand'] !== $corp_options['brand']){

							//check brand

						$promo_message = 'Applied promo code is invalid for this brand';

							$basic_validation_fail = true;

						}else if($specs_array['brand'] == 'Apple' && !empty($specs_array['generation']) && $specs_array['generation'] !== $corp_options['generation']){

						$promo_message = 'Applied promo code is invalid for this generation';

							//check generation

							$basic_validation_fail = true;

						}else if($specs_array['processor'] !== $corp_options['processor']){

							//check processor

							$basic_validation_fail = true;

						$promo_message = 'Applied promo code is invalid for this processor';

						}

						

						if($basic_validation_fail){

							$message = 'Applied promo code is invalid for this request.';

							echo json_encode(array('status'=>0, 'data'=>array('message'=>$promo_message,'order_id'=>''))); exit();

						}



						$internal_order_id = $promoModel->getInternalOrderId();

						$is_active = $promoModel->getIsActive();

						if($is_active == '1'){

							$message = 'This promo code is already used';

							echo json_encode(array('status'=>0, 'data'=>array('message'=>$message,'order_id'=>''))); exit();

						}else{

							$data['used_promo_code'] = $applied_promo_code;

							$data['program'] = 'Lenovo Corporate';

						}

						break;

					}

				}

				//getting ticket id from promo code

				// $ticket_id = $this->get_string_between($applied_promo_code, 'EBLEN', 'CORP');



				// $promoModel = Mage::getModel('gadget/request')->load($ticket_id);



				// print_r($promoModel->getData());

				// $corporate_offer_options = $promoModel->getOptions();

				

			}

			

			try{

            //set legal term agree on 08th feb 2018 Mahesh Gurav on Sujay's request

            $data['agree'] = 'YES';



				$model = Mage::getModel("gadget/request")->addData($data)->save();

				$data['price'] = $formatPrice;   

				$data['order_id'] = $model->getId(); 

	            $postObject = new Varien_Object();

	            $postObject->setData($data); 



	            $emailId = $data['email'];

	            // $recipients = explode(",",$emailId);

	            $recipients[] = $data['email'];  

	            $support_team = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);     

	            $bccAdd[] = explode(",", $support_team);                   



	            // $filePath = 'home/electronicsbazaa/public_html/neweb/skin/frontend/rwd/electronics/customs/pdf/invoice.pdf';



	            $mailTemplate = Mage::getModel('core/email_template');  

	            /* @var $mailTemplate Mage_Core_Model_Email_Template */



	            // $mailTemplate->getMail()->createAttachment(

             //                      file_get_contents($filePath),

             //                      Zend_Mime::TYPE_OCTETSTREAM,

             //                      Zend_Mime::DISPOSITION_ATTACHMENT,

             //                      Zend_Mime::ENCODING_BASE64,

             //                      'invoice.pdf'

             //            );



	            $mailTemplate->setDesignConfig(array('area' => 'frontend'))

	            //->setReplyTo($data['email'])

	            ->addBcc($bccAdd) 

	            ->sendTransactional(

	                Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),

	                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER), 

	                $recipients,

	                null,

	                array('data' => $postObject) 

	            );

	            $order_id = $model->getId(); 
	            /**** Start code for send SYG retailer notification (code by jp. )****/			          
		        if($sample['customer_id'] != '' && $order_id != '') {
		        	$customerId = $sample['customer_id'];			            
		        	Mage::getModel('gadget/request')->sendSygRetailerNotification($customerId,$order_id);			
	            }			          
	            /**** End code for send SYG retailer notification (code by jp. )****/	            

	            $post['order_id'] = $order_id; 

	            $message = "Request is sent and your request id is ".$order_id;

	            $status = 1;

			}catch(Exception $e){

				$message = $e->getMessage();

				$status = 0;

				$order_id = 0;

			}



			//if promo code is used then deactivate it by setting column value to 1
			//remove code for static promo code Mahesh Gurav 14th March 2018
			// if(!empty($data['used_promo_code'])){

			// 	try{

			// 		$promoApplyModel = Mage::getModel('gadget/request')->load($ticket_id);

			// 		$promoApplyModel->setData('is_active','1');

			// 		$promoApplyModel->save();

			// 	}catch(Exception $ep){

			// 		$error_message = $ep->getMessage();

			// 		echo json_encode(array('status'=>0, 'data'=>array('message'=>$error_message,'order_id'=>''))); exit();	

			// 	}

			// }	



			//for corporate user with corp price, user is lenovo store owner createting promo code

			if($sample['corp_price']){

				//create promo code

				// $promo_code = 'EBLEN'.$order_id.'CORP';

				$promo_code = 'EBLENPR'.rand(1000,9999).'CORP';

				$valid_promo_code = Mage::getModel('customapiv6/customer')->createPromoCode($promo_code);

				

				$corp_updatemodel = Mage::getModel("gadget/request")->load($order_id);

				$corp_updatemodel->setCorpPrice($sample['corp_price']);

				$corp_updatemodel->setPromoCode($valid_promo_code);

				$corp_updatemodel->setInternalOrderId($sample['internal_order_id']);



				try{

					$corp_updatemodel->save();

					//$order_id = $promo_code;					

				}catch(Exception $ec){

					$message = $ec->getMessage();

					echo json_encode(array('status'=>0, 'data'=>array('message'=>$message,'order_id'=>''))); exit();

				}

			}



			echo json_encode(array('status'=>$status, 'data'=>array('message'=>$message,'order_id'=>$order_id))); exit();

		}else{

			echo json_encode(array('status'=>0, 'data'=>array('message'=>'Empty Request','order_id'=>0))); exit();

		}	





	}

	

	public function checkoutAction(){

		try{

			$checkoutData = $this->getRequest()->getParams(); 



			if(false){

				echo json_encode(array('status' => 1, 'message' => 'Success'));

			}else{

				echo json_encode(array('status' => 0, 'message' => 'Error'));

			}

			//$this->logCartAdd($checkoutData);

		}catch (Exception $e) {

			echo json_encode(array('status' => 0, 'message' => $e->getMessage()));

			exit;

		}

	}



	public function logCartAdd($checkoutData) { 





	    $product = $item->getProduct(); 



	    $html = '';

        if($checkoutData)

        {

    	    if ($options)  

    	    {

    	        if (isset($options['options'])) 

    	        {

    	            $result = $options['options'];

    	        }



                $optionsArray = array();



    	        if(count($result)>0){

    	            foreach($result as $key =>$value){

    	            	$resultoptionlabel =  $value['label'];

    	                $resultoptionvalue =  $value['value'];

    	                if(strpos($resultoptionvalue, ',') != false){

                            $html .= '<tr><td valign="top" align="left" style="padding: 0 0 10px 0;"><h2 style="font-family:Arial,Helvetica,sans-serif;font-size:16px;margin:0;padding:0;color:#ffffff;margin:0 0 2px 0">';

    	                	$html .= $resultoptionlabel;

                            $html .= '</h2><p style="margin:0;padding:0;font-size:13px;font-family:Arial,Helvetica,sans-serif;color:#ffffff">';

    	                	$html .= $resultoptionvalue;

                            $html .= '</p></td></tr>'; 

                            $optionsArray[$resultoptionlabel] = $resultoptionvalue;

    	                }else{

                            $html .= '<tr><td valign="top" align="left" style="padding: 0 0 10px 0;"><h2 style="font-family:Arial,Helvetica,sans-serif;font-size:16px;margin:0;padding:0;color:#ffffff;margin:0 0 2px 0">';

                            $html .= $resultoptionlabel;

                            $html .= '</h2><p style="margin:0;padding:0;font-size:13px; line-height:22px; font-family:Arial,Helvetica,sans-serif;color:#ffffff">';

                            $html .= $resultoptionvalue;

                            $html .= '</p></td></tr>'; 

                            $optionsArray[$resultoptionlabel] = $resultoptionvalue;

    	                }

    	                

            		} 

    	    	}

    	    }

    	   	

            $optionsArrayJson = json_encode($optionsArray);

            

            if ($postData) { 

  

                try { 

                    $baseUrl = Mage::getBaseUrl();

                    $post['name'] = $postData['name']; 

                    $post['sku'] = $product->getSku();      

                    $post['proname'] = $product->getName();       

                    $post['brand'] = $product->getAttributeText('gadget');  

                    $price = number_format((float)$postData['product_price'], 2, '.', '');      

                    $post['price'] = number_format($price);  

                    // Mage::log($postData['product_price'],null,'gadget.log');     

                    // Mage::log($price,null,'gadget.log');     

                    // Mage::log($post['price'],null,'gadget.log');           

                    $post['switch'] = $html;    

                    $post['option'] = $html;    

                    $post['options'] = $optionsArrayJson;    

                    $post['pincode'] = $postData['pincode'];       

                    $post['city'] = $postData['city'];                           

                    $post['email'] = $postData['email'];       

                    $post['email1'] = 'mailto:'.$postData['email'];       

                    $post['mobile'] = $postData['mobile'];

     

                    $post['quikr-id'] = $postData['quikr-id'];

                    $post['landmark'] = $postData['landmark'];

                    $post['address'] = $postData['address'];    

                    // $img = 'http://cdn.electronicsbazaar.com/media/catalog/product/cache/1/image/9df78eab33525d08d6e5fb8d27136e95';

                    $img = Mage::getBaseUrl().'media';

                    $post['image'] = $img.$product->getImage();      



                    //set legal term agree on 08th feb 2018 Mahesh Gurav on Sujay's request

                    $post['agree'] = 'YES';



                    $model = Mage::getModel("gadget/request") 

                    ->addData($post)

                    ->save();



                     $post['order_id'] = $model->getId(); 

                   



                        $postObject = new Varien_Object();

                        $postObject->setData($post); 



                        $emailId = $post['email'].','.Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);

                        $recipients = explode(",",$emailId);

                        $recipients[] = $post['email'];       

                        $bccAdd[] = 'keval@electronicsbazaar.com';                  



                        $mailTemplate = Mage::getModel('core/email_template');

                        /* @var $mailTemplate Mage_Core_Model_Email_Template */

                        $mailTemplate->setDesignConfig(array('area' => 'frontend'))

                        ->setReplyTo($post['email'])

                        ->addBcc($bccAdd) 

                        ->sendTransactional(

                            Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),

                            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER), 

                            $recipients,

                            null,

                            array('data' => $postObject) 

                        );

                    

      

                    

                   

                    

                } 

                catch (Exception $e) {

                    

                    

                    

                }



            }



    	}



   }



   //corporate laptop for replace with new

   	public function replacelaptopAction(){

   		$new_laptop_processor = $this->getRequest()->getParam('new_laptop');

   		$old_laptop_processor = $this->getRequest()->getParam('old_laptop');

   		$price = 0;

   		if($new_laptop_processor == 'Core i7'){

	   		switch ($old_laptop_processor) {

			    case "Core i7":

			        $price = 20000;

			        break;

			    case "Core i5":

			        $price = 15000;

			        break;

			    case "Core i3":

			        $price = 12500;

			        break;		    

		        default:

			        $price = 9000;

			        break;

			}   			

   		}else if($new_laptop_processor == 'Core i5'){

	   		switch ($old_laptop_processor) {

			    case "Core i7":

			        $price = 20000;

			        break;

			    case "Core i5":

			        $price = 15000;

			        break;

			    case "Core i3":

			        $price = 12500;

			        break;		    

		        default:

			        $price = 9000;

			        break;

			}   			

   		}else if($new_laptop_processor == 'Core i3'){

	   		switch ($old_laptop_processor) {

			    case "Core i7":

			        $price = 20000;

			        break;

			    case "Core i5":

			        $price = 15000;

			        break;

			    case "Core i3":

			        $price = 12500;

			        break;		    

		        default:

			        $price = 9000;

			        break;

			}   			

   		}



		$status = 0;

   		if($price > 0){

   			$status = 1;

   		}



   		echo json_encode(array('status' => $status, 'price' => $price));

   		exit;

   	}





   //corporate laptop

   //custom prices

   public function newlaptopAction(){

   		$corp_apple_product = '{

		"status": 1,

		"data": {

			"products": {

				"product_id": "16100",

				"name": "New Laptop",

				"price": 0,

				"image": "https:\/\/www.electronicsbazaar.com\/media\/catalog\/productno_selection",

				"options": {

					"Processor": [{

						"option_id": "1271",

						"option_type_id": "5357",

						"option_name": "Core i3",

						"option_price": 20000

					}, {

						"option_id": "1271",

						"option_type_id": "5359",

						"option_name": "Core i5",

						"option_price": 20000

					}, {

						"option_id": "1271",

						"option_type_id": "5369",

						"option_name": "Core i7",

						"option_price": 20000

					}]

					

				}

			}

		}

	}';

				echo $corp_apple_product;

				exit;

   }





   public function laptopAction()

   {

   		//$brand = $this->getRequest()->getParam('brand');

		//$code = $this->getRequest()->getParam('code');

		$base_url = Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);



		//$collection = Mage::helper('gadget')->getBrandedGadgetsCollection($brand,$code);  



		//$_product_id = $collection->getLastItem()->getId();

		$sku = $this->getRequest()->getParam('code');

		$product_id = $this->getRequest()->getParam('product_id');

		$group_id = $this->getRequest()->getParam('group_id');

		//$option_id = $this->getRequest()->getParam('option_id');

		//$option_type_id = $this->getRequest()->getParam('option_type_id');

		// var_dump($sku); exit;



		$is_corporate = $this->getRequest()->getParam('is_corporate');

		if($is_corporate == 1){

			$corp_apple_product = '{"status": 1, "data": { "products": {

			"product_id": "10435",

			"name": "Apple Laptop",

			"price": 0,

			"image": "https:\/www.electronicsbazaar.com\/media\/catalog\/productno_selection",

			"options": {

				"Generation": [{

					"option_id": 0,

					"option_type_id": 16191,

					"option_name": "MacBook (2011 or Earlier)",

					"option_price": 0

				}, {

					"option_id": 0,

					"option_type_id": 16189,

					"option_name": "MacBook (2012 to 2014)",

					"option_price": 0

				}, {

					"option_id": 0,

					"option_type_id": 16187,

					"option_name": "MacBook (2015 or Later)",

					"option_price": 0

				}],

				"Model": [{

					"option_id": "2743",

					"option_type_id": "5927",

					"option_name": "Macbook",

					"option_price": 0

				}, {

					"option_id": "2743",

					"option_type_id": "5929",

					"option_name": "Macbook Pro",

					"option_price": 0

				}, {

					"option_id": "2743",

					"option_type_id": "5931",

					"option_name": "Macbook Air",

					"option_price": 0

				}],

				"Processor": [{

					"option_id": "1271",

					"option_type_id": "5355",

					"option_name": "AMD",

					"option_price": 9000

				}, {

					"option_id": "1271",

					"option_type_id": "5357",

					"option_name": "Core i3",

					"option_price": 12500

				}, {

					"option_id": "1271",

					"option_type_id": "5359",

					"option_name": "Core i5",

					"option_price": 15000

				}, {

					"option_id": "1271",

					"option_type_id": "5369",

					"option_name": "Core i7",

					"option_price": 20000

				}, {

					"option_id": "1271",

					"option_type_id": "3161",

					"option_name": "Pentium \/ Celeron",

					"option_price": 9000

				}],

				"RAM": [{

					"option_id": "1269",

					"option_type_id": "3157",

					"option_name": "8 GB and above",

					"option_price": 0

				}, {

					"option_id": "1269",

					"option_type_id": "3158",

					"option_name": "Below 8 GB",

					"option_price": 0

				}],

				"Whatistheconditionoftheitem?": [{

					"option_id": "1268",

					"option_type_id": "3154",

					"option_name": "Working",

					"option_price": 0

				}, {

					"option_id": "1268",

					"option_type_id": "3156",

					"option_name": "Not Working",

					"option_price": 0

				}],

				"Arethecompatiblechargerandbatteryincluded?": [{

					"option_id": "1286",

					"option_type_id": "3184",

					"option_name": "Yes",

					"option_price": 0

				}, {

					"option_id": "1286",

					"option_type_id": "3185",

					"option_name": "No",

					"option_price": 0

				}]

			}

		}

	}

}';

			echo $corp_apple_product;

			exit;

		}



			$product = Mage::getModel('catalog/product');

		if(!empty($sku)){

			$_laptop = $product->load($product->getIdBySku($sku));

		}else if($product_id){

			$_laptop = Mage::getModel('catalog/product')->load($product_id);



			$sku = $_laptop->getSku(); 

		}



		$options_array = array();



			if($_laptop->getHasOptions() == 1){



				$collection = Mage::getModel('catalog/product')->getCollection();

				$collection->addAttributeToFilter('status', 1);

				$collection->addAttributeToSelect('*');

				$collection->addAttributeToSort('name', 'ASC');

				

				if(in_array($sku, array('SYGLAPDELL','SYGLAPHP','SYGLAPLENOVO','SYGLAPOTHER'))){

					$processor_products = $collection->addAttributeToFilter('sku', array('like'=>'%SYGLAP-%'));

				}



				// if($sku == 'SYGLAPAPPLE'){

				if(in_array($sku, array('SYGLAPAPPLE','SYGLAPAPPLE-GEN1','SYGLAPAPPLE-GEN2','SYGLAPAPPLE-GEN3'))){

					$generation_products = $collection->addAttributeToFilter('sku', array('like'=>'%SYGLAPAPPLE-%'));

				}

				



				$processor = array();

				$j = 0;

				foreach ($processor_products as $product) {

					$processor[$j]['name'] = $product->getName();

					$processor[$j]['price'] = (int) $product->getPrice();

					$processor[$j]['id'] = (int) $product->getEntityId();

					$j++;

				}



				$generation = array();

				$m = 0;

				foreach ($generation_products as $product) {

					$generation[$m]['name'] = $product->getName();

					$generation[$m]['price'] = (int) $product->getPrice();

					$generation[$m]['id'] = (int) $product->getEntityId();

					$m++;

				}





				$options = '';

				$title = '';

	    		$options_array = array();

				foreach ($_laptop->getOptions() as $o) {

					$title = str_replace(" ", "", $o->getTitle());





					if($title == 'Processor' && $sku !== "SYGLAPAPPLE"){

						foreach ($processor as $key => $value) {

							$options_array[$title][$key]['option_id'] = 0000;

			                $options_array[$title][$key]['option_type_id'] = $value['id'];

			                $options_array[$title][$key]['option_name'] = $value['name'];

			                $options_array[$title][$key]['option_price'] =  $value['price'];

						}

					}else if($title == 'Generation'){

						foreach ($generation as $key => $value) {

							$options_array[$title][$key]['option_id'] = 0000;

			                $options_array[$title][$key]['option_type_id'] = $value['id'];

			                $options_array[$title][$key]['option_name'] = $value['name'];

			                $options_array[$title][$key]['option_price'] =  $value['price'];

						}

					}else{



						$options = $o->getValues();

						$opt_cnt = 0;

			            foreach ($options as $k => $v) {

			            	

			                $options_array[$title][$opt_cnt]['option_id'] = $v->getOptionId();

			                $options_array[$title][$opt_cnt]['option_type_id'] = $v->getOptionTypeId();

			                $options_array[$title][$opt_cnt]['option_name'] = $v->getTitle();

			                $option_price = (int) $v->getPrice();

			                if( $v->getPriceType() == 'percent'){

			                	$option_price = (int) $_laptop->getPrice()*$option_price/100;

			                }

			                $options_array[$title][$opt_cnt]['option_price'] =  $option_price;	



			            	

			                $opt_cnt++;

			            }

					} //else end

				}



			}// end



			$products['products']['product_id'] = $_laptop->getId();

			$products['products']['name'] = $_laptop->getName();

			$products['products']['price'] = (int) $_laptop->getPrice();

			$products['products']['image'] = $base_url.'media/catalog/product'.$_laptop->getImage();

			$products['products']['options'] = $options_array;



		echo json_encode(array('status' => 1, 'data' => $products ));

	}



   public function mobilebrandsAction(){

   		$attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'gadget');

		$attributeId = $attribute->getAttributeId();

 		$attributeOptionMobiles = Mage::getResourceModel('eav/entity_attribute_option_collection')   

                ->setPositionOrder('asc')

                ->setAttributeFilter($attributeId)

                ->setStoreFilter()

                ->load();

        $mobileBrands = $attributeOptionMobiles->getData(); 

        $mobile_brands = array();

        foreach ($mobileBrands as $key => $mobile) {

        	$mobile_brands[$key]['name'] = $mobile['value'];

        	$mobile_brands[$key]['code'] = 'SYGMOB'.strtoupper($mobile['value']);

        	$mobile_brands[$key]['img_url'] = $mobile['image'];

        }

        $data['brands'] = $mobile_brands;

        echo json_encode(array('status' => 1, 'data' => $data));

   }



   public function laptopbrandsAction(){

   		$attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'gadget_laptop');

		$attributeId = $attribute->getAttributeId();

 		$attributeOptionMobiles = Mage::getResourceModel('eav/entity_attribute_option_collection')   

                ->setPositionOrder('asc')

                ->setAttributeFilter($attributeId)

                ->setStoreFilter()

                ->load();

        $laptopBrands = $attributeOptionMobiles->getData(); 

        $laptop_brands = array();

        foreach ($laptopBrands as $key => $mobile) {

        	$laptop_brands[$key]['name'] = $mobile['value'];

        	$laptop_brands[$key]['code'] = 'SYGLAP'.strtoupper($mobile['value']);

        	$laptop_brands[$key]['img_url'] = $mobile['image'];

        }

        $data['brands'] = $laptop_brands;

        echo json_encode(array('status' => 1, 'data' => $data));

   }



   

   public function listAction(){

	   	try{

		   	$customer_id = $this->getRequest()->getParam('user_id');
		   	$for = $this->getRequest()->getParam('for');
		   	$page_no = (int) $this->getRequest()->getParam('page_no');
		   	$page_size = (int) $this->getRequest()->getParam('page_size');

		   	$page=1;

		   	$size=5;

		   	if($page_no > 0){

		   		$page = $page_no;

		   	}



		   	if($page_size > 0){

		   		$size = $page_size;

		   	}



		   	$customer = Mage::getModel('customer/customer')->load($customer_id);



		   	$group_id = $customer->getGroupId();



		   	$email = $customer->getEmail();







		   	$post_json = array();

		   	if($email){

		   		$totalGadgets = Mage::getModel('gadget/request')->getCollection();
		   		$totalGadgets->addFieldToSelect(array('id'));
		   		//$totalGadgets->addFieldToFilter('email',$email);
		   		if($for == 'null'){ /// for normal customer
		   			$totalGadgets->addFieldToFilter('email',$email);
		   		}else{ /// for retailer
		   			$totalGadgets->addFieldToFilter('confirmed_by_user_id',$customer_id);
		   		}
		   		$totalGadgetsCount = $totalGadgets->count();

		   		$collection = Mage::getModel('gadget/request')->getCollection();
		   		$collection->addFieldToSelect(array('id','price','status','promo_code','corp_price'));
		   		//$collection->addFieldToFilter('email',$email);
		   		if($for == 'null'){ /// for normal customer
		   			$collection->addFieldToFilter('email',$email);
		   		}else{ /// for retailer
		   			$collection->addFieldToFilter('confirmed_by_user_id',$customer_id);
		   		}
						   		
		   		// if(in_array($group_id, array(6,7))){
		   		// 	$collection->addFieldToSelect(array('price','status','promo_code'));
			   	// 	$collection->addFieldToFilter('promo_code',array('neq'=>NULL));
		   		// }
		   		$collection->setPageSize($size);
				$collection->setCurPage($page);
				$collection->setOrder('created_at', 'DESC');	
		   		$data = $collection->getData();

		   		// print_r($data);

		   		$count = count($data);
		   		$recent_orders = array();
		   		foreach ($data as $key => $value) {
		   			$correct_price = array();
		   			foreach ($value as $p => $l) {
		   				if($p == 'price'){
		   					$correct_price[$p] = str_replace(",", "", $l);
		   				}else{
		   					$correct_price[$p] = $l;
		   				}
		   			}

		   			if($value['status']){
		   				$statusOptionsArray = Mage::helper('gadget')->getGadgetStatusOptions();
		   				$correct_price['status'] = $statusOptionsArray[$value['status']];
		   			}

		   			//for corporate user

		   			if($correct_price['corp_price'] > 0){
		   				$correct_price['price'] = $correct_price['corp_price'];
		   			}

		   			$recent_orders[] = $correct_price; 
		   		}	

		   		$json_data = array('count'=>$totalGadgetsCount,'recent_orders'=>$recent_orders);
		   		$post_json = array( "status" => 1, "message"=> "My orders listed successfully", "data" => $json_data);

		   		echo json_encode($post_json);

		   		exit;

		   	}else{

		   		$post_json = array( "status" => 0, "message"=> "My orders is empty", "data" => "");

		   		echo json_encode($post_json);

		   		exit;

		   	}

		}catch(Exception $e){

				$post_json = array( "status" => 0, "message"=> $e->getMessage(), "data" => "");

		   		echo json_encode($post_json);

		   		exit;

		}   	

   }



   public function detailAction(){

   		try{

		   	$order_id = $this->getRequest()->getParam('order_id');

		   	if(strpos($order_id, 'CORP') > 0){

		   		$order_id = $this->get_string_between($order_id, 'EBLEN', 'CORP');

		   	}

	   		//$order_id = 10036;

	   		$orderArray = array();

	   		if(!empty($order_id)){

		   		$order = Mage::getModel('gadget/request')->load($order_id);

		   		if($order->getData()){

					foreach ($order->getData() as $key => $value){

	   					if($key !== 'option'){

		   					if($key == 'options'){

			   					$options_array = json_decode($value,1);

			   					foreach ($options_array as $label => $param) {



			   						if($label == 'What is the condition of the item ?'){

				   						$orderArray['Condition'] = $param;

				   					}if($label == 'Are the compatible charger and battery included ?'){

				   						$orderArray['Charger'] = $param;

				   					}if($label == 'IMEI'){

				   						$orderArray['imei_no'] = $param;

				   					}else{

			   							$orderArray[$label] = $param;

				   					}

			   					}

		   					}else if($key == 'imei_no' && $value !== 0){

			   						$orderArray[$key] = $value;

		   					}elseif($key == 'price'){

		   						$orderArray[$key] = str_replace(",", "", $value);

		   					}else if($key == 'serial_number' && strlen($value) > 0){

			   						$orderArray['imei_no'] = $value;

		   					}else if($key == 'status'){

				   				$statusOptionsArray = Mage::helper('gadget')->getGadgetStatusOptions();

				   				$orderArray['status'] = $statusOptionsArray[$value];

				   			}else{

			   					$orderArray[$key] = $value;

		   					}

	   					}

	   				}



	   				//for corporate user

	   				if($orderArray['corp_price'] > 0){

	   					$orderArray['price'] = $orderArray['corp_price'];

	   				}



		   			$return_json = array('status'=>1,'message'=>'Order details','data'=>$orderArray);

	   						   		

	   			}else{

		   			$return_json = array('status'=>0,'message'=>'Order does not exists','data'=>'');

		   			

		   		}

		   			



		   		echo json_encode($return_json);

		   			exit;

		   		

   			}



   		}catch(Exception $e){

   			$return_json = array('status'=>0,'message'=>$e->getMessage(),'data'=>'');

   			echo json_encode($return_json);

		   	exit;

   		}

   }

   public function registerAction() 

	{



		$customerCount = Mage::getModel('customer/customer')->getCollection()

		->addFieldToFilter('mobile',$_REQUEST['mobile']);

		//->addFieldToFilter('group_id',7);



		//mage::log($_REQUEST ,null,'sandeep.log');



		$name = explode(' ', $_REQUEST['name']);

		$_REQUEST['firstname'] = $name[0];



		if($name[1]){

			$_REQUEST['lastname'] = $name[1];  

		}else{

			$_REQUEST['lastname'] = '';  	

		}     

		

		if(empty($_REQUEST['firstname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['firstname'])) { 

			echo json_encode(array('status' => 0, 'message' => 'Please enter valid name'));

			exit;

		}

		if($_REQUEST['ebpin_user'] != $_REQUEST['ebpin_server']) {

			echo json_encode(array('status' => 0, 'message' => 'Eb Pin is Not Correct.'));

			exit;

		}

		// if(empty($_REQUEST['lastname']) || !preg_match("/^[a-zA-Z]*$/",$_REQUEST['lastname'])) {

		// 	echo json_encode(array('status' => 0, 'message' => 'Please provide valid last name.'));

		// 	exit;

		// }

		if(count($customerCount) > 0) { 

			echo json_encode(array('status' => 0, 'message' => 'Mobile Number Already Register.'));

			exit; 

		}

		if(empty($_REQUEST['mobile']) || !preg_match("/^[0-9]{5,10}$/",$_REQUEST['mobile']) || count($customerCount) > 0) { 

			echo json_encode(array('status' => 0, 'message' => 'Please provide 10 Digit Mobile Number.'));

			exit; 

		} 

		if(!filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL)) {

			echo json_encode(array('status' => 0, 'message' => 'Please provide valid Email.'));

			exit;

		}

		if(strlen($_REQUEST['password']) < 6) {

			echo json_encode(array('status' => 0, 'message' => 'Your password must be atleast 6 character long.'));

			exit;

		}

		if(!in_array($_REQUEST['device_type'],array('android', 'iphone'))) {

			echo json_encode(array('status' => 0, 'message' => 'Device type must be android or iphone .'));

			exit;

		}



		for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  

			$randomNumber .= mt_rand(0, 9);

		}



		$data = array();

		$data['firstname'] = $_REQUEST['firstname'];

		$data['lastname'] = $_REQUEST['lastname'];

		$data['email'] = $_REQUEST['email'];

		$data['md5_customer_id'] = md5($_REQUEST['email']);

		$data['password'] = $_REQUEST['password'];

		$data['mobile'] = $_REQUEST['mobile'];

		// $data['dob'] = date('Y-m-d', strtotime($_REQUEST['dob']));

		// $data['prefix'] = $_REQUEST['prefix'];

		$data['device_type'] = $_REQUEST['device_type'];

		$data['push_id'] = $_REQUEST['push_id'];

		// $data['group_id'] = 7;    

		$data['website_id'] = 1;



		//check corporate user

		$store_id = '';

		if($_REQUEST['is_corporate'] == 1){

			//discontine corporate 16 Feb 18

			echo json_encode(array('status' => 0, 'message' => "Sorry! The Corporate user registration is discontinued for now."));

			exit;

			$store_id = 'EBLEN_'.rand (1000,9999);

			$valid_store_id = Mage::getModel('customapiv6/customer')->createCorporateStoreId($store_id);

			$data['group_id'] = 6;

			$data['corporate_store_id'] = $valid_store_id;

		}else{

			$data['group_id'] = 1;

		}



		$data['cus_country'] = 'IN';

		$data['cus_state'] = $_REQUEST['cus_state'];

		$data['cus_city'] = $_REQUEST['cus_city'];

		$data['repcode'] = 'Aff_'.$_REQUEST['firstname'].'_'.$randomNumber;

		

		try {

			$customer = Mage::getModel('customer/customer');

			$customer->addData($data);

			$customer->save();

			$customer->sendNewAccountEmail('registered','',1);

			/*$customerData = Mage::getModel('customapiv6/customer')->getCustomerInfo($customer->getId());*/



			$customer_data = array();

			$customer_data['id'] = $customer->getId();

			$customer_data['firstname'] = $customer->getData('firstname');

			$customer_data['email'] = $customer->getData('email');

			$customer_data['lastname'] = $customer->getData('lastname');

			

			$customer_data['mobile'] = $customer->getData('mobile');



			// creating the user quote and sending in response

			$quoteId = Mage::getModel('customapiv6/customer')->createQuoteAndAssignCustomer($customer);

			$customer_data['quoteId'] = $quoteId;



			$customer_data['is_corporate'] = '0';

			$customer_data['is_affiliate'] = '0';

			if($customer->getGroupId() == 4){

				$customer_data['is_affiliate'] = '1';				

			// }else if(in_array($customer->getGroupId(), array(6,7))){

			}else if($customer->getGroupId() == 6){

				$customer_data['is_corporate'] = '1';

			}

			$customer_data['corporate_store_id'] = $customer->getData('corporate_store_id');



			// update push id within notification table

			$saved_flag = Mage::getModel('customapiv6/customer')->addPushIdAndDeviceType($data['device_type'], $data['push_id'], $customer->getId());



			echo json_encode(array('status' => 1, 'message'=> 'User Registered Successfully', 'login_type' => 'normal', 'user' => $customer_data));

			exit;

		} catch(Exception $ex) {

			$this->getResponse()->setBody(json_encode(array('status' => 0, 'message' => $ex->getMessage())));

		}

	}



	public function sendemailAction(){

		//save gadget POST

		$status = 0;

		$message = '';

		if ($this->getRequest()->isPost()) {

			$gadgetData = $this->getRequest()->getParams();

			$customer_id = $gadgetData['user_id'];



			$customer = Mage::getModel('customer/customer')->load($customer_id);



			$gadgetErrors  = Mage::helper('gadget')->validateData($gadgetData);

	            $errors = $gadgetErrors;

	        try{

	        	$gadget_other = Mage::getModel('gadget/other');

	        	$brand = strip_tags($gadgetData['brand']);

	        	$model = strip_tags($gadgetData['model']);

	        	$description = strip_tags($gadgetData['description']);

	        	$data = array('brand'=>$brand,'model'=>$model,'description'=>$description,'customer_id'=>$customer->getId()); 

	        	$gadget_other->setData($data);



                if (count($errors) === 0) {

                    $gadget_other->save();



                    $receiver = "sellyourgadget@electronicsbazaar.com";

                    // $receiver = "web.maheshgurav@gmail.com";

                    $subject = "Sell your gadget other details for $brand $model";

                    $message = '';

                    $message .='<p>Sell your gadget request for Other Gadget from customer as per below details.</p>';

                    $message .='<p><b>Customer Name:</b> '.$customer->getFirstname().'</p>';

                    $message .='<p><b>Customer Email:</b> '.$customer->getEmail().'</p>';

					$message .= '<table cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; margin:10px 0 0 0;">';

					$message .= '<tbody>';

					$message .= '<tr>';

					$message .= '<td style="font-family: arial; font-size: 14px; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; padding: 10px;"><strong>Brand</strong></td><td style="font-family: arial; font-size: 14px; border-bottom: 1px solid #dddddd; padding: 10px;">'.$brand.'</td>';

					$message .= '</tr>';

					$message .= '<tr>';

					$message .= '<td style="font-family: arial; font-size: 14px; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; padding: 10px;"><strong>Model</strong></td><td style="font-family: arial; font-size: 14px; border-bottom: 1px solid #dddddd; padding: 10px;">'.$model.'</td>';

					$message .= '</tr>';

					$message .= '<tr>';

					$message .= '<td style="font-family: arial; font-size: 14px; border-right: 1px solid #dddddd; padding: 10px;"><strong>Description</strong></td><td style="font-family: arial; font-size: 14px; padding: 10px;">'.$description.'</td>';

					$message .= '</tr>';

					$message .= '</tbody>';

					$message .= '</table>';

                    Mage::helper('gadget')->sendEmail($receiver,$subject,$message);

                    $status = 1;

                    $message = "Thanks. We will get back to you shortly.";

                } else {

                    $str_msg = '';

                    foreach ($errors as $errorMessage) {

                        $str_msg .= $errorMessage." ";

                    }



                    $message= rtrim($str_msg," ");

                    $status = 0;

                }

	        }catch(Exception $e){

	        	$message =  $e->getMessage();

	        	$status = 0;

	            echo json_encode(array("status"=>$status,"message"=>$message));

	            exit;

	        }

	    }

        echo json_encode(array("status"=>$status,"message"=>$message));

        exit;

	}



	//08 Dec 17 Mahesh Gurav

	public function checkpincodeAction(){

		if(!empty($_REQUEST['pincode'])){

			$pincodeData = Mage::getModel('operations/serviceablepincodes')->getCollection()->addFieldToFilter('pincode',$_REQUEST['pincode'])->getFirstItem()->getData();



			if($pincodeData['ecom_qc']){

				echo json_encode(array("status"=>1,"message"=>"We service this pincode"));

        		exit;

			}else{

				echo json_encode(array("status"=>0,"message"=>"Sorry! We are yet to service this pincode"));

        		exit;

			}

		}

	}



	public function homeAction()

	{    

		try {

			$banners = array();

			$media_url = Mage::getUrl('media/sellurgadget');

			for ($i=0; $i < 1; $i++) { 

		        $banners[$i]['banner_type'] = '';

		        $banners[$i]['event_click_label'] = 'sellyourgadet-home';

				$banners[$i]['category_id'] = '';

				$banners[$i]['category_name'] = '';

				$banners[$i]['banner_image'] = $media_url.'banner-app-5.png';

				$banners[$i]['banner_image_corp'] = $media_url.'sell_laptop_corp.png';

			}



			$tmp['banners'] = $banners;

			$tmp['working'] = $media_url.'how-it-works-5.png';			

			$tmp['mobile'] = $media_url.'smartphone-app-5.png';			

			$tmp['laptop'] = $media_url.'laptop-app-5.png';

			$tmp['laptop_corp'] = $media_url.'sell_banner_corp.png';			

				

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

		

		$this->getResponse()->setHeader('Content-type', 'application/json', true);

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

	}

	

	public function trackAction(){

		$block = $this->getLayout()->createBlock('core/template')

		->setTemplate('gadget/track.phtml');

		$this->getResponse()->setBody($block->toHtml());

	}





	//Promo code validate for getting price

	public function validatePromoCodeAction(){

		$request = $this->getRequest()->getParams();

		if(!empty($request['promo_code'])){

			$promo_code = $request['promo_code'];

			$promo_collection = Mage::getModel('gadget/request')->getCollection()->addFieldToFilter('promo_code',array('eq'=>$promo_code));

			if(count($promo_collection) > 0){

				foreach ($promo_collection as $promoModel) {

					if(is_null($promoModel->getIsActive())){

						$status = 1;

						$new_price = $promoModel->getCorpPrice();

						$message = 'Promo code applied successfully';

					}else{

						$status = 0;

						$message = 'Promo code is invalid for this request';

					}

					//take unique and one promo code only

					break;

				}



				echo json_encode(array('status'=>$status,'message'=>$message,'new_price'=>$new_price));

					exit;



			}else{

				echo json_encode(array('status'=>0,'message'=>'Promo code is invalid for this request','new_price'=>0));

				exit;

			}

		}	

	}



	//validate promo code using promo code and internal order id

	public function validatePromoCodeUsingOrderIdAction(){

		$request = $this->getRequest()->getParams();

		if(!empty($request['promo_code']) && $request['internal_order_id']){

			$promo_code = $request['promo_code'];

			$internal_order_id = $request['internal_order_id'];



			$requestCollection = Mage::getModel('gadget/request')->getCollection();

			$requestCollection->addFieldToFilter('promo_code',array('eq'=>$promo_code));

			$requestCollection->addFieldToFilter('internal_order_id',array('eq'=>$internal_order_id));

			$requestCollection->addFieldToFilter('is_active',NULL);

			$new_price = 0;

			if(count($requestCollection) > 0){

				foreach ($requestCollection as $promoModel) {

					$status = 1;

					$new_price = $promoModel->getCorpPrice();

					$message = 'Promo code applied successfully';

					break;

				}

			}else{

				$status = 0;

				$message = 'The Promo code you have entered does not match the order id';

			}



			echo json_encode(array('status'=>$status,'message'=>$message,'new_price'=>$new_price));

			exit;

		}

	}



	private function get_string_between($string, $start, $end){

	    $string = ' ' . $string;

	    $ini = strpos($string, $start);

	    if ($ini == 0) return '';

	    $ini += strlen($start);

	    $len = strpos($string, $end, $ini) - $ini;

	    return substr($string, $ini, $len);

	}

	

	//gadget request bank list asa per customer email
	//22 Jan 2018
	//Mahesh Gurav  Changes made by Sonali on 21st Mar 2018
	public function getBankListAction(){
		$user_email = $_REQUEST['email'];
		
		
		$bank_array = array();
		if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != ''){
			$request = Mage::getModel('gadget/request')->getCollection()
			->addFieldToSelect(array('confrm_by_retailer_name_in_bank','confrm_by_retailer_bank_name','confrm_by_retailer_acct_number','confrm_by_retailer_ifsc_code'))
			->addFieldToFilter('confirmed_by_user_id',$_REQUEST['user_id']);
			foreach ($request as $bank) {
				if($bank->getconfrm_by_retailer_name_in_bank() != ''){
					$bank_str = "Customer Name: ".$bank->getconfrm_by_retailer_name_in_bank().", Bank Name: ".$bank->getconfrm_by_retailer_bank_name().", Bank IFSC: ".$bank->getconfrm_by_retailer_ifsc_code().", Bank Account No: ".$bank->getconfrm_by_retailer_acct_number();
					if(!in_array($bank_str, $bank_array)){
						$bank_array[] = $bank_str;
					}
				}
			}

			$request = Mage::getModel('gadget/request')->getCollection()
			->addFieldToSelect(array('bank_customer_name','bank_name','bank_ifsc','bank_account_no'))
			->addFieldToFilter('email',$user_email);
			
			$min_val = 3;
			foreach ($request as $bank) {
				if(strlen($bank->getBankCustomerName()) > $min_val && strlen($bank->getBankName()) > $min_val && strlen($bank->getBankIfsc()) > $min_val && strlen($bank->getBankAccountNo()) > $min_val){
					$bank_str = "Customer Name: ".$bank->getBankCustomerName().", Bank Name: ".$bank->getBankName().", Bank IFSC: ".$bank->getBankIfsc().", Bank Account No: ".$bank->getBankAccountNo();
					if(!in_array($bank_str, $bank_array)){
						$bank_array[] = $bank_str;
					}

					
				}
			}

		}else if(!empty($user_email)){
			$request = Mage::getModel('gadget/request')->getCollection()
			->addFieldToSelect(array('bank_customer_name','bank_name','bank_ifsc','bank_account_no'))
			->addFieldToFilter('email',$user_email);
			
			$min_val = 3;
			foreach ($request as $bank) {
				if(strlen($bank->getBankCustomerName()) > $min_val && strlen($bank->getBankName()) > $min_val && strlen($bank->getBankIfsc()) > $min_val && strlen($bank->getBankAccountNo()) > $min_val){
					$bank_str = "Customer Name: ".$bank->getBankCustomerName().", Bank Name: ".$bank->getBankName().", Bank IFSC: ".$bank->getBankIfsc().", Bank Account No: ".$bank->getBankAccountNo();
					if(!in_array($bank_str, $bank_array)){
						$bank_array[] = $bank_str;
					}

					
				}
			}
		}

		if(count($bank_array) > 0){
			echo json_encode(array('status'=>1,'bank_list'=>$bank_array));
			exit;
		}else{
			echo json_encode(array('status'=>0,'bank_list'=>array()));
			exit;
		}

	}


	//SYG normal user promo codes static style
    //Mahesh Gurav
    //12 March 2018
    public function validateStaticCodeAction(){
    	$request = $this->getRequest()->getParams();
    	$code = $request['promo_code'];
    	$processor = $request['processor'];
    	$status = '0';
    	$price = Mage::getModel('gadget/gadget')->getPromoPrice($code,$processor);
    	if($price > '0'){
    		$status = 1;
    		$message = 'Promo code applied successfully';
    	}else{
    		$message = 'Invalid promo code';
    	}

    	echo json_encode(array('status' => $status, 'message'=>$message, 'price' => $price));
    	exit;
    }

    /*
    * @date : 16th Mar 2018
    * @author : Sonali Kosrabe
    * @purpose : To save retailers data 
    */

    public function saveRetailersDataAction(){
    	$postData = $this->getRequest()->getParams();

    	$customer = Mage::getModel('customer/customer')->load($postData['customer_id']);
    	
    	if(!isset($postData['address_id'])){
    		
    		$postcode = $postData['postcode'];
			$city = $postData['city'];
			$street = $postData['street'].', '.$postData['street1'];
			$regionCode =  $postData['region_id'];
			$countryCode = 'IN';
			
			$directory = Mage::getModel('directory/region')->load($regionCode,'code');
			$regionId = $directory->getregion_id();
			
			

			$address = Mage::getModel("customer/address");
			$address->setCustomerId($customer->getId())
			        ->setFirstname($customer->getFirstname())
			        ->setLastname($customer->getLastname())
			        ->setCountryId('IN')
					->setRegionId($regionId) //state/province, only needed if the country is USA
					->setRegion($postData['region_id'])
			        ->setPostcode($postcode)
			        ->setCity($city)
			        ->setTelephone($customer->getMobile())
			        ->setFax($customer->getMobile())
			        ->setStreet($street)
			        ->setIsDefaultBilling(0)
			        ->setIsDefaultShipping(0)
			        ->setSaveInAddressBook(1);
			 
			try{
			    $address->save();
			    $postData['address_id'] = $address->getId();
			}
			catch (Exception $e) {
			    echo json_encode(array('status'=>0,'message'=>'unable to save address.'));
            
			}
    	}

    	$bankDetails = json_decode($postData['bank_details']);
    	$bankDetails = (array)$bankDetails[0];

    	//print_r($postData);die;
    	$orderdetails = Mage::getModel('gadget/request')->load($postData['order_id']);
        $sdata = array('confrm_by_retailer_name_in_bank'=>$bankDetails['customer_name'],
                        'confrm_by_retailer_bank_name'=>$bankDetails['bank_name'],
                        'confrm_by_retailer_acct_number'=>$bankDetails['bank_account_no'],
                        'confrm_by_retailer_ifsc_code'=>$bankDetails['bank_ifsc'],
                        'confrm_by_retailer_address_id'=>$postData['address_id'],
                        'status'=>'sellback_request',
                        'retailers_sellback_at'=>date('Y-m-d H:i:s'));
        $orderdetails->addData($sdata);
        try{
            $orderdetails->save();
            /// Process order to ecom for pickup
            $ecomData = Mage::getModel('gadget/request')->sellBackSygOdrer($postData['order_id'],$customer->getId());
            echo json_encode($ecomData);
            
        }catch(Exception $e){
            echo json_encode(array('status'=>0,'message'=>'Problem in sending request.'));
        }
    }

    /*
    * @date : 16th Mar 2018
    * @author : Jitendra Patel
    * @purpose : To confirm SYG offer request by retailers. 
    */
    public function orderconfirmAction()
    {       
            $data = $this->getRequest()->getParams();
            if(!empty($data))
            {
              if($data['orderid'] != '' && $data['userid'] != '')
                {                            
                    //set session.
                    $orderdata = '';
                    $orderid = $data['orderid'];
                    $userid = $data['userid'];                    
                    $orderdata = Mage::getModel('gadget/request')->load($orderid);
                    $orderdata = $orderdata->getData();                                                    
                    try {
                           if(!empty($orderdata))
                           {
                             if($orderdata['is_order_confirmed'] != '' && $orderdata['is_order_confirmed'] == 'Yess')
                               {                            
                                echo json_encode(array("status" => 0, "message" => "Oops! This order is already confirmed by someone else."));
                                exit;                           
                               }
                               else
                               {
                                $orderdetails = Mage::getModel('gadget/request')->load($orderid);
                                $orderdetails->setConfirmedByUserId($userid);
                                $orderdetails->setIsOrderConfirmed('Yes');
                                $orderdetails->setStatus('confirmed_by_retailer');
                                $orderdetails->save();

                                //code for load customer details.
                                $customerdetails = Mage::getModel('customer/customer')->load($userid);//$data['userid'] 
                                $customerdetails = $customerdetails->getData();                                

                                //code for send email for confirmation.
                                $templateId = 31;
                             
                                // Set sender information            
                                $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
                                $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');        
                                $sender = array('name' => $senderName,
                                            'email' => $senderEmail);
                                
                                // Set recepient information
                                $recepientEmail = $customerdetails['email'];
                                $recepientName  = $customerdetails['firstname'];;        
                                
                                // Get Store ID        
                                $store = Mage::app()->getStore()->getId();
                             
                                $vars = array( 
                                	'order_id'      => $orderid,
                                    'orderid'      => $orderid,
                                    'customername' => $customerdetails['firstname'],
                                    'proname'      => $orderdetails['proname'],
                                    'price'        => $orderdetails['price'],
                                    'firstname'    => $customerdetails['firstname'],
                                    'mobile'       => $customerdetails['mobile'],                                                                         
                                    'address'      => $orderdetails['address']
                                    );                                    
                                        
                                $translate  = Mage::getSingleton('core/translate');
                             
                                // Send Transactional Email
                                Mage::getModel('core/email_template')
                                    ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
                                        
                                $translate->setTranslateInline(true); //echo "Send"; //exit;

                                Mage::log('API Order Confirm User Email ID : '.$customerdetails['email'], null, 'syg-retailers.log', true);

				                //code for get device id for users.
				                $user_device_id_data = '';
				                $user_device_id      = '';
				                $user_device_type    = '';
				                $user_device_id_data = Mage::getModel('neo_notification/fcmpush')->getCollection()
				                                    ->addFieldToSelect('device_Id')                                
				                                    ->addFieldToSelect('device_type')
				                                    ->addFieldToFilter('user_id',$customerdetails['entity_id']);
				                                                                
				                $user_device_id_data = $user_device_id_data->getData();

				                if(count($user_device_id_data) > 0)
				                {
				                    foreach($user_device_id_data as $data)
				                    {
				                        $user_device_id   = $data['device_Id'];
				                        $user_device_type = $data['device_type'];                

				                        //echo "Device ID:";echo '<pre>', print_r($user_device_id);exit;
				                        if($user_device_id != '' && $user_device_type != '')
				                        {
				                            //$user_device_id = array('37162');
				                            Mage::log('API Device ID : '.$user_device_id, null, 'syg-retailers.log', true);
		                                    Mage::log('API Order Confirm User Device ID Found'.$user_device_id, null, 'syg-retailers.log', true);
		                                    $message = 
		                                    "Dear ".$customerdetails['firstname'].",\nYou have agreed to pick up the following device. Kindly ensure the same is picked up within the next 24 Hours. \nOrder Number – " .$orderid. "\nDevice – " .$orderdetails['proname']. "\nExchange Price to be Paid – INR ".str_replace('?','',$orderdetails['price'])."\nIncentive – INR 500\nCustomer Phone No - " .$customerdetails['mobile']."\nPickup Address - " .$orderdetails['address']."";

		                                    $notification_data = array(
		                                        'title' => $message,
		                                        'type' => 'syg_link_confirm',
		                                        'user_id' => $customerdetails['entity_id'],
		                                        'order_id' => $orderid                     
		                                    );
				                            $ios = $user_device_type;                    
				                            $fcm_response = Mage::helper('neo_notification')->sygFCMNotification($user_device_id, $productData, $notification_data, $ios);                    
				                        }
				                        else
				                        {
				                            Mage::log('API Device ID OR Type NoT Found', null, 'syg-retailers.log', true);
				                        }
				                    }
				                }
				                else
				                {
				                   Mage::log('API Device ID NoT Found', null, 'syg-retailers.log', true); 
				                }                                                                                                     

                                echo json_encode(array("status" => 1, "message" => "Thank you for confirming the pick up request.The device details has been shared with you."));
                                exit;                                                                 
                               }
                            }
                            else
                            {
                                echo json_encode(array("status" => 0, "message" => "Order data not found."));
                                Mage::log($e->getMessage());
                                exit;                 
                            }

                    } catch(Exception $e) {
                    echo json_encode(array("status" => 0, "message" => $e->getMessage()));
                    Mage::log($e->getMessage());
                    exit;                
                    }
                }
                else
                {
                    echo json_encode(array("status" => 0, "message" => "Order id or User id not found."));
                    Mage::log($e->getMessage());
                    exit;                 
                }                   
            }
            else
            {
                echo json_encode(array("status" => 0, "message" => "Something went wrong."));
                Mage::log($e->getMessage());
                exit;                 
            }
    }    
} 

