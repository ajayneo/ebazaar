<?php 
Class Neo_Operations_IndexController extends Mage_Core_Controller_Front_Action{
	/*  @author :Sonali Kosrabe
		@purpose : To correct pincode, city, state, country
		@date : 24-oct-2017
	*/
	public function customerUpdateAction(){
		$customers = Mage::getModel('customer/customer')->getCollection();//->addFieldToSelect('entity_id');
		$customers = $customers->addFieldToFilter('entity_id',22707);
		$customers = $customers->getColumnValues('entity_id');
		$chucked_customers = array_chunk($customers,1000);
		//print_r($chucked_customers);die;
		foreach ($chucked_customers as $chucked_customer) {
			$start = microtime(true);
			foreach ($chucked_customer as $key => $customerId) {
				$customer = Mage::getModel('customer/customer')->load($customerId);
			
				if($customer->getDefaultShippingAddress()->getPostcode()){
					
					$shippingPincode = $customer->getDefaultShippingAddress()->getPostcode();
					$data = Mage::getModel('operations/serviceablepincodes')->getPincodeData($shippingPincode);
					if($data){

						$customer->setPincode($data['pincode']);
						$customer->setCusCity($data['city']);
						$customer->setCusState($data['region_id']);
						$customer->setCusCountry($data['country']);
						try{
							$customer->save();
							Mage::log("Customer updated : #".$customerId,null,'customer_update.log',true);
						}catch(exceptions $e){
							Mage::log('error in updating customers data for customer :'.$customerId,null,'customer_update_error.log',true);
						}
						
					}else{
						Mage::log('error in updating customers data for customer :'.$customerId,null,'customer_update_error.log',true);
					}
					
				}
			}
			
			$time_elapsed_secs = microtime(true) - $start;
			$first = reset($chucked_customer);
		    $last = end($chucked_customer);
			Mage::log("Time Taken for 1000 - from ".$first." to ".$last." : ".$time_elapsed_secs,null,'customer_update.log',true);
			
		}
		
	}

	/*
	* @author : Sonali Kosrabe
	* @purpose : To update orders city, state from pincodes
	* @dates : 26-oct-2017
	*/

	public function orderAddressUpdateAction(){

		$orders = Mage::getModel('sales/order')->getCollection()->addFieldToSelect('increment_id');
		//$orders = $orders->addFieldToFilter('state','complete');
		$orders = $orders->addFieldToFilter('increment_id',array('in'=>array('200072020','200072026','200072029','200079332','200076992','200077106')));
		$orders = $orders->getColumnValues('increment_id');
		
		$chucked_orders = array_chunk($orders,1000);
		
		foreach ($chucked_orders as $chucked_order) {
			$start = microtime(true);
			foreach ($chucked_order as $key => $orderId) {
				Mage::getModel('operations/serviceablepincodes')->orderAddressUpdate($orderId);
			}
			$time_elapsed_secs = microtime(true) - $start;
			$first = reset($chucked_order);
		    $last = end($chucked_order);

		    Mage::log("Time Taken for 1000 - from ".$first." to ".$last." : ".$time_elapsed_secs,null,'order_address_update.log',true);
		}


	}

	/*
	* @author : Sonali Kosrabe
	* @purpose : To get order billing addresses
	* @dates : 25-oct-2017
	*/
	public function prepaidOrdersBillingAddressAction(){
		
		$to = gmdate("Y-m-d H:i:s");
		$lastTime = strtotime('-3 month');//strtotime($to) - 3600;
		$from = gmdate('Y-m-d H:i:s', $lastTime);

		$orders = Mage::getModel('sales/order')->getCollection()
							->addFieldToSelect('increment_id')
							->addFieldToSelect('grand_total')
							->addFieldToSelect('customer_id')
							->addFieldToSelect('status');

		$orders->getSelect()->join(
		    array('p' => $orders->getResource()->getTable('sales/order_payment')),
		    'p.parent_id = main_table.entity_id',
		    array()
		);



        $orders->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="billing"', array('sfoa.*'));
        //$collection->getSelect()->group('sfoa.city');

        $orders->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
        //$orders->addFieldToFilter('method',array('nin'=>array('banktransfer','cashondelivery','replace','custompayment','purchaseorder')));
        /*echo $orders->getSelect();
        echo "<pre>";
        print_r($orders->getData());
        die;*/
        header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=Customer_Order_BillingInfo.csv');
		$export = fopen('php://output', 'w');

		fputcsv($export, array('Customer Email','Customer Name','Pincode','Street','City','State','Telephone','Gstin')); // write the file header with the column names
		
        foreach ($orders as $order) {
        	$orderData = array($order->getEmail(),
        						$order->getFirstname().' '.$order->getLastname(),
        						$order->getPostcode(),
        						$order->getStreet(),
        						$order->getCity(),
        						$order->getTelephone(),
        						$order->getRegion(),
        						$order->getGstin());
        	fputcsv($export, $orderData);//Add the fields you added in csv header
        }
        //print_r($orderData);

        fclose($export );

	}

	/*
	* @author : Sonali Kosrabe
	* @purpose : To cancel order which are on hold
	* @dates : 25-oct-2017
	*/

	function autoCancelHoldOrderAction(){
		if(!$this->isWeekend()){
			$lastTime = strtotime('-1 day');
			$to = gmdate('Y-m-d H:i:s', $lastTime);
			$orders = Mage::getModel('sales/order')->getCollection()->addFieldToSelect('increment_id');
			$orders->addFieldToFilter('state','holded');
			$orders->addAttributeToFilter('main_table.updated_at', array('to' => $to));
			
			foreach ($orders as $order) {
				$order =  Mage::getModel('sales/order')->load($order->getId());
				Mage::getModel('operations/serviceablepincodes')->orderCancelBackToStock($order);
			}
		}
		
	}

	
	/*
	* @author : Sonali Kosrabe
	* @purpose : To check date is on weekend
	* @dates : 25-oct-2017
	*/
	function isWeekend() {
		return (date('N', strtotime(date('Y-m-d'))) >= 6);
	}

	/*
	* @author : Sonali Kosrabe
	* @purpose : To get delivery date on product details page
	* @dates : 25-oct-2017
	*/
	public function checkPostcodeAction(){
		$pincode = $this->getRequest()->getPost('postcode');
		$method = $this->getRequest()->getPost('method');
		if($pincode){
		 	$estimatedDelivery = Mage::getModel('operations/serviceablepincodes')->getProductDeliveryOnDetailsPage($pincode,$method);
		 
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($estimatedDelivery));
			return;
		}else{
			$response['status'] = 0;
			$response['message'] = $this->__('Delivery not available in your location yet.');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
	}

	/*
	* @author : Sonali Kosrabe
	* @purpose : To get pincode from order address
	* @dates : 25-oct-2017
	*/
	public function getAddressDataAction(){
		$addressId = $this->getRequest()->getPost('addressId');
		if($addressId){
		 	
		 	$address = Mage::getModel('customer/address')->load($addressId);
    		
    		$address = $address->getData();
            $response = array();
			if(count($address) > 0){
				$response['status'] = 'SUCCESS';
				$response['message'] = $this->__($address['postcode']);
			}else{
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Address not available.');
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}else{
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Address not available.');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
	}

	

	/* 
	*  @author : Sonali Kosrabe
	*  @purpose : To print invoice in APP without username password
	*  @date : 01-11-2017
	*/
	public function getOrderInvoicePrintAction(){
		$orderId = $this->getRequest()->getParam('orderId');
		if($orderId){
			$order = Mage::getModel('sales/order')->load($orderId);
			$invoice = Mage::getModel('sales/order_invoice')->getCollection()->addFieldToFilter('order_id',$orderId)->getData();
			
			if ($invoicePdf = Mage::getModel('sales/order_invoice')->load($invoice[0]['entity_id'])) {
				
				$pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoicePdf));
				return $this->_prepareDownloadResponse(
	                    'invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
	                    'application/pdf'
	                );
			}
			
		}	

	}

	/* 
	*  @Function : stock list to download active stock
	*  @developer : Mahesh Gurav
	*  @date : 15 Nov 2017
	*/	
	public function stocklistAction(){
		ini_set('max_execution_time','1000');   
        ini_set('memory_limit', '-1');
         
        //active stock collection
        $collection = Mage::getModel('catalog/product')
         ->getCollection()
         ->addAttributeToSelect('*')
         ->joinField('qty',
             'cataloginventory/stock_item',
             'qty',
             'product_id=entity_id',
             '{{table}}.stock_id=1',
             'left'
         )
         ->addAttributeToFilter('is_in_stock', array('eq' => 1))
         ->addAttributeToFilter('qty', array('gt'=>0));
        Mage::getSingleton('cataloginventory/stock')
            ->addInStockFilterToCollection($collection);
        $collection->getSelect()->order('name ASC');//->having('qty>0');

        $group_by_category = array();
        $i = 0;
         foreach ($collection as $product) {
            $categories = $product->getCategoryIds(); 
            if(!empty($categories) && $categories[1]){
                $parent_cat = $categories[1];
            }else{
                $parent_cat = $categories[0];
            }

            $_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($parent_cat);
            // $product_qty = $product->getQty();
            $category_name = trim($_cat->getName());
            $product_name = $product->getName();

            $product_price = (int) round(Mage::helper('tax')->getPrice($product, $product->getFinalPrice()));
            if($product->getSpecialPrice()){
              $product_price = (int) round(Mage::helper('tax')->getPrice($product, $product->getSpecialPrice()));
            }
            $sku = $product->getSku();
            if($product_price !== 0 && strpos($sku, 'SYG') === false && strpos($sku, 'SG') === false && strpos($sku, 'rpcdev') === false && strpos($sku, 'dev') === false && $category_name !== 'Sell Your Gadget' && $category_name !== ''){
                $group_by_category[$category_name][$i]['name'] = $product_name;
                $group_by_category[$category_name][$i]['price'] = $product_price;
                $group_by_category[$category_name][$i]['sku'] = $sku;
                $i++;
            }
         }
        // asort($group_by_category);



        $header = array('SKU','PRODUCT NAME','CATEGORY','PRICE (INR)');
        //set file name same to avoid multiple files
        $filename = "stock.csv";
        $file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;
        //open file to write new data
        $filepath = fopen($file,"w");
        fputcsv($filepath, $header);
        
        foreach ($group_by_category as $category_name => $item) {
          // $product = array_values($item);
          // $sku = $product[0];
        	foreach ($item as $key => $sku) {
        		# code...
          		fputcsv($filepath, array($sku['sku'],$sku['name'],$category_name,$sku['price']));
        	}
        }
        fclose($filepath);

        //code to force download excel file of active stock list
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file)); 
        echo readfile($file);
        exit;
	}

	public function categorylistAction(){
		ini_set('max_execution_time','1000');   
        ini_set('memory_limit', '-1');

		$category = Mage::getModel('catalog/category');
		$tree = $category->getTreeModel();
		$tree->load();

		$ids = $tree->getCollection()->getAllIds();
		if ($ids)
		{
	    	foreach ($ids as $id)
		  	{
		     $cat = Mage::getModel('catalog/category');
		     $cat->load($id);
		     if($cat->getLevel()==2 && $cat->getIsActive()==1)
		     {
		        $category1 = Mage::getModel('catalog/category')->load($cat->getId());
		        $products = Mage::getResourceModel('catalog/product_collection')
		                                ->addAttributeToSelect('name')
		                                ->addAttributeToSelect('sku')
		                                ->addAttributeToSelect('price')
		                             ->addCategoryFilter($category1);
		        echo "<b>".$cat->getName()."</b><br>";
		       
		     }
		  }
		}
	}


	/* 
	@author : Sonali Kosrabe
	@date : 3rd Jan 2018
	@purpose : To get per day product sale report
	*/

	public function orderSalesReportAction()
	{
		ini_set("memory_limit",-1);
		date_default_timezone_set('Asia/Kolkata');
		// One day Logic

		$to = date("Y-m-d 23:59:59",strtotime('-1 day')); //$from
		$from = date("Y-m-d 00:00:00",strtotime('-1 day'));
		
		$to = gmdate('Y-m-d H:i:s',strtotime($to));
		$from = gmdate('Y-m-d H:i:s',strtotime($from));
		
		$html .= Mage::getModel('operations/productsale')->getCategorySaleReport($to,$from,'Daily');

		$html .= "<br/><br/>";
		///// Weekly Logic
		$weekDates = Mage::getModel('operations/reports')->getWeekDates();
		$weekFrom = $weekDates['weekFrom'];
		$weekTo = $weekDates['weekTo'];

		$weekFrom = gmdate('Y-m-d H:i:s',strtotime($weekFrom));
		$weekTo = gmdate('Y-m-d H:i:s',strtotime($weekTo));
		

		$html .= Mage::getModel('operations/productsale')->getCategorySaleReport($weekTo,$weekFrom,'Weekly');

		$html .= "<br/><br/>";

		/// Monthly Logic
		$today =  date('Y-m-d');
		$monthTo = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
        $monthFrom = date('Y-m-d 00:00:00', strtotime("first day of this month",strtotime($monthTo)));
        $monthFrom = gmdate('Y-m-d H:i:s',strtotime($monthFrom));
		$monthTo = gmdate('Y-m-d H:i:s',strtotime($monthTo));

        $html .= Mage::getModel('operations/productsale')->getCategorySaleReport($monthTo,$monthFrom,'Monthly');
		
		//echo $html;die;
		// Send Mail
		$templateId = 29;
		$emailTemplate = Mage::getModel('core/email_template')->load($templateId);
		$template_variables['html'] = $html;
		$storeId = Mage::app()->getStore()->getId();
		$processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		// Set sender information			
		$sender = array('name' => $senderName,'email' => $senderEmail);
		//$recepientEmail = array('sonalik2788@gmail.com'); //
		$recepientEmail = array('arm@electronicsbazaar.com','crm@electronicsbazaar.com','insidesales@electronicsbazaar.com','preethi.j@electronicsbazaar.com','kushlesh@electronicsbazaar.com','rajkumarvarma@kaykay.co.in','ketaki@electronicsbazaar.com','george.v@electronicsbazaar.com','hari@electronicsbazaar.com','mayur.m@electronicsbazaar.com','syed.s@electronicsbazaar.com','mangesh.r@electronicsbazaar.com','sonalik2788@gmail.com','sujay.k@electronicsbazaar.com','sathish.vittal@electronicsbazaar.com','lokhesh.s@electronicsbazaar.com','bighnesh.k@electronicsbazaar.com','ravi.k@electronicsbazaar.com','rajib.c@electronicsbazaar.com','ankur.d@electronicsbazaar.com');
		//$recepientName = 'Sonali Kosrabe';
		$subject = $emailTemplate->getTemplateSubject();
		$z_mail = new Zend_Mail('utf-8');
    	$z_mail->setBodyHtml($processedTemplate)
        ->setSubject($subject)
        ->setFrom($senderEmail, $senderName);
        $z_mail->addTo('support@electronicsbazaar.com');
        $z_mail->addBcc($recepientEmail);
		try{
			$z_mail->send();
		}catch(Exception $e){
			Mage::log($e->getMessage(),NULL,'dailySalesReport.log',true);
		}
		
	}

	/* 
	@author : Sonali Kosrabe
	@date : 23rd Jan 2018
	@purpose : To get customer report
	*/

	public function customerReportAction()
	{
		$html = "<table><tr><td colspan='4'>"; 
		$html .= Mage::getModel('operations/reports')->getCustomerRegistrationReport('Daily');
		$html .= "</td></tr><tr><td colspan='4'>&nbsp;</td></tr>"; 
		$html .= "<tr><td colspan='4'>";
		$html .= Mage::getModel('operations/reports')->getCustomerRegistrationReport('Weekly');
		$html .= "</td></tr><tr><td colspan='4'>&nbsp;</td></tr>"; 
		$html .= "<tr><td colspan='4'>";
		$html .= Mage::getModel('operations/reports')->getCustomerRegistrationReport('Monthly');
		$html .= "</td></tr><tr><td colspan='4'>&nbsp;</td></tr>"; 
		$html .= "<tr><td colspan='4'>";
		$html .= Mage::getModel('operations/reports')->getStatewiseCustomerRegisteredReport();
		$html .= "</td></tr><tr><td colspan='4'>&nbsp;</td></tr>"; 
		$html .= "</table>";
		$html .= "<br><br>";
		//die;
		$span_to_check = 12; // check active customer since $span_to_check months
		$to = date('Y-m-d 23:59:59', strtotime("last day of last month")); //$from
		$from = date("Y-m-d 00:00:00", strtotime("-".$span_to_check." months", strtotime($to)));
		$from = date("Y-m-d 00:00:00", strtotime("+1 day", strtotime($from)));
		$html .= Mage::getModel('operations/reports')->getActiveCustomerReport($from,$to,$span_to_check);
		
		// Send Mail
		$templateId = 29;
		$emailTemplate = Mage::getModel('core/email_template')->load($templateId);
		$template_variables['html'] = $html;
		$storeId = Mage::app()->getStore()->getId();
		$processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		// Set sender information			
		$sender = array('name' => $senderName,'email' => $senderEmail);
		//$recepientEmail = array('sonalik2788@gmail.com'); //
		$recepientEmail = array('preethi.j@electronicsbazaar.com','kushlesh@electronicsbazaar.com','rajkumarvarma@kaykay.co.in','ketaki@electronicsbazaar.com','george.v@electronicsbazaar.com','hari@electronicsbazaar.com','mayur.m@electronicsbazaar.com','syed.s@electronicsbazaar.com','mangesh.r@electronicsbazaar.com','sonalik2788@gmail.com','sujay.k@electronicsbazaar.com','sathish.vittal@electronicsbazaar.com','lokhesh.s@electronicsbazaar.com','bighnesh.k@electronicsbazaar.com','ravi.k@electronicsbazaar.com','rajib.c@electronicsbazaar.com','ankur.d@electronicsbazaar.com');
		//$recepientName = 'Sonali Kosrabe';
		$subject = "Customer Report"; //$emailTemplate->getTemplateSubject();
		$z_mail = new Zend_Mail('utf-8');
    	$z_mail->setBodyHtml($processedTemplate)
        ->setSubject($subject)
        ->setFrom($senderEmail, $senderName);
        $z_mail->addTo('support@electronicsbazaar.com');
       /* $z_mail->createAttachment(
	        $html,
	        Zend_Mime::TYPE_OCTETSTREAM,
	        Zend_Mime::DISPOSITION_ATTACHMENT,
	        Zend_Mime::ENCODING_BASE64,
	        'testcode.xls'
	    );*/
        $z_mail->addBcc($recepientEmail);
		try{
			$z_mail->send();
		}catch(Exception $e){
			Mage::log($e->getMessage(),NULL,'dailyCustomerReport.log',true);
		}
	}

	/* 
	@author : Sonali Kosrabe
	@date : 14th Feb 2018
	@purpose : To get product stock report
	*/

	public function productReportAction(){
		Mage::getModel('operations/reports')->productReport();
	}

	/* 
	@author : Sonali Kosrabe
	@date : 16th Feb 2018
	@purpose : To get ASM and RSM report
	*/
	
	public function asmRsmReportAction(){
		$data = Mage::getModel('operations/reports')->asmRsmReport();
		$templateId = 29;
		$emailTemplate = Mage::getModel('core/email_template')->load($templateId);
		$template_variables['html'] = $data['asmRevenueGenerated'];
		$storeId = Mage::app()->getStore()->getId();
		$processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		// Set sender information			
		$sender = array('name' => $senderName,'email' => $senderEmail);
		//$recepientEmail = array('sonalik2788@gmail.com'); //
		$recepientEmail = array('arm@electronicsbazaar.com','crm@electronicsbazaar.com','insidesales@electronicsbazaar.com','preethi.j@electronicsbazaar.com','kushlesh@electronicsbazaar.com','rajkumarvarma@kaykay.co.in','ketaki@electronicsbazaar.com','george.v@electronicsbazaar.com','hari@electronicsbazaar.com','mayur.m@electronicsbazaar.com','syed.s@electronicsbazaar.com','mangesh.r@electronicsbazaar.com','sonalik2788@gmail.com','sujay.k@electronicsbazaar.com','sathish.vittal@electronicsbazaar.com','lokhesh.s@electronicsbazaar.com','bighnesh.k@electronicsbazaar.com','ravi.k@electronicsbazaar.com','rajib.c@electronicsbazaar.com','ankur.d@electronicsbazaar.com');
		//$recepientName = 'Sonali Kosrabe';
		$subject = "ASM wise DSR"; //$emailTemplate->getTemplateSubject();
		$z_mail = new Zend_Mail('utf-8');
    	$z_mail->setBodyHtml($processedTemplate)
        ->setSubject($subject)
        ->setFrom($senderEmail, $senderName);
        $z_mail->addTo('support@electronicsbazaar.com');
        $z_mail->createAttachment(
	        $data['csvData'],
	        Zend_Mime::TYPE_OCTETSTREAM,
	        Zend_Mime::DISPOSITION_ATTACHMENT,
	        Zend_Mime::ENCODING_BASE64,
	        'asmRsmReport.xls'
	    );
        $z_mail->addBcc($recepientEmail);
		try{
			$z_mail->send();
			echo "mail sent";
		}catch(Exception $e){
			Mage::log($e->getMessage(),NULL,'dailyCustomerReport.log',true);
		}
	}


	
}