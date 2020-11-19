<?php
class Neo_Gadget_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	** products (type=gadgets, attribute_set=gadget, brand=any)
	**
	**
	**/

    public function getBrandedGadgetsLaptopCollection($brand,$code){
        
        Mage::log("sellurgadget.log", null, 'test log');
        $_product = Mage::getModel('catalog/product');
        if($code == 'Mobile'){
            $attr = $_product->getResource()->getAttribute('gadget');
            if ($attr->usesSource()) {
                $gadgetId = $attr->getSource()->getOptionId($brand); 
            }
        }else if($code == 'Laptops' || $code == 'Laptop'){
            $attr = $_product->getResource()->getAttribute('gadget_laptop');
            if ($attr->usesSource()) {
                $gadgetId_laptop = $attr->getSource()->getOptionId($brand); 
            }
        }
        
        /*$attr_brand = $_product->getResource()->getAttribute('brands');
        if ($attr_brand->usesSource()) {
            $brandId = $attr_brand->getSource()->getOptionId($brand); 
        }*/
        if($code == 'Laptops'){
            $correctcode = 'Laptop';
        }else{
            $correctcode = $code;
        }
        $attr_code = $_product->getResource()->getAttribute('product_department');
        if ($attr_code->usesSource()) {
            $codeId = $attr_code->getSource()->getOptionId($correctcode); 
        }


        
        $products = $_product->getCollection()
        ->joinField('qty','cataloginventory/stock_item','qty','product_id=entity_id','{{table}}.stock_id=1','left')
        ->addAttributeToFilter('qty', array("gt" => 0))
        ->addAttributeToFilter('status', array('neq' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED))
        //->addAttributeToFilter('visibility', array('eq' => 4))
        ->addAttributeToFilter('attribute_set_id',array('eq' => 48))
        ->addAttributeToSelect('image')
        ->addAttributeToSelect('*')  
        ->addFieldToFilter('price', array('gt' => '0.00'))  
        //->addAttributeToFilter('brands', $brandId)
        ->addAttributeToFilter('product_department', array('eq' => $codeId));
        

        Mage::log("sellurgadget.log", null, "sellurgadget.log");
        if($code == 'Mobile' && $gadgetId ){
            $products->addAttributeToFilter('gadget', $gadgetId);

        }else if($code == 'Laptop' || $code == 'Laptops'){
            //$products->addAttributeToFilter('gadget_laptop', $gadgetId_laptop);
            $products->addAttributeToFilter('gadget_laptop_value', array('eq' => 'Other')); 

        }

        $products->addAttributeToSort('created_at', 'desc');  

        return $products;
    }

	public function getBrandedGadgetsCollection($brand,$code){
        
        $_product = Mage::getModel('catalog/product');

        Mage::log("sellurgadget.log", null, 'test 2 log');
        
        if($code == 'Mobile'){
            //echo "<br>mobile";
            $attr = $_product->getResource()->getAttribute('gadget');
            if ($attr->usesSource()) {
                $gadgetId = $attr->getSource()->getOptionId($brand); 
            }
            //echo "mobile id ".$gadgetId;    
        }else if($code == 'Laptop' || $code == 'Laptops'){
            //echo "<br>laptop";
            $attr = $_product->getResource()->getAttribute('gadget_laptop');
            if ($attr->usesSource()) {
                $gadgetId_laptop = $attr->getSource()->getOptionId($brand); 
            }

            //echo "<br>laptop id ".$gadgetId;
        }
        

        
        /*$attr_brand = $_product->getResource()->getAttribute('brands');
        if ($attr_brand->usesSource()) {
            $brandId = $attr_brand->getSource()->getOptionId($brand); 
        }*/

        if($code == 'Laptops'){
            $correctcode = 'Laptop';
        }else{
            $correctcode = $code;
        }

        $attr_code = $_product->getResource()->getAttribute('product_department');
        if ($attr_code->usesSource()) {
            $codeId = $attr_code->getSource()->getOptionId($correctcode); 
        }
        
        $products = $_product->getCollection()
        ->joinField('qty','cataloginventory/stock_item','qty','product_id=entity_id','{{table}}.stock_id=1','left')
        ->addAttributeToFilter('qty', array("gt" => 0))
        ->addAttributeToFilter('status', array('eq' => 1))
        //->addAttributeToFilter('visibility', array('eq' => 4))
        ->addAttributeToFilter('attribute_set_id',array('eq' => 48))
        ->addAttributeToSelect('image')
        ->addAttributeToSelect('*')  
        //->addAttributeToFilter('brands', $brandId)
        ->addAttributeToFilter('product_department', array('eq' => $codeId));
        

        if($code == 'Mobile' && $gadgetId ){
            $products->addAttributeToFilter('gadget', $gadgetId);

        }else if($code == 'Laptop' && $gadgetId_laptop){
            //$products->addAttributeToFilter('gadget_laptop', $gadgetId_laptop);
            $products->addAttributeToFilter('gadget_laptop_value', array('eq' => 'Other')); 

        }

        //$products->getFirstItem();

        $products->addAttributeToSort('created_at', 'desc');  

        // echo "<pre>";
        // print_r( $products->getData()); 
        // die();

        return $products;

    }


    public function getProductOptions($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        return $proOption = $product->getOptions();
    }

    public function sendEmail($receiver,$subject,$message){
        $reciepient = $sender;
    
        $template_id = "navisionexport_hourly_email_template";
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
        $template_variables = array('msg' =>$message);
        $storeId = Mage::app()->getStore()->getId();
    
        $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        try {
            $z_mail_openbox = new Zend_Mail('utf-8');
            $z_mail_openbox->setBodyHtml($processedTemplate)
                ->setSubject($subject)
                ->addTo($receiver)
                // ->addBcc($receiver)
                ->setFrom($senderEmail, $senderName);
            $z_mail_openbox->send();
        }catch(Exception $e){
            echo $e->getMessage();
        }    
    
    }

    
    public function validateData($gadgetData){
        $error_message = array();
        if(empty($gadgetData['brand'])){
            $error_message[] = "Gadget brand name is required.";
        }

        if(!empty($gadgetData['brand']) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $gadgetData['brand'])){
            $error_message[] = "One or more special characters present in gadget brand.";   
        }

        if(empty($gadgetData['model'])){
            $error_message[] = "Gadget model is required.";
        }

        if(!empty($gadgetData['model']) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $gadgetData['mode'])){
            $error_message[] = "One or more special characters present in gadget model.";   
        }

        return $error_message;
    }

    public function getCustomOptions($product_id){
        if($product_id){
            $_product = Mage::getModel('catalog/product')->load($product_id);
        }
    }

    /* 
    @author : Sonali Kosrabe
    @date : 22-12-17
    @purpose : To get Gadgets Status Array
    */
    
    public function getGadgetStatusOptions($status = NULL){

        $statusOptionsArray = array('new' => 'Request Submitted', 
                                    'approved' => 'Approved', 
                                    'confirmed_by_retailer'=>'Confirmed',
                                    'sellback_request'=>'Sell-Back Request',
                                    'processing' => 'In Pickup Process', 
                                    'pickedup' => 'Picked Up',
                                    'payment_pending'=>'Payment Pending',
                                    'payment_done'=>'Payment Done');
        if($status == NULL){
            return $statusOptionsArray;    
        }else{
            return $statusOptionsArray[$status];
        }
        
    }


    public function getLaptopOptions(){
        $options = array();
        $processors = array(
                1121=>'Atom',
                1122=>'AMD',
                1123=>'Core i3',
                1124=>'Core i5',
                1125=>'Core i7');

        $ram = array(
                3195=>'8 GB and above',
                3196=>'Below 8 GB');

        $charger = array(
                3233=>'Yes',
                3234=>'No');

        $brands = array(
                2121=>'Apple',
                2122=>'Dell',
                2123=>'HP',
                2124 =>'Lenovo');

        $options[0]['option_type'] = 'Select';
        $options[0]['default_title'] = 'Brand';
        $options[0]['option_id'] = 5116; 
        $options[0]['values'] = $brands;

        $options[1]['option_type'] = 'Select';
        $options[1]['default_title'] = 'Processor';
        $options[1]['option_id'] = 5115; 
        $options[1]['values'] = $processors;

        $options[2]['option_type'] = 'Select';
        $options[2]['default_title'] = 'RAM';
        $options[2]['option_id'] = 1286; 
        $options[2]['values'] = $ram;

        $options[3]['option_type'] = 'Radio';
        $options[3]['default_title'] = 'Charger';
        $options[3]['option_id'] = 1311; 
        $options[3]['values'] = $charger;

        $options[4]['option_type'] = 'field';
        $options[4]['default_title'] = 'Serial';
        $options[4]['option_id'] = 1289; 
        $options[4]['values'] = '';


        $options[5]['option_type'] = 'field';
        $options[5]['default_title'] = 'Other Brand Name';
        $options[5]['option_id'] = 1290; 
        $options[5]['values'] = '';

        return $options;
    }

}
	 