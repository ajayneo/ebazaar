<?php
class Neo_Gadget_IndexController extends Mage_Core_Controller_Front_Action{

    const XML_PATH_EMAIL_RECIPIENT  = 'gadget/gadget/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'gadget/gadget/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'gadget/gadget/email_template';
    const XML_PATH_ENABLED          = 'gadget/gadget/enabled';  

    public function IndexAction() {  
        // if(Mage::helper('customer')->isLoggedIn())
        // {
    		Mage::getSingleton('core/session')->unsSygform(); 
            $this->loadLayout();
            $this->getLayout()->getBlock("head")->setTitle($this->__("Gadget"));
            $this->renderLayout(); 
        // }else{
      	 //   Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());
        //     Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'))->sendResponse();
        // }    
    }

    public function mobileAction(){
    	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Mobile"));
	      $this->renderLayout();
    }

    public function gadgetpostAction(){

    	$post_data=$this->getRequest()->getPost();

        if ($post_data) { 
            try { 
                $baseUrl = Mage::getBaseUrl();
                $post['name'] = $post_data['name'];       
                $post['brand'] = $post_data['brand'];       
                $post['price'] = $post_data['price'];       
                $post['switch'] = $post_data['switch'];       
                $post['pincode'] = $post_data['pincode'];       
                $post['city'] = $post_data['city'];       
                $post['email'] = $post_data['email'];       
                $post['mobile'] = $post_data['mobile'];   
                
                $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
                $product = Mage::getModel("gadget/gadget")->load($post_data['reference_id'])->getData();
                //$post['image'] = 'http://www.electronicsbazaar.com/media/emailcampaigns/Affiliates/site/iphone.png';       
                $post['image'] = $mediaUrl . $product['image'];       

                $model = Mage::getModel("gadget/request")
                ->addData($post_data)
                ->save();

                if($model->getId()>0){

                    $postObject = new Varien_Object();
                    $postObject->setData($post); 

                    // $emailId = $post_data['email'].','.Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
                    $bccAdd[] = explode(",",Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT));
                    $recipients[] = $post_data['email'];                      

                    $mailTemplate = Mage::getModel('core/email_template');
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */
                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post_data['email'])
                    ->addBcc($bccAdd) 
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $recipients,
                        null,
                        array('data' => $postObject) 
                    );
                }
                Mage::app()->getResponse()->setRedirect($baseUrl."gadget/index/success/")->sendResponse();
                
                return;
            } 
            catch (Exception $e) {
                $this->_redirect($baseUrl."gadget/index/failure/");
                return;
            }

        }
        $this->_redirect($baseUrl."gadget/index/failure/"); 
    }

    public function successAction(){
        $this->loadLayout();   
          $this->getLayout()->getBlock("head")->setTitle($this->__("Success"));
          
          /* ***** Start code for send SYG retailer notification (code by jp. )***** */
          //get order id.
          $orderid = '';
          $orderid = Mage::getSingleton('core/session')->getOrderId();
          $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();          
          Mage::getModel('gadget/request')->sendSygRetailerNotification($customerId,$orderid);
          /* ***** End code for send SYG retailer notification (code by jp. )***** */
 

          $this->renderLayout();
    }
    public function confirmAction(){
          $this->loadLayout();   
          $this->getLayout()->getBlock("head")->setTitle($this->__("Confirm Page"));
          $this->renderLayout();
    }    

    public function failureAction(){
          $this->loadLayout();   
          $this->getLayout()->getBlock("head")->setTitle($this->__("Failure"));
          $this->renderLayout();
    }
     
    public function searchgadgetAction()
    {                     

        $attr = 'gadget'; 
        $brand = $this->getRequest()->getPost();
        $_product = Mage::getModel('catalog/product');
        $attr = $_product->getResource()->getAttribute($attr);
        if ($attr->usesSource()) {
            $gadgetId = $attr->getSource()->getOptionId($brand['brand']); 
        }

        $products = Mage::getModel('catalog/product')
                        ->getCollection() 
                        ->addAttributeToSelect('name')
                        ->addAttributeToFilter('type_id', 'neo')                        
                        //->addAttributeToFilter('name', array('like' => '%'.$brand['string'].'%'))
                        ->addAttributeToFilter($attr, $gadgetId);    

                   

        $html = '';
 

  
        foreach($products as $product)
        {  
            //target="_blank"
            $html .= '<a  href='.$product->getUrlInStore().' onclick="fillme('."'".$product->getName()."'".');">';
            $html .= '<div class="user_div">';
            //$html .= '<img src="'.(string)Mage::helper('catalog/image')->init($product, 'image')->resize(30).'" style="border-radius:5px;float:left;">';
            $html .= '<div class="name">'.$product->getName().'</div>';
            $html .= '</div>';
            $html .= '</a>';
        }
        
        if($html == ''){
            $html = '<div class="no_data">No Result Found !</div>';
        }
        echo $html;
    }

    public function listAction(){
        $this->loadLayout();   
        $this->getLayout()->getBlock("head")->setTitle($this->__("Trade Orders"));
        $this->renderLayout(); 
    }

    public function detailAction(){
        $customer = Mage::getSingleton('customer/session');
        if(!$customer->isLoggedIn()){
            //echo 'no logged in';
            $this->_redirect('/');
            return;
        }
     $this->loadLayout();   
        $this->getLayout()->getBlock("head")->setTitle($this->__("Trade Details"));
        $this->renderLayout();   
    }

    public function NewAction() {  

          // if(Mage::helper('customer')->isLoggedIn())
          // {
            $this->loadLayout();   
            $this->getLayout()->getBlock("head")->setTitle($this->__("Gadget"));
            $this->renderLayout(); 
          // }else{
          //   Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());
          //   Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'))->sendResponse();
          // }    
    }

    public function checkpincodeAction(){
        if(!empty($_REQUEST['pincode'])){
            $pincodeData = Mage::getModel('operations/serviceablepincodes')->getCollection()->addFieldToFilter('pincode',$_REQUEST['pincode'])->getFirstItem()->getData();
            $cookies['pincode'] = $_REQUEST['pincode'];
            $cookies['ecom_qc'] = $pincodeData['ecom_qc'];
            Mage::getModel('core/cookie')->set('syg-pincode', serialize($cookies));

            if($pincodeData['ecom_qc']){
                echo json_encode(array("status"=>1,"message"=>"We service this pincode"));
                exit;
            }else{
                echo json_encode(array("status"=>0,"message"=>"Sorry! We are yet to service this pincode"));
                exit;
            }
        }
    }

    public function brandsAction(){
        if(!empty($_REQUEST['cat'])){
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $_REQUEST['cat']);
            $skin = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/rwd/ebtronics/';  
            $str = '';
            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);
                if($_REQUEST['cat'] == 'gadget'){
                    $par = "mobile-";
                    $sku = "SYGMOB";
                }else if($_REQUEST['cat'] == 'gadget_laptop'){
                    $par = "laptop-";
                    $sku = "SYGLAP";
                }
                if(count($options) > 0){
                    foreach ($options as $key => $value) {
                        $class = $par.strtolower($value['label']);
                        $sku_str = $sku.strtoupper($value['label']);
                        $image_name = $value['label'];
                        if($value['label'] == 'Other'){
                            $image_name = 'laptop-icon';
                        }
                        $str .='<li onclick="sku(\''.$sku_str.'\')"class="'.$class.'"><div class="gadget-brand__box"><img src="'.$skin.'images/brands-logo/'.strtolower($image_name).'.png" alt="'.$value['label'].'"></div><div class="gadget-brand__title">'.$value['label'].'</div></li>';
                    }
                }
            }

            if(strlen($str) > 0 ){
                echo json_encode(array("status"=>1,"data"=>$str));
                exit;
            }else{
               echo json_encode(array("status"=>0,"data"=>"No Brands Found"));
                exit; 
            }
        }
    }

    public function skuAction(){
        //classes to remember
        //div //new-gadget-mobile
        //ul //gadget-mobile
        //li //mobile-apple
        
        if(!empty($_REQUEST['sku'])){

            $result_cat = substr($_REQUEST['sku'], 0, 6);
            $result_brand =  substr($_REQUEST['sku'],6);
            
            if(strlen($result_brand) > 0){
                $brand = strtolower($result_brand);
            }
            
            $cat = '';
            if($result_cat == 'SYGMOB'){
                $cat = 'mobile';
            }else if($result_cat == 'SYGLAP'){
                $cat = 'laptop';
            }
            
            if(strlen($cat) > 0 && strlen($brand)){
                $cookies['selected-div'] = '.new-gadget-'.$cat;  
                $cookies['selected-ul'] = '.gadget-'.$cat;  
                $cookies['selected-li'] = '.'.$cat.'-'.$brand; 
                $cookies['selected-li'] = '.'.$cat.'-'.$brand; 
                // setcookie("syghome", serialize($cookies), time() + (60 * 20));
                Mage::getModel('core/cookie')->set('syghome', serialize($cookies));
            }
            
            $product_id = Mage::getModel("catalog/product")->getIdBySku($_REQUEST['sku']);
        }
        
        if($product_id){    
            $_product = Mage::getModel("catalog/product")->load($product_id);
            
            $str = Mage::getBaseUrl().$_product->getUrlPath();
            echo json_encode(array("status"=>1,"data"=>$str));
            exit;
        }else{
            echo json_encode(array("status"=>0,"data"=>"No Product Found"));
            exit;
        }
        
    }

    public function trackAction(){
        $block = $this->getLayout()->createBlock('core/template')
        ->setTemplate('gadget/track.phtml');
        
        $this->getResponse()->setBody($block->toHtml());
    }

    public function serializeAction(){
        $params = $this->getRequest()->getParams();
        Mage::log("Serialize action start ------------------",null,'gadget.log',true);
        $product_url ='';
        $details_string = '';
        if(!empty($params)){
            $details_string .= '<ul>';
            
            if(strip_tags($params['other_brand_name']) && !in_array(5115, array_keys($params['options']))){

                    $brand_name = strip_tags($params['other_brand_name']);
                    $details_string .= '<li class="clearfix">';
                    $details_string .= '<label>Enter Brand Name</label>';
                    $details_string .='<div class="description">'.$brand_name.'</div>';
                    $details_string .='</li>';

                    $params['other_brand_name'] = $brand_name;
            }

            if($params['processor']){
                if (!filter_var($params['processor'], FILTER_VALIDATE_URL) === false) {

                    $link_array = explode('/',$params['processor']);
                    $page = end($link_array);
                    $page = str_replace("laptop", "", $page);
                    $processor = ucfirst(trim(str_replace("-", " ", $page)));
                    $details_string .= '<li class="clearfix">';
                    $details_string .= '<label>Processor</label>';
                    $details_string .='<div class="description">'.$processor.'</div>';
                    $details_string .='</li>';

                    $params['processor'] = $processor;
                }
            }

            if($params['generation']){
                if (!filter_var($params['generation'], FILTER_VALIDATE_URL) === false) {

                    $link_array = explode('/',$params['generation']);
                    $page = end($link_array);
                    // $page = str_replace("laptop", "", $page);
                    $generation = ucfirst(trim(str_replace("-", " ", $page)));
                    $details_string .= '<li class="clearfix">';
                    $details_string .= '<label>Generation</label>';
                    $details_string .='<div class="description">'.$generation.'</div>';
                    $details_string .='</li>';

                    $params['generation'] = $generation;
                }
            }


             


            $product_url = $params['syg_product_url'];
            $product_id = $params['product_id'];
            $_productModel = Mage::getModel('catalog/product')->load($product_id);


            if($_productModel->getProductDepartment() == 2730){
                $processor = $_productModel->getName();
            }

            $_options = $_productModel->getOptions();
            $price = $params['product_price'];

            
            $processor = '';
            $condition = '';
            if(in_array(5115, array_keys($params['options']))){
                //corporate form
                $laptop_options = Mage::helper('gadget')->getLaptopOptions();
                $options_on_syg = array();

                if(strip_tags($params['other_brand_name'])){
                    $options_on_syg['Other Brand Name'] = strip_tags($params['other_brand_name']);
                    $details_string .= '<li class="clearfix">';
                    $details_string .= '<label>Other Brand Name</label>';
                    $details_string .='<div class="description">'.strip_tags($params['other_brand_name']).'</div>';
                    $details_string .='</li>';
                }
                foreach ($laptop_options as $options) {
                    if(in_array($options['option_id'], array_keys($params['options']))){
                        $details_string .= '<li class="clearfix">';
                        $details_string .= '<label>'.$options['default_title'].'</label>';
                        if($options['option_type'] == 'field'){
                            $details_string .='<div class="description">'.$params['options'][$options['option_id']].'</div>';
                            $options_on_syg[$options['default_title']] = $params['options'][$options['option_id']];
                        }else{
                            foreach ($options['values'] as $key => $value) {
                                # code...
                                // echo "key ".$key." val = ".$value." option val = ".$params['options'][$options['option_id']];
                                if( $params['options'][$options['option_id']] == $key){
                                    $details_string .='<div class="description">'.$value.'</div>';
                                    $options_on_syg[$options['default_title']] = $value;
                                }
                            }
                        }
                        $details_string .='</li>';
                    }

                }

                $params['corporate_options'] = $options_on_syg;
                
            }else{
                foreach ($_options as $value) {
                    $option_id = $value->getOptionId();
                    if(in_array($option_id, array_keys($params['options']))){
                        $details_string .= '<li class="clearfix">';
                    
                        //option label
                        $title = $value->getTitle();
                        if(strrpos($value->getTitle(), 'condition')){ 
                            $title = 'Condition';
                        }else if(strrpos($value->getTitle(), 'compatible charger')){ 
                            $title = 'Is the compatible charger included ?';
                        }
                        $details_string .= '<label>'.$title.'</label>';
                        $session_option_type_id = $params['options'][$option_id];
                        // Getting Values if it has option values, case of select,dropdown,radio,multiselect
                        $values = $value->getValues();
                        if($value->getType() == 'field'){
                            $details_string .= '<div class="description">'.$session_option_type_id.'</div>';
                        }

                        foreach ($values as $values) {
                            $option_type_id = $values['option_type_id'];
                            if($session_option_type_id == $option_type_id){
                                $option_title = $values['default_title'];
                                $details_string .='<div class="description">'.$option_title.'</div>';
                                //check non working price using below two params
                                if($title == 'Condition'){
                                    $condition = $option_title;
                                }
                            }
                        }
                        $details_string .='</li>';
                    }
                }    
            }

                
        $details_string .= '</ul>';

        }
        
        //set price using non working processor
        $non_working_processor_prices = array('Pentium or celeron'=>1000,'Atom'=>500,'Amd'=>1000,'Core i3'=>2000,'Core i5'=>2000,'Core i7'=>2000);
        if($condition == 'Non Working'){
            $finalPrice = $non_working_processor_prices[$params['processor']];
            $params['product_price'] = $price = Mage::helper('core')->currency($finalPrice, true, false); 
        }

        Mage::getSingleton('core/session')->setSygform($params); 
        
        // echo $details_string; exit;
        if(Mage::helper('customer')->isLoggedIn())
        {
            echo json_encode(array("status"=>1,"message"=>$details_string,"price"=>$price));
            exit;
        }else{
            // echo $product_url; exit;
            Mage::getSingleton('customer/session')->setBeforeAuthUrl($product_url);
            echo json_encode(array("status"=>0,"message"=>"Customer Not Signed In"));
            exit;
            // Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'))->sendResponse();
        }
    }

    public function checkPostcodeAction(){
        $pincode = $this->getRequest()->getParam('postcode');
        if(!empty($pincode) && strlen($pincode) == 6){
            $serviceable = Mage::getModel('operations/serviceablepincodes')->getPincodeData($pincode);
            
            if(!empty($serviceable)){
                echo  json_encode(array('status'=>1, 'data'=>$serviceable));
                
            }else{
                echo json_encode(array('status'=>0, 'data'=>""));
                
            }   
        }else{
            echo json_encode(array('status'=>0, 'data'=>"Invalid Pincode"));
        }
    }

    public function getGadgetTrackingDetailsAction(){
        $gadget = Mage::getModel('gadget/request')->load(10321);
        $queryArray['awb_number'] = $gadget->getAwbNumber();
        $queryArray['order_id'] = $gadget->getId();
        $tracking = Mage::getModel('shippinge/ecom')->ecomexpressTracking($queryArray);
        print_r($tracking);
    }

    //Multiple shipments tracking status for gadgets
    //Mahesh Gurav
    //06th Jan 2018
    public function gadgetstrackingAction(){
        // echo "Here";
        $gadgets = Mage::getModel('gadget/request')->getCollection();
        $gadgets->addFieldToSelect('id');
        $gadgets->addFieldToFilter('status','processing');
        $gadgets->getSelect()->limit(10);
        $order_ids = array();
        foreach($gadgets as $gadget){
            $order_ids[] = $gadget->getId();
        }
        $queryArray['awb'] = '';
        $queryArray['order_id'] = implode(",", $order_ids);
        // $queryArray['order_id'] = '200088587,200088585,200088581';
        $shipment_tracking_details = Mage::getModel('shippinge/ecom')->multiShipmentTracking($queryArray);
        print_r($shipment_tracking_details);
    }


    //Multiple shipments tracking status for orders
    //Mahesh Gurav
    //06th Jan 2018
    public function orderstrackingAction(){
        // echo "Here";
        $orders = Mage::getModel('sales/order')->getCollection();
        $orders->addFieldToSelect('increment_id');
        $orders->addFieldToFilter('status','shipped');
        $orders->setOrder('increment_id', 'desc');
        $orders->getSelect()->limit(10);
        $order_ids = array();
        foreach($orders as $order){
            $order_ids[] = $order->getIncrementId();
        }
        print_r($order_ids); 
        $queryArray['awb'] = '';
        $queryArray['order_id'] = implode(",", $order_ids);
        // $queryArray['order_id'] = '200088587,200088585,200088581';
        $shipment_tracking_details = Mage::getModel('shippinge/ecom')->multiShipmentTracking($queryArray);
        print_r($shipment_tracking_details);
    }

    //SYG corporate user get 
    public function corporatePriceAction(){
        $params = $this->getRequest()->getParams();

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

        echo json_encode(array('status' => $status, 'price' => number_format($price)));
        exit;
    }

    //save request for corporate user 
    //Mahesh Gurav
    //31st Jan 2018
    public function ticketsubmitAction(){
        $postData = Mage::app()->getRequest()->getPost();
        $postData = $this->getRequest()->getParams();
        
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($postData) { 
        $_session = Mage::getSingleton('core/session')->getSygform();
        
        // print_r($postData); 
        // print_r($_session); 
        // exit;

        // echo "price = ".$product_price;

        $laptop_options = Mage::helper('gadget')->getLaptopOptions();
        $options_on_syg = array();
        $html = '';

        if(!empty($_session['corporate_options']['Other Brand Name'])){
            
            $options_on_syg['Other Brand Name'] = $_session['corporate_options']['Other Brand Name'];
            $html .= '<tr><td valign="top" align="left" style="padding: 0 0 10px 0;"><h2 style="font-family:Arial,Helvetica,sans-serif;font-size:16px;margin:0;padding:0;color:#ffffff;margin:0 0 2px 0">';
            $html .= 'Other Brand Name';
            $html .= '</h2><p style="margin:0;padding:0;font-size:13px;font-family:Arial,Helvetica,sans-serif;color:#ffffff">';
            $html .= $_session['corporate_options']['Other Brand Name'];
            $html .= '</p></td></tr>'; 
        }

        foreach ($laptop_options as $options) {
            if(in_array($options['option_id'], array_keys($postData['options']))){
                
                $html .= '<tr><td valign="top" align="left" style="padding: 0 0 10px 0;"><h2 style="font-family:Arial,Helvetica,sans-serif;font-size:16px;margin:0;padding:0;color:#ffffff;margin:0 0 2px 0">';
                $html .= $options['default_title'];
                $html .= '</h2><p style="margin:0;padding:0;font-size:13px;font-family:Arial,Helvetica,sans-serif;color:#ffffff">';
                if($options['option_type'] == 'field'){
                    $options_on_syg[$options['default_title']] = $postData['options'][$options['option_id']];
                    $html .= $postData['options'][$options['option_id']];
                }else{
                    foreach ($options['values'] as $key => $value) {
                        if( $postData['options'][$options['option_id']] == $key){
                            $options_on_syg[$options['default_title']] = $value;
                            $html .= $value;
                        }
                    }
                }
                $html .= '</p></td></tr>';
            }

        }//end of foreach    
            
            $optionsArrayJson = json_encode($options_on_syg);
            $product_price = $postData['product_price'];
            $product_price = preg_replace('/[^A-Za-z0-9\-]/', '', $product_price);
            $product_price = number_format($product_price);
                // echo "<pre>"; print_r($postData); exit;
            $product = Mage::getModel('catalog/product')->load($postData['product_id']);
            $product_name = $options_on_syg['Brand']." ".$options_on_syg['Processor'];
                try { 
                    $baseUrl = Mage::getBaseUrl();
                    $post['name'] = $product_name; 
                    $post['sku'] = $product->getSku();      
                    $post['proname'] = $product_name;       
                    $post['brand'] = $product->getName();         
                    $post['price'] = $product_price;       
                    $post['switch'] = $html;    
                    $post['option'] = $html;    
                    $post['options'] = $optionsArrayJson;    
                    $post['pincode'] = $postData['postcode'];       
                    $post['city'] = $postData['city'];                           
                    $post['email'] = $customer->getEmail();       
                    $post['email1'] = 'mailto:'.$customer->getEmail();       
                    $post['mobile'] = $customer->getMobile();
                    $post['image'] = (string) Mage::helper('catalog/image')->init($product, 'image'); 

                    $post['bank_customer_name'] = $postData['customer_name'];      
                    $post['bank_name'] = $postData['bank_name'];    
                    $post['bank_ifsc'] = $postData['ifsc_code'];      
                    $post['bank_account_no'] = $postData['account_number']; 
                         
                    $post['serial_number'] = $options_on_syg['Serial'];   

                    if(!empty($postData['internal_order_id'])){
                        $post['internal_order_id'] = $postData['internal_order_id'];      
                    }else{
                        Mage::getSingleton('core/session')->addError('Please enter Internal Order Id');
                        $base_response = Mage::getBaseUrl()."gadget/index/failure/";
                        echo json_encode(array('status'=>'0','base_url'=>$base_response));
                        exit;                 
                    }

                    // $post['imei_no'] = '';
                    if(!empty($postData['default_address'])){

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
                            $post['mobile'] = $customer->getMobile();
                        }
                    }   
                    
                    if(!empty($postData['street']) && !empty($postData['postcode']) && !empty($postData['city'])){
                        $street = strip_tags($postData['street']);
                        
                        $post['name'] = $customer->getFirstname()." ".$customer->getLastname();
                        $post['email'] = $customer->getEmail();
                        $post['mobile'] = $customer->getMobile();
                        $post['pincode'] = $postData['postcode'];
                        $post['city'] = $postData['city'];
                        // print_r($postData);
                        
                        $serviceable = Mage::getModel('operations/serviceablepincodes')->getPincodeData($postData['postcode']);
                        if(!empty($serviceable) && $serviceable['ecom_qc'] == 1){
                        $regionId =  $serviceable['region_id'];
                        $countryCode =  $serviceable['country'];
                        $city =  $serviceable['city'];
                        $post['address'] = $street.", ".$city.", ".$regionId.", ".$countryCode;

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
                            // Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/failure/")->sendResponse();
                            $base_response = Mage::getBaseUrl()."gadget/index/failure/";
                            echo json_encode(array('status'=>'0','base_url'=>$base_response));
                            exit; 
                        }
                    
                        
                        try{
                            $address->save();
                        }catch (Exception $e) {
                            // Zend_Debug::dump($e->getMessage());
                        }
                    }
                    
                    
                    $model = Mage::getModel("gadget/request") 
                    ->addData($post)
                    ->save();
                    $post['order_id'] = $model->getId();     

                    Mage::getSingleton('core/session')->setOrderId($post['order_id']);
                    
                    $postObject = new Varien_Object();
                    $postObject->setData($post); 

                    // $emailId = $post['email'].','.Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
                    $bccAdd = explode(",",Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT));
                    $recipients = $customer->getEmail();       

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
                    
      
                    
                    // Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/success/")->sendResponse();
                    $base_response = Mage::getBaseUrl()."gadget/index/success/";
                    echo json_encode(array('status'=>'1','base_url'=>$base_response));
                    exit; 
                    
                    
                } 
                catch (Exception $e) {
                    // Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/failure/")->sendResponse();

                    $base_response = Mage::getBaseUrl()."gadget/index/failure/";
                    echo json_encode(array('status'=>'0','base_url'=>$base_response));
                    exit; 
                }

            }//end if

    }//function ends



    /**************** Start code for syg order confirmation ********************/
    function pluralize( $count, $text ) 
    { 
        return $count . ( ( $count == 1 ) ? ( " $text" ) : ( " ${text}s" ) );
    }


    public function orderconfirmAction()
    {
       // echo "dsfds";die;
       if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams(); //echo '<pre>', print_r($data);exit;
            // below 2 lines are added by pradeep sanku on the 26th June 2015 as a part of not showing the date value in the database
           
            
            if(!empty($data))
            {
              if($data['orderid'] != '' && $data['userid'] != '')
                {                            
                    //set session.
                    $orderdata = '';
                    $orderid = $data['orderid'];
                    $userid = $data['userid'];                    
                    $orderdata = Mage::getModel('gadget/request')->load($orderid);
                                //->addFieldToFilter('cid',$cid);
                    $orderdata = $orderdata->getData();
                    //echo "Che:".$orderdata['is_order_confirmed'];                                
                    //echo '<pre>', print_r($orderdata);exit;   

                    $orderid = $data['orderid'];
                    $orderdetails = Mage::getModel('gadget/request')->load($orderid);
                    //echo $orderdetails->getCreatedAt();
                    $to_time = strtotime($orderdetails->getCreatedAt());
                    $from_time = strtotime(date('Y-m-d H:i:s'));
                    $minutes = round(abs($to_time - $from_time) / 60);
                    $hours = $minutes/60;
                    //echo  round($hours). " hrs";
                    if(round($hours) > 24){
                         echo json_encode(array("status" => false, "message" => "Oops! Order confirmation time limit is expired. Please hurry for next orders."));
                         exit;   
                    }                                                                                                                                              
                    try {
                           if(!empty($orderdata))
                           {
                             if($orderdata['is_order_confirmed'] != '' && $orderdata['is_order_confirmed'] == 'Yes')
                               {                            
                                echo json_encode(array("status" => false, "message" => "Oops! This order is already confirmed by someone else."));
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
                                $templateId = 27;
                             
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
                                Mage::log('Order Confirm User Email ID : '.$customerdetails['email'], null, 'syg-retailers.log', true);

                                //code for get device id for users.
                                $user_device_id = '';
                                $user_device_id_data = array();
                                $user_device_id_data = Mage::getModel('neo_notification/fcmpush')
                                                ->getCollection()->addFieldToSelect('device_Id')                                
                                                ->addFieldToFilter('user_id',$customerdetails['entity_id'])
                                                ->addFieldToFilter('device_type','android');
                                                //->getColumnValues('device_Id');
                                $user_device_id_data = $user_device_id_data->getData();

                                foreach($user_device_id_data as $data)
                                {
                                   $user_device_id = $data['device_id'];
                                }                                
                                
                                if($user_device_id != '')
                                {   
                                    Mage::log('Order Confirm User Device ID Found'.$user_device_id, null, 'syg-retailers.log', true);
                                    $message = 
                                    "Dear ".$customerdata['firstname'].",\nYou have agreed to pick up the following device. Kindly ensure the same is picked up within the next 24 Hours. \nOrder Number – " .$orderid. "\nDevice – " .$orderdetails['proname']. "\nExchange Price to be Paid – INR ".str_replace('?','',$orderdetails['price'])."\nPhone No - " .$customerdata['mobile']."\nAddress - " .$orderdetails['address']."";

                                   $notification_data = array(
                                        'title' => $message,
                                        'type' => 'syg_link_confirm',
                                        'user_id' => $customerdata['entity_id'],
                                        'order_id' => $orderid                     
                                    );
                                    $ios = 'android';                    
                                    $fcm_response = Mage::helper('neo_notification')->sygFCMNotification($user_device_id, $productData, $notification_data, $ios);                                
                                }
                                else
                                {
                                   Mage::log('Order Confirm User Device ID Not Found', null, 'syg-retailers.log', true);   
                                }   
                                echo json_encode(array("status" => true, "message" => "Thank you for confirming the pick up request.The device details has been shared with you."));
                                exit;                                                                 
                               }
                           }
                            else
                            {
                                echo json_encode(array("status" => false, "message" => "Order data not found."));
                                Mage::log($e->getMessage());
                                exit;                 
                            }

                    } catch(Exception $e) {
                    echo json_encode(array("status" => false, "message" => $e->getMessage()));
                    Mage::log($e->getMessage());
                    exit;                
                    }
                }
                else
                {
                    echo json_encode(array("status" => flase, "message" => "Please enter valid proper data."));
                    Mage::log($e->getMessage());
                    exit;                 
                }                   
            }
            else
            {
                echo json_encode(array("status" => false, "message" => "Something went wrong."));
                Mage::log($e->getMessage());
                exit;                 
            }
        }
    }

    public function sygorderAction(){
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
        }else{
            $this->loadLayout();   
            $this->getLayout()->getBlock("head")->setTitle($this->__("SYG Order List"));        
            $this->renderLayout();    
        }
         
    }

    public function sygorderviewAction(){
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
        }else{
            $this->loadLayout();           
            $this->getLayout()->getBlock("head")->setTitle($this->__("SYG Order View"));   
            $this->renderLayout(); 
        }
    }   

    /*
    * @date : 15th Mar 2018
    * @author : Sonali Kosrabe
    * @purpose : To sell back product to EB
    */

    public function sellBackAction(){
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
        }else{
            $order_id = $this->getRequest()->getParam('id');
            
            $orderdetails = Mage::getModel('gadget/request')->load($order_id);
           // echo "<pre>";print_r($orderdetails);die;
            if($orderdetails->getAwbNumber() != ''){
                Mage::getSingleton("core/session")->addSuccess(Mage::helper("gadget")->__("Your request has been already processed."));
                $this->_redirect("*/*/sygorder");
            }
            $this->loadLayout();   
            $this->getLayout()->getBlock("head")->setTitle($this->__("SYG Sell Back"));        
            $this->renderLayout();    
        }
         
    } 

    /*
    * @date : 15th Mar 2018
    * @author : Sonali Kosrabe
    * @purpose : To save sell back request data
    */

    public function sellBackSendDataAction(){
        $postData = $this->getRequest()->getPost();
        //print_r($postData);die;
        //echo Mage::getModel('core/date')->date('Y-m-d H:i:s');die;
        $orderdetails = Mage::getModel('gadget/request')->load($postData['order_id']);
        if($orderDetails['awb_number']!= ''){
            Mage::getSingleton("core/session")->addSuccess(Mage::helper("gadget")->__("Your request has been already processed."));
            $this->_redirect("*/*/sygorder");
        }

        $customerId = Mage::getModel('customer/session')->getCustomer()->getEntityId();
        $customer = Mage::getModel('customer/customer')->load($customerId);


        if($postData['address_id']){
            
            $postcode = $postData['postcode'];
            $city = $postData['city'];
            $street = $postData['street'];
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
                Mage::getSingleton("core/session")->addError(Mage::helper("gadget")->__('unable to save address.'));
                $this->_redirect("*/*/sygorder");

            }
        }

        
        $sdata = array('confrm_by_retailer_name_in_bank'=>$postData['confrm_by_retailer_name_in_bank'],
                        'confrm_by_retailer_bank_name'=>$postData['confrm_by_retailer_bank_name'],
                        'confrm_by_retailer_acct_number'=>$postData['confrm_by_retailer_acct_number'],
                        'confrm_by_retailer_ifsc_code'=>$postData['confrm_by_retailer_ifsc_code'],
                        'confrm_by_retailer_address_id'=>$postData['confrm_by_retailer_address_id'],
                        'status'=>'sellback_request',
                        'retailers_sellback_at'=>Mage::getModel('core/date')->date('Y-m-d H:i:s'));
        $orderdetails->addData($sdata);

        try{
            $orderdetails->save();

            //$this->sellBackSygOdrer($postData['order_id']);
            $ecomData = Mage::getModel('gadget/request')->sellBackSygOdrer($postData['order_id'],$customer->getId());
            if($ecomData['status']){
                Mage::getSingleton("core/session")->addSuccess(Mage::helper("gadget")->__($ecomData['message']));
            }else{
                Mage::getSingleton("core/session")->addError(Mage::helper("gadget")->__($ecomData['message']));
            }
            
        }catch(Exception $e){
            Mage::getSingleton("core/session")->addError(Mage::helper("gadget")->__($e->getMessage()));
        }

        $this->_redirect("*/*/sygorder");

    }

    /*
    * @date : 9th Mar 2018
    * @author : Sonali Kosrabe
    * @purpose : To create shipment for SYG orders
    */
    public function sellBackSygOdrer($orderId){
           // $productId = $this->getRequest()->getParam('productId');
            $gadget = Mage::getModel('gadget/request')->load($orderId);
            $response = Mage::getModel('shippinge/ecom')->sendRvpRequestGadget($orderId);
            $message = trim($response['message'],',');
            $awb_response = serialize($response);
            $awb_request_date = date('Y-m-d H:i:s');
            $awb_number = $response['airwaybill_number'];

            if($response['success'] == 0){
                $data['awb_number'] = $gadget->getAwbNumber();
                $data['status'] = $gadget->getStatus();
                $data['message'] = "Error : ".$message." ". $response['result'][0]['reason'];
                $data['success'] = $response['success'];
                Mage::getSingleton("core/session")->addError(Mage::helper("gadget")->__($data['message']));
            }else{
                $gadget->setAwbNumber($awb_number);
                $gadget->setAwbRequestDate($awb_request_date);
                $gadget->setAwbResponse($awb_response);
                $gadget->setStatus('processing');
                $gadget->save();

                $data['awb_number'] = $gadget->getAwbNumber();
                $data['status'] = $gadget->getStatus();
                $data['message'] = $message;
                $data['success'] = $response['success'];
                
                // Check if any customer is logged in or not
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $customerName = Mage::getSingleton('customer/session')->getCustomer()->getName();
                }else{
                    $customerName = '';
                }

                $templateId = 28;
                $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
                $template_variables['customerName'] = $customerName;
                $template_variables['orderNo'] = $gadget->getId();
                $template_variables['tracking_name'] = "ECOM";
                $template_variables['tracking_number'] = $awb_number;

                $storeId = Mage::app()->getStore()->getId();
                $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
                $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
                $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
                // Set sender information           
                $sender = array('name' => $senderName,'email' => $senderEmail);
                $recepientEmail = array('sonalik2788@gmail.com'); //
                
                $subject = $emailTemplate->getTemplateSubject();
                $z_mail = new Zend_Mail('utf-8');
                $z_mail->setBodyHtml($processedTemplate)
                ->setSubject($subject)
                ->setFrom($senderEmail, $senderName);
                $z_mail->addTo($recepientEmail);
                //try{
                $z_mail->send();
                return true;

                   // Mage::getSingleton("core/session")->addSuccess(Mage::helper("gadget")->__("Order is processed for shipment with AWB number ".$data['awb_number']));
                   // $this->_redirect("*/*/sygorder");
                /*}catch(Exception $e){
                    Mage::getSingleton("core/session")->addSuccess(Mage::helper("gadget")->__($e->getMessage()));
                }*/
            }
            //$this->_redirect('*/*/sygorder');            
        }
    /**************** Start code for syg order confirmation ********************/    
    /************** promo code static value **********************************/
    public function promostaticpriceAction(){
        $request = $this->getRequest()->getParams();
        $promo_code = strtoupper($request['promo_code']);
        $processor = $request['processor']; 
        // $_session = Mage::getSingleton('core/session')->getSygform();
        $sygForm = Mage::getSingleton('core/session')->getSygform();
        $old_price = str_replace("₹", "", $sygForm['product_price']);
        $old_price = str_replace(",", "", $old_price);
        $old_price = str_replace("Rs.", "", $old_price);
        $old_price = str_replace("Rs", "", $old_price);

        $old_price = (int) $old_price;
        $result = array();
        if(!empty($promo_code) && !empty($processor)){
            $status = '0';
            $promo_price = Mage::getModel('gadget/gadget')->getPromoPrice($promo_code,$processor);
            
            if($promo_price > 0 && $sygForm['promo_code'] == ''){
                $price = $promo_price + $old_price;
                $status = 1;
                $message = 'Promo code applied successfully';
                $sygForm['product_price'] = 'Rs.'.$price;
                $sygForm['promo_code'] = $promo_code;
                $sygForm = Mage::getSingleton('core/session')->setSygform($sygForm);

            }else{
                $message = 'Invalid promo code';
            }

        }

        echo json_encode(array('status' => $status, 'message'=>$message, 'price' => $price));
    }
    //*************************** function ends ***********************

    public function removepromoAction(){
        $removed = false;
        $status = 0;
        $sygForm = Mage::getSingleton('core/session')->getSygform();
        $old_price = str_replace("₹", "", $sygForm['product_price']);
        $old_price = str_replace(",", "", $old_price);
        $old_price = (int) str_replace("Rs.", "", $old_price);
        $promos = array('gadget/promocode/promo_code_500','gadget/promocode/promo_code_1000');
        foreach ($promos as $key => $value) {
            if(Mage::getStoreConfig($value) == $sygForm['promo_code']){
                $addon_amount = explode("promo_code_", $value);
                $message = "Promo code ".Mage::getStoreConfig($value)." removed successfully";
                // print_r($addon_amount);
                $amount_minus = (int) end($addon_amount);

                $new_price = $old_price - $amount_minus;
                // echo $new_price ." = ". $old_price." - ".$amount_minus;
                // echo $new_price;
                $sygForm['promo_code'] = '';
                $sygForm['product_price'] = 'Rs.'.$new_price;
                $removed = true;
            }
        }
        if($removed){
            Mage::getSingleton('core/session')->setSygform($sygForm);
            $status = 1;
        }

        echo json_encode(array('status' => $status, 'message'=>$message, 'price' => $new_price));
    }
}  
