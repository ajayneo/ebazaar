<?php
class Neo_Custom_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

	public function checkPincodeStateAction(){
		
		$response = array();
		$availableflag = false;		

		$data = 'Available in this location.';
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$pincode = $this->getRequest()->getParam('pincode');
		
		$query = 'SELECT entity_id,state_code FROM city_pincodes WHERE pincode="'.$pincode.'"';
		$result = $connection->fetchRow($query);		
		if(isset($result['entity_id'])){
			$availableflag = true;
		}

		if($availableflag){				
			$query_region = 'SELECT region_id,default_name FROM directory_country_region WHERE code="'.$result['state_code'].'"';		
			$result_region = $connection->fetchRow($query_region);		
			$response['status'] = $result_region['region_id'].'$$$$'.$result['state_code'].'$$$$'.$result_region['default_name'];			
		}else{
			$response['status'] = 'ERROR';
		}	
		/* Return State Id & State Code */
		echo $response['status'];
    }
	
	public function checkPincodeStateCitiesAction(){
		$response = array();
		$availableflag = false;		

		$data = 'Available in this location.';
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$pincode = $this->getRequest()->getParam('pincode');
		
		$query = 'SELECT city FROM city_pincodes WHERE pincode="'.$pincode.'"';
		$result = $connection->fetchRow($query);			
		
		if(isset($result['city'])){
			$availableflag = true;
		}

		if($availableflag){				
			$response['status'] = $result['city'];
		}else{
			$response['status'] = 'ERROR';
		}	
		/* Return City Name */
		echo $response['status'];
	}

	//@created for hourly emails of laptops stock
	//@auth: Mahesh Gurav
	//@date: 06 Jun 2018
	public function laptopslistAction(){
		date_default_timezone_set('Asia/Calcutta');
		$collection = Mage::getModel('catalog/product')
	     ->getCollection()
	     // ->addAttributeToSelect(array('product_id','sku','name','price','qty'))
	     ->addAttributeToSelect('*')
	     ->joinField('active',
	         'catalog_product_entity_int',
	         'value',
	         'entity_id=entity_id',
	         'attribute_id=96 and value=1',
	         'left'
	     )
	     ->joinField('qty',
	         'cataloginventory/stock_item',
	         'qty',
	         'product_id=entity_id',
	         '{{table}}.stock_id=1 and {{table}}.is_in_stock=1',
	         'inner'
	     )
	     ->joinField('tax',
	         'tax_class',
	         'class_name',
	         'class_id = `tax_class_id`',
	         null,
	         'left'
	     );
	     
	     //exclude sell yourgadget demo products
	     $collection->addAttributeToFilter('attribute_set_id', array('neq' => 48))
	     //only laptops
	    ->addAttributeToFilter('attribute_set_id', array('eq' => 9));
	    
	    //category model set
	    $store_id = Mage::app()->getStore()->getId();
	    $_cat = Mage::getModel('catalog/category')->setStoreId($store_id);
	    	
// 1) All Pre owned Laptop
// 2) All Brand REF Laptop
// 3) All Open box Laptop  
	    foreach ($collection as $product) {
	    	$categories = $product->getCategoryIds();
		    $category_name2 = '';
		    if(isset($categories[1])){
		        $parent_cat2 = $categories[1];
		        $cat2 = $_cat->load($parent_cat2);
		        $category_name2 = $cat2->getName();
		    }
		    $product_qty = (int) $product->getQty();
	    
		    $product_price = (int) round(Mage::helper('tax')->getPrice($product, $product->getFinalPrice()));
		    $product_price = number_format($product_price);
		    $product_name = trim($product->getName());
		    $sku = $product->getSku();
		    
		    if($category_name2 == "Pre-Owned Laptops"){
		    	$preowned[] = array($sku,$product_name,$category_name2,$product_price,$product_qty);
		    }else if($category_name2 == "Brand Refurbished Laptops"){
		    	$refurb[] = array($sku,$product_name,$category_name2,$product_price,$product_qty);
		    }else if($category_name2 == "Open Box Laptops"){
		    	$openbox[] = array($sku,$product_name,$category_name2,$product_price,$product_qty);
		    }else{
		    	$other[] = array($sku,$product_name,$category_name2,$product_price,$product_qty);
		    }
	    }
     	
     	$header = array('SKU','PRODUCT NAME','CATEGORY','PRICE','QTY'); 
		$filename = "laptops_stock.csv"; //same file name for dialy report
		$file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;
		$filepath = fopen($file,"w");
		//same header as email body
		fputcsv($filepath, $header);
     	
     	
     	$laptops_str1 ='';
		$laptops_str1 = '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:5px 0 0 0;">';
		$laptops_str1 .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">SKU</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">Product Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">Category Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">Total Price(incl tax)</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">Qty</th></tr>';
     	foreach ($preowned as $k => $v) {
     		fputcsv($filepath, $v);
     		 $laptops_str1 .= '<tr>';
     		 foreach ($v as $i => $o) {
     		 	 $laptops_str1 .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">'.$o.'</td>';
     		 }
     		 $laptops_str1 .= '</tr>';
     	}

     	foreach ($refurb as $k => $v) {
     		fputcsv($filepath, $v);
     		 $laptops_str1 .= '<tr>';
     		 foreach ($v as $i => $o) {
     		 	 $laptops_str1 .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">'.$o.'</td>';
     		 }
     		 $laptops_str1 .= '</tr>';
     	}
     	foreach ($openbox as $k => $v) {
     		fputcsv($filepath, $v);
     		 $laptops_str1 .= '<tr>';
     		 foreach ($v as $i => $o) {
     		 	 $laptops_str1 .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">'.$o.'</td>';
     		 }
     		 $laptops_str1 .= '</tr>';
     	}
     	foreach ($other as $k => $v) {
     		fputcsv($filepath, $v);
     		 $laptops_str1 .= '<tr>';
     		 foreach ($v as $i => $o) {
     		 	 $laptops_str1 .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 5px;">'.$o.'</td>';
     		 }
     		 $laptops_str1 .= '</tr>';
     	}
     	fclose($filepath);
     	$laptops_str1 .= '</table>';
     		

     	$template_id = "navisionexport_hourly_email_template";
    	$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
     	$laptop = array('msg' =>$laptops_str1);
        $subject = "Stock Report for Laptops ".date('Y-m-d h:m a');
        $template = $emailTemplate->getProcessedTemplate($laptop);

       $reciepient = array('hari@electronicsbazaar.com','kushlesh@electronicsbazaar.com','rajkumar@electronicsbazaar.com','vipul.g@electronicsbazaar.com','akhilesh@electronicsbazaar.com','omkar.g@kaykay.co.in','jayesh.b@kaykay.co.in','prasad.k@kaykay.co.in','sagar.s@kaykay.co.in','prakash@kaykay.co.in','ketaki@electronicsbazaar.com','chanchal@kaykay.co.in','prashantmotwani@kaykay.co.in','daniel.d@electronicsbazaar.com','roy.g@electronicsbazaar.com','vicky.p@electronicsbazaar.com','birendra@electronicsbazaar.com','raj.k@electronicsbazaar.com','ravi.k@electronicsbazaar.com','chandan.p@electronicsbazaar.com','parth@electronicsbazaar.com;','rizwan@electronicsbazaar.com','vivek.s@electronicsbazaar.com','santosh.n@electronicsbazaar.com');
     	$bcc = array('support@electronicsbazaar.com');

     	// $reciepient = array('web.maheshgurav@gmail.com');
     	try{
     		
     		//prepare file attachment
            $attachment = file_get_contents($file);
            $attach = new Zend_Mime_Part($attachment);
            $attach->type = 'application/csv';
            $attach->disposition = Zend_Mime::DISPOSITION_INLINE;
            $attach->encoding    = Zend_Mime::ENCODING_8BIT;
            $attach->filename    = $filename;

     		$z_mail = new Zend_Mail('utf-8');
	        $z_mail->setBodyHtml($template)
	        ->setSubject($subject)
	        ->addTo($reciepient)
	        ->addBcc($bcc)
	        ->setFrom($senderEmail, $senderName);
	        $z_mail->addAttachment($attach);
	        
	        $z_mail->send();

     	}catch(Exception $e){
     		Mage::log($e->getMessage());
     	}
     	exit();

	}    
}