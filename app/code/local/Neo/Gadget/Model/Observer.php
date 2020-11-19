<?php
class Neo_Gadget_Model_Observer
{
    const XML_PATH_EMAIL_RECIPIENT  = 'gadget/gadget/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'gadget/gadget/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'gadget/gadget/email_template';
    const XML_PATH_EMAIL_RECIPIENT_QUIKR  = 'gadget/quikr/recipient_email';
    const XML_PATH_EMAIL_SENDER_QUIKR     = 'gadget/quikr/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE_QUIKR   = 'gadget/quikr/email_template';
    const XML_PATH_ENABLED                = 'gadget/gadget/enabled';
    public function logCartAdd(Varien_Event_Observer $observer) { 
	    //Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/onepage"));
   	    $item = $observer->getQuoteItem();
	    $product = $item->getProduct(); 
	    $customer = Mage::getSingleton('customer/session')->getCustomer();
	    $postData = Mage::app()->getRequest()->getPost();
        // echo "<pre>"; print_r($postData); echo "</pre>"; exit;
        $_session = Mage::getSingleton('core/session')->getSygform();
        $product_price = 0;
        if($postData['product_price'] == $_session['product_price']){
            $product_price = $postData['product_price'];
        }else{
            $product_price = $_session['product_price'];
        }
	    $options = $product->getTypeInstance(true)->getOrderOptions($product);
	    $html = '';
        if($product->getAttributeSetId() == 48)
        {
    	    if ($options)  
    	    {
    	        if (isset($options['options'])) 
    	        {
    	            $result = $options['options'];
    	        }
                $optionsArray = array();
    	        if(count($result)>0){
                    //setting processor for laptops in options
                    $processor = '';
                    if(substr($product->getSku(), 0, 7 ) === "SYGLAP-" && $product->getProductDepartment() == 2730){
                        $processor = $product->getName();
                        $optionsArray['Processor'] = $processor;
                        $html .= '<tr><td valign="top" align="left" style="padding: 0 0 10px 0;"><h2 style="font-family:Arial,Helvetica,sans-serif;font-size:16px;margin:0;padding:0;color:#ffffff;margin:0 0 2px 0">';
                        $html .= 'Processor';
                        $html .= '</h2><p style="margin:0;padding:0;font-size:13px;font-family:Arial,Helvetica,sans-serif;color:#ffffff">';
                        $html .= $processor;
                        $html .= '</p></td></tr>';
                    }
                    //generation from apple laptops as product itseld
                    $generation = '';
                    if(substr($product->getSku(), 0, 12 ) === "SYGLAPAPPLE-" && $product->getProductDepartment() == 2730){
                        $generation = $product->getName();
                        $optionsArray['Generation'] = $generation;
                        $html .= '<tr><td valign="top" align="left" style="padding: 0 0 10px 0;"><h2 style="font-family:Arial,Helvetica,sans-serif;font-size:16px;margin:0;padding:0;color:#ffffff;margin:0 0 2px 0">';
                        $html .= 'Generation';
                        $html .= '</h2><p style="margin:0;padding:0;font-size:13px;font-family:Arial,Helvetica,sans-serif;color:#ffffff">';
                        $html .= $generation;
                        $html .= '</p></td></tr>';
                    }
                    $other_brand_name = '';
                    $other_brand_title = '';
                    if(!empty($postData['other_brand_name'])){
                        $other_brand_title = 'Other Brand Name';
                        $other_brand_value = strip_tags($postData['other_brand_name']);
                        $optionsArray[$other_brand_title] = $other_brand_value;
                        $html .= '<tr><td valign="top" align="left" style="padding: 0 0 10px 0;"><h2 style="font-family:Arial,Helvetica,sans-serif;font-size:16px;margin:0;padding:0;color:#ffffff;margin:0 0 2px 0">';
                        $html .= $other_brand_title;
                        $html .= '</h2><p style="margin:0;padding:0;font-size:13px;font-family:Arial,Helvetica,sans-serif;color:#ffffff">';
                        $html .= $other_brand_value;
                        $html .= '</p></td></tr>';
                    }
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
    	   	
            // print_r($optionsArray); exit;
            $optionsArrayJson = json_encode($optionsArray);
            // $product_name = 
            
            if ($postData) { 
                // echo "<pre>"; print_r($postData);
                try { 
                    $baseUrl = Mage::getBaseUrl();
                    $post['name'] = $postData['name']; 
                    $post['sku'] = $product->getSku();   
                    // $sku_arrray = explode("-", $product->getSku());   
                    // $post['sku'] = $sku_arrray[0];      
                    $post['proname'] = $product->getName();       
                    $post['brand'] = $product->getAttributeText('gadget');         
                    $unformat_price = str_replace("₹", "", $product_price);
                    $unformat_price = str_replace(",", "", $unformat_price);
                    $unformat_price = str_replace("Rs.", "", $unformat_price);
                    $post['price'] = $unformat_price;       
                    $post['switch'] = $html;    
                    $post['option'] = $html;    
                    $post['options'] = $optionsArrayJson;    
                    $post['pincode'] = $postData['pincode'];       
                    $post['city'] = $postData['city'];                           
                    $post['email'] = $customer->getEmail();       
                    $post['email1'] = 'mailto:'.$customer->getEmail();       
                    $post['mobile'] = $postData['mobile'];
                    $post['quikr-id'] = $postData['quikr-id'];
                    // $post['landmark'] = $postData['landmark'];
                    // $post['address'] = $postData['address'];    
                    //$img = 'http://cdn.electronicsbazaar.com/media/catalog/product/cache/1/image/9df78eab33525d08d6e5fb8d27136e95';
                    $post['image'] = (string) Mage::helper('catalog/image')->init($product, 'image'); 
                    $post['bank_customer_name'] = $postData['customer_name'];      
                    $post['bank_name'] = $postData['bank_name'];    
                    $post['bank_ifsc'] = $postData['ifsc_code'];      
                    $post['bank_account_no'] = $postData['account_number'];      
                    // $post['serial_number'] = $postData['serial_number'];   
                    // $post['imei_no'] = $postData['serial_number']; 
                    if(isset($postData['promo_code']) && $postData['promo_code'] !== ''){
                        $post['used_promo_code'] = $postData['promo_code']; 
                    }
                    $options_params  = array_keys($optionsArray);
                    
                    if(in_array('IMEI', $options_params)){
                        $post['serial_number'] = $optionsArray['IMEI'];   
                    }else if(in_array('Serial', $options_params)){
                        $post['serial_number'] = $optionsArray['Serial'];   
                    }else{
                        Mage::getSingleton('core/session')->addError('Sorry! Unable to process further due to IMEI/Serial Number not found in request');
                        Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/failure/")->sendResponse();
                        exit;
                    }
                    $post['address_id'] = $postData['default_address'];   
                    $addressCustomer = Mage::getModel('customer/address')->load($postData['default_address'])->getData(); 
                    
                    $address = '';
                    if($addressCustomer['street']){
                        $address = $addressCustomer['street'];
                    }
                    if($addressCustomer['city']){
                        $address .= ' '.$addressCustomer['city'];
                    }
                    if($addressCustomer['region']){
                        $address .= ' '.$addressCustomer['region'];
                    }
                    if($addressCustomer['postcode']){
                        $address .= ' '.$addressCustomer['postcode'];
                    }
                    if($addressCustomer){
                    $post['address'] = $address;  
                        $post['name'] = $addressCustomer['firstname'].' '.$addressCustomer['lastname'];
                        $post['pincode'] = $addressCustomer['postcode'];
                        $post['city'] = $addressCustomer['city'];
                        $post['email'] = $customer->getEmail();
                        // $post['mobile'] = $addressCustomer['mobile'];
                        $post['mobile'] = $customer->getMobile();
                    }
                    // if(!empty($postData['street']) && !empty($postData['region_id']) && !empty($postData['postcode']) && !empty($postData['city'])){
                    if(!empty($postData['street']) && !empty($postData['postcode']) && !empty($postData['city'])){
                        $street = strip_tags($postData['street']);
                        
                        $post['name'] = $postData['name'];
                        $post['email'] = $customer->getEmail();
                        $post['mobile'] = $postData['mobile'];
                        $post['pincode'] = $postData['postcode'];
                        $post['city'] = $postData['city'];
                        // print_r($postData);
                        $post['address'] = $street.", ".$postData['city'].", ".$postData['region_id'].", India";
                        
                        $serviceable = Mage::getModel('operations/serviceablepincodes')->getPincodeData($postData['postcode']);
                        if(!empty($serviceable) && $serviceable['ecom_qc'] == 1){
                        $regionId =  $serviceable['region_id'];
                        $countryCode =  $serviceable['country'];
                        $city =  $serviceable['city'];
                            //save address to customer address
                            $countryCode = 'IN';
                            $regionModel = Mage::getModel('directory/region')->loadByCode($regionCode, $countryCode);
                            $regionId = $regionModel->getId();
                            $address = Mage::getModel("customer/address");
                            $address->setCustomerId($customer->getId())
                                    ->setFirstname($customer->getFirstname())
                                    ->setLastname($customer->getLastname())
                                    ->setCountryId($countryCode)
                                    ->setRegionId($regionId) //state/province, only needed if the country is USA
                                    ->setPostcode($postData['postcode'])
                                    ->setCity($city)
                                    ->setTelephone($customer->getMobile())
                                    ->setFax($customer->getMobile())
                                    ->setStreet($postData['street'])
                                    // ->setIsDefaultBilling('1')
                                    // ->setIsDefaultShipping('1')
                                    ->setSaveInAddressBook('1');
                        }else{
                            Mage::getSingleton('core/session')->addError('Sorry! We are yet to service this pincode');
                            Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/failure/")->sendResponse();
                        }
                    
                        
                        try{
                            $address->save();
                        }catch (Exception $e) {
                            // Zend_Debug::dump($e->getMessage());
                        }
                    }
                    
                    
                    // print_r($addressCustomer);
                    // var_dump($addressCustomer['fax']);
                    // var_dump($addressCustomer['postcode']);
                    
                    
                    // print_r($post); exit;
                    //set legal term agree on 08th feb 2018 Mahesh Gurav on Sujay's request
                    $post['agree'] = 'YES';
                    $model = Mage::getModel("gadget/request") 
                    ->addData($post)
                    ->save();
                    $post['order_id'] = $model->getId();     
                    Mage::getSingleton('core/session')->setOrderId($post['order_id']);
                    if($model->getId() > 0 && $product->getEntityId() != 8271){
 
                        $postObject = new Varien_Object();
                        $postObject->setData($post); 
                        // $emailId = $post['email'].','.Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
                        $bccAdd = explode(",",Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT));
                        $recipients = $post['email'];       
                        $mailTemplate = Mage::getModel('core/email_template');
                        /* @var $mailTemplate Mage_Core_Model_Email_Template */
                        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                        //->setReplyTo($post['email'])   
                        ->addBcc($bccAdd) 
                        ->sendTransactional(
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER), 
                            $recipients,
                            null,
                            array('data' => $postObject) 
                        );
                    }else{
                        $postObject = new Varien_Object();
                        $postObject->setData($post); 
                        // $emailId = $post['email'].','.Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT_QUIKR);
                        $bccAdd = explode(",",Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT_QUIKR));
                        $recipients[] = $post['email'];                       
                        $mailTemplate = Mage::getModel('core/email_template');
                        /* @var $mailTemplate Mage_Core_Model_Email_Template */
                        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                        ->setReplyTo($post['email'])
                        ->addBcc($bccAdd) 
                        ->sendTransactional(
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE_QUIKR),
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER_QUIKR), 
                            $recipients,
                            null,
                            array('data' => $postObject)
                            ); 
                    }
      
                    
                    if($product->getEntityId() == 8271){
                         Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."quikr/index/success/")->sendResponse();
                        exit; 
                    }else{
                        Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/success/")->sendResponse();
                        exit; 
                    }
                    
                } 
                catch (Exception $e) {
                    
                    if($product->getEntityId() == 8271){
                         Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."quikr/index/failure/")->sendResponse();
                        exit; 
                    }else{
                        Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/failure/")->sendResponse();
                        exit; 
                    }
                    
                }
            }
            if($product->getEntityId() == 8271){
                Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."quikr/index/success/")->sendResponse();
                exit; 
            }else{
                Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/success/")->sendResponse();
                exit; 
            }
    }
   }
   public function logCartAdd___(Varien_Event_Observer $observer) {
	    //Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/onepage"));
   	    $item = $observer->getQuoteItem();
	    $product = $item->getProduct();
	    $ss = Mage::app()->getRequest()->getPost();
        print_r($ss);
        
	    $options = $product->getTypeInstance(true)->getOrderOptions($product);
	    if ($options)
	    {
	        if (isset($options['options'])) 
	        {
	            $result = $options['options'];
	        }
	        if(count($result)>0){
	            foreach($result as $key =>$value){
	            	echo '<br>'.$resultoption =  $value['label'];
	                echo '<br>'.$resultoption =  $value['value'];
	                
	        }
	    	}
	    }
	    exit;
	   
	         Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/success/")->sendResponse();
            //Mage::app()->getResponse()->sendResponse();
            exit;
   }
		 
}
