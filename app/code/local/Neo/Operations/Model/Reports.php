<?php

class Neo_Operations_Model_Reports extends Mage_Core_Model_Abstract
{

    public function customerUpdates($size,$limit,$start){
        $customers = Mage::getModel('customer/customer')->getCollection();//->addFieldToSelect('entity_id');
        $customers = $customers->addFieldToFilter('entity_id',array('nin'=>array(633,736,746,793,979,1209,1540,1977,1996,2078,2093,2962,3024,3275,3418,3637,3387,5034,5088,5715,5789,5905,6234,6685,6860,6925,7144,7218,7544,7627,7665,7812,8002,8229,8649,8654,8657,8666,8732,9026,9030,9139,9261,9327,9578,9693,9729,9730,9737,9750,10292,10377,10384,10529,10601,10874,11060,11144,11146,11596,11882,12097,13484,14622,14714,14783,14787,14904,16158,16197,16198,16203,18352,18521,18969,22778,23048,23408,23420,23634,23858,24491,25310,25313,25336,25344,25373,25390,26178,26186,26196,26290,26419,26424,26487,26541,26646,26665,26741,26817,26988,27359,27465,27732,27880,28223,28918,29718,29726,29918,31917,32271,34731,34845,35087,36639,36785,36826,37639,37898,40437,42502)));
        //$customers = $customers->addFieldToFilter('created_in','Admin');
        $customers = $customers->addFieldToFilter('entity_id',array('gt'=>$start));
        $customers = $customers->addFieldToFilter('entity_id',array('lt'=>1000));
        /*echo "<pre>";
        print_r(implode(',',$customers->getColumnValues('entity_id')));die;*/
        $customers->setCurPage($size) // 2nd page
                  ->setPageSize($limit); // 10 elements per pages
        $customers = $customers->getColumnValues('entity_id');
        $chucked_customers = array_chunk($customers,30);
        
        $not_available_pincode_customers = array();
        $html = "<table><tr>";
        $html .= "<th>Customer ID</th><th>Customer Email</th></tr>";
        foreach ($chucked_customers as $chucked_customer) {
            $start = microtime(true);
            foreach ($chucked_customer as $key => $customerId) {
                echo "\r\n Customer Id: ".$customerId;
                $customer = Mage::getModel('customer/customer')->load($customerId);
                //if($customer->getAllAddresses())


                if($customer->getPincode()){
                    $pincode = $customer->getPincode();
                }else{
                    if(!$customer->getAddresses()){
                        echo " --- Updated";
                        $customer->setCusState(null);
                        $customer->save();
                        $not_available_pincode_customers[$customer->getId()] = $customer->getEmail();
                        $html .= "<tr><td>".$customer->getId()."</td><td>".$customer->getEmail()."</td></tr>";
                        continue;
                    }
                    if ( ! $customer->getDefaultBillingAddress() ) {
                        foreach ($customer->getAddresses() as $address) {
                            $address->setIsDefaultBilling('1')->save();
                            continue; // we only want to set first address of the customer as default billing address
                        }
                    }
                    if ( ! $customer->getDefaultShippingAddress() ) {
                        foreach ($customer->getAddresses() as $address) {
                            $address->setIsDefaultShipping('1')->save();
                            continue; // we only want to set first address of the customer as default shipping address
                        }
                    }
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $pincode = $customer->getDefaultShippingAddress()->getPostcode();
                }
                
                if($pincode){
                    
                    //$shippingPincode = $customer->getDefaultShippingAddress()->getPostcode();
                    $data = Mage::getModel('operations/serviceablepincodes')->getPincodeData($pincode);
                    if($data){

                        $customer->setPincode($data['pincode']);
                        $customer->setCusCity($data['city']);
                        $customer->setCusState($data['region_id']);
                        $customer->setCusCountry($data['country']);
                        try{
                            $customer->save();
                            Mage::log("Customer updated : #".$customerId,null,'customerUpdates.log',true);
                        }catch(exceptions $e){
                            Mage::log('error in updating customers data for customer :'.$customerId,null,'customerUpdates.log',true);
                        }
                        
                    }else{
                        Mage::log('error in updating customers data for customer :'.$customerId,null,'customerUpdates.log',true);
                    }
                    
                }else{
                    $customer->setCusState(null);
                    $customer->save();
                    $not_available_pincode_customers[$customer->getId()] = $customer->getEmail();
                    $html .= "<tr><td>".$customer->getId()."</td><td>".$customer->getEmail()."</td></tr>";
                }

            }
            
            $time_elapsed_secs = microtime(true) - $start;
            $first = reset($chucked_customer);
            $last = end($chucked_customer);
            echo "\r\n Time Taken for 300 - from ".$first." to ".$last." : ".$time_elapsed_secs;
            Mage::log("Time Taken for 300 - from ".$first." to ".$last." : ".$time_elapsed_secs,null,'customer_update.log',true);
            
        }

        $html .= "<tr><th>Total</th><th>".count($not_available_pincode_customers)."</th></tr>";
        $html .= "</table>";


        $templateId = 29;
        $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
        $template_variables['html'] = "Please check attached csv for customer not having pincodes.";
        $storeId = Mage::app()->getStore()->getId();
        $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        // Set sender information           
        $sender = array('name' => $senderName,'email' => $senderEmail);

        $recepientEmail = array('web.maheshgurav@gmail.com');
        //$recepientName = 'Sonali Kosrabe';
        $subject = "Customer Pincodes Not Available!! Page - ".$size;
        $z_mail = new Zend_Mail('utf-8');
        $z_mail->setBodyHtml($processedTemplate)
        ->setSubject($subject)
        ->setFrom($senderEmail, $senderName);
        $z_mail->addTo('support@electronicsbazaar.com');
        $z_mail->addBcc($recepientEmail);
        $z_mail->createAttachment(
            $html,
            Zend_Mime::TYPE_OCTETSTREAM,
            Zend_Mime::DISPOSITION_ATTACHMENT,
            Zend_Mime::ENCODING_BASE64,
            'CustomerNotUpdatedList.xls'
        );
        try{
            $z_mail->send();
        }catch(Exception $e){
            Mage::log($e->getMessage(),NULL,'customerUpdates.log',true);
        }
        //print_r($not_available_pincode_customers);
        
    }
	
    /* 
    @author : Sonali Kosrabe
    @date : 3rd Jan 2018
    @purpose : To get weekday count
    */
    public function getWeekday($date) {
        
        return date('w', strtotime($date));
    }

    /* 
    @author : Sonali Kosrabe
    @date : 8th Feb 2018
    @purpose : To get weekdates
    */
    public function getWeekDates(){
        $today =  date('Y-m-d',strtotime('+0 day'));
        $weekDay = $this->getWeekday($today);
        $d = date('d',strtotime($today));
        $todayMonth = date('m',strtotime($today));

        if($weekDay == 1){
            $weekStart = 7;
        }elseif($weekDay == 0){
            $weekStart = 6;
        }else{
            $weekStart = $weekDay - 1;
        }
        $weekTo = date('Y-m-d 23:59:59', strtotime('-1 day',strtotime($today)));
        $weekFrom = date("Y-m-d 00:00:00",strtotime('-'.$weekStart.' day',strtotime($today)));
        $weekMonth = date('m',strtotime($weekFrom));
        if($weekMonth < $todayMonth && $d != 1){
            $weekFrom = date('Y-m-d 00:00:00', strtotime("first day of this month",strtotime($today)));
        }
        $weekDates = array('weekFrom'=>$weekFrom,'weekTo'=>$weekTo);
        return $weekDates;
    }

    /*
    * @author : Sonali Kosrabe
    * @date : 23th Jan 2018
    * @purpose : To get customer count report for a month and year.
    */

    public function getCustomerRegistrationReport($type = NULL){
        date_default_timezone_set('Asia/Kolkata');
        $today =  date('Y-m-d');
        if($type == 'Monthly'){
            $toDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
            $fromDate = date('Y-m-d 00:00:00', strtotime("first day of this month",strtotime($toDate)));
            $tableTitle =  "In ".date('F',strtotime($toDate));
            
        }elseif($type == 'Daily'){
            $toDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
            $fromDate = date('Y-m-d 00:00:00', strtotime($toDate));
            $tableTitle = "On ".date('d/m/Y',strtotime($toDate));
        }elseif($type == 'Weekly'){
            ///// Weekly Logic
            $weekDates = $this->getWeekDates();
            $fromDate = $weekDates['weekFrom'];
            $toDate = $weekDates['weekTo'];
            $tableTitle = "Week from ".date('d/m/Y',strtotime($fromDate)).' to '.date('d/m/Y',strtotime($toDate));
        }


        //// Till date record
        $tillDate = date('Y-m-d 23:59:59',strtotime('-1 day',strtotime($fromDate)));

        $fromDate = gmdate('Y-m-d H:i:s',strtotime($fromDate));
        $toDate = gmdate('Y-m-d H:i:s',strtotime($toDate));
        $tillDate = gmdate('Y-m-d H:i:s',strtotime($tillDate));
        
    	$customerModel = Mage::getModel('customer/customer');
    	$customerCollection = $customerModel->getCollection()
                            ->addAttributeToSelect('created_at')
                            ->addAttributeToFilter('group_id','4')
                            ->addAttributeToFilter('created_at', array('to' => $tillDate));
        $customerCollectionCount = $customerCollection->count();
        /////////////////////////////

		$customerCollectionForType = $customerModel->getCollection()->addAttributeToSelect('created_at');
		$customerCollectionForType = $customerCollectionForType->addAttributeToFilter('group_id','4')
                            ->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));
        $customerCollectionForTypeCount = $customerCollectionForType->count();
        $customerids = $customerCollectionForType->getColumnValues('entity_id');
		
        $customersOrders = Mage::getResourceModel('sales/order_collection');
        $customersOrders = $customersOrders->addFieldToSelect('customer_id');
        $customersOrders = $customersOrders->addFieldToFilter('customer_id',array('in'=>$customerids));
        $customersOrders = $customersOrders->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));
        $billedCustomers = array_unique($customersOrders->getColumnValues('customer_id'));

        //print_r($billedCustomers);


		$html = "<table border='1' width='100%' cellpadding='5px' cellspacing='0' style='margin-right:30px;margin-bottom:30px; font-size:12px'>

                    <tr>
                        <th width='250px' align='center' rowspan='2'>No. of customers till ".date('d/m/Y',strtotime($tillDate))."</th>
                        <th width='250px' align='center' colspan='2'>".$tableTitle."</th>
                        <th width='250px' align='center' rowspan='2'>Total Customers</th>
                    </tr>
                    <tr>
                        <th width='250px' align='center'>Registered</th>
                        <th width='250px' align='center'>Billed</th>
                    </tr>
                    <tr>
                        <td width='250px' align='center'>".$customerCollectionCount."</td>
                        <td width='250px' align='center'>".$customerCollectionForTypeCount."</td>
                        <td width='250px' align='center'>".count($billedCustomers)."</td>
                        <td width='250px' align='center'>".($customerCollectionCount + $customerCollectionForTypeCount)."</td>
                    </tr>

					";

		
		$html .= "</table>";
		return $html;
    }
    /*
    * @author : Sonali Kosrabe
    * @date : 23th Jan 2018
    * @purpose : To get statewise customer count report for a month and year.
    */
    public function getStatewiseCustomerRegistrationReport($type = NULL, $stateId = NULL){
        
        $today =  date('Y-m-d');
        if($type == 'Monthly'){
            $toDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
            $fromDate = date('Y-m-d 00:00:00', strtotime("first day of this month",strtotime($toDate)));
        }elseif($type == 'Daily'){
            $toDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
            $fromDate = date('Y-m-d 00:00:00', strtotime($toDate));
        }elseif($type == 'Weekly'){
            $weekDates = $this->getWeekDates();
            $fromDate = $weekDates['weekFrom'];
            $toDate = $weekDates['weekTo'];
            $tableTitle =  "Registered for week from ".date('d/m/Y',strtotime($fromDate)).' to '.date('d/m/Y',strtotime($toDate));
        }

        date_default_timezone_set('Asia/Kolkata');
        $fromDate = gmdate('Y-m-d H:i:s',strtotime($fromDate));
        $toDate = gmdate('Y-m-d H:i:s',strtotime($toDate));
        
        $customerCollectionForType = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToFilter('cus_state',$stateId)
                                    ->addAttributeToFilter('group_id','4')
                                   ->addAttributeToSelect('created_at');
        $customerCollectionForType = $customerCollectionForType->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));

        $customerCollectionForTypeCount = $customerCollectionForType->count();

        $customerids = $customerCollectionForType->getColumnValues('entity_id');
        $customersOrders = Mage::getModel('sales/order')->getCollection();
        
        $customersOrders->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region_id'));
        $customersOrders->addFieldToSelect(array('customer_id'));
        $customersOrders->addFieldToFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate));
        $customersOrders->addAttributeToFilter('sfoa.region_id',$stateId);
        $customersOrders->addFieldToFilter('customer_id',array('in'=>$customerids));
        $billedCustomers = array_unique($customersOrders->getColumnValues('customer_id'));
        return array('customerCollectionForTypeCount'=>$customerCollectionForTypeCount,'billedCustomers'=>count($billedCustomers));
    }
    /*
    * @author : Sonali Kosrabe
    * @date : 24th Jan 2018
    * @purpose : To get active customer report.
    */
    public function getActiveCustomerReport($from = NULL,$to = NULL,$span_to_check = NULL){
    	ini_set('memory_limit', -1);
        date_default_timezone_set('Asia/Kolkata');
        //convert time to gmtime
        $to = gmdate('Y-m-d H:i:s',strtotime($to));
        $from = gmdate('Y-m-d H:i:s',strtotime($from));
        
    	/*$orderCollection = Mage::getModel('sales/order')->getCollection();
    	
    	$orderCollection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region_id'));
    	$orderCollection->getSelect()->group('sfoa.region_id');
    	$orderCollection->addFieldToSelect(array('customer_id'));
    	$orderCollection->addFieldToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
    	$orderCollection->getSelect()->columns('group_concat(DISTINCT(main_table.customer_id) separator ",") as customer_id,group_concat(DISTINCT(main_table.increment_id) separator ",") as increment_id');*/


        $customersCollection = Mage::getResourceModel('customer/customer_collection');
        $customersCollection = $customersCollection->addAttributeToSelect('cus_state')->addAttributeToFilter('group_id','4');
        $customersCollectionStates = array_unique($customersCollection->getColumnValues('cus_state'));
        /*$customersCollection->getSelect()->group('cus_state');
        $customersCollection->getSelect()->columns('group_concat(DISTINCT(entity_id) separator ",") as customer_id');*/
        //echo "<pre>";
        //echo $customersCollection->count();die;
      

       

    	
        $html = "<table border='1' width='100%' cellpadding='5px' cellspacing='0' style='font-size:12px'>
					<tr>
					<th width='300px' align='center'>State</th>
                    <th width='200px' align='center'>Total Customers</th>
					<th width='200px' align='center'>Active Customers</th>
                    <th width='200px' align='center'>Dormant Customers</th>
				</tr>";

    	$data = array(); $totalCustomers = 0; $totalActiveCustomers = 0; $totalDormantCustomers = 0;$countOfStateNotDefined = 0;
    	foreach ($customersCollectionStates as $stateId) {
            
            $customers = Mage::getResourceModel('customer/customer_collection')
                            ->addAttributeToSelect(array('entity_id'));
            if($stateId == ''){
                $customers->addAttributeToFilter('cus_state', [ ['null' => true]]);
            }else{
                $customers->addAttributeToFilter('cus_state',$stateId);
            }
            //echo $customers->getSelect();
            $customer_ids = $customers->getColumnValues('entity_id');
            /*echo "<br>".$stateId."-----".count($customer_ids);
            print_r($customer_ids);
            die;*/
            if($stateId == NULL){
                $countOfStateNotDefined += count($customer_ids);
                continue;
            }

    		$activeCount = 0; $semiActiveCount = 0; $inActiveCount = 0; $dormantCount = 0;

    		//$customer_ids = explode(',', $order->getCustomerId());
            $totalCustomerForRegion = count($customer_ids);
    		foreach ($customer_ids as $customerid) {
    			$customerStatus = $this->checkActiveCustomer($customerid,$from,$to,$span_to_check);
    			if($customerStatus['active']){
    				$activeCount++;
    			}
                if($customerStatus['semi-active']){
                    $semiActiveCount++;
                }
                if($customerStatus['in-active']){
                    $inActiveCount++;
                }
            }

            $region = Mage::getModel('directory/region')->load($stateId);
            $state_name = $region->getName(); 

            $customerCollectionForDormant = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToFilter('cus_state',$stateId)
                                    ->addAttributeToFilter('group_id','4')
                                    ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to));
            $customerCollectionForDormantCount = $customerCollectionForDormant->count();

            $customerids = $customerCollectionForDormant->getColumnValues('entity_id');
            $customersOrders = Mage::getModel('sales/order')->getCollection();
            
            $customersOrders->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region_id'));
            $customersOrders->addFieldToSelect(array('customer_id'));
            $customersOrders->addAttributeToFilter('sfoa.region_id',$stateId);
            $customersOrders->addFieldToFilter('customer_id',array('in'=>$customerids));
            $customersOrders->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
            $customersOrdersCount = count(array_unique($customersOrders->getColumnValues('customer_id')));
            //$dormantCount = $customerCollectionForDormantCount - $customersOrdersCount;

            //$totalCustomerCount = $activeCount+$semiActiveCount+$inActiveCount+$dormantCount;
            
            $totalCustomerCount = count($customer_ids); 

            $activePercent = ($activeCount / $totalCustomerCount);
            $activeCustomerRatio = number_format( $activePercent * 100, 2 );

            $semiActivePercent = ($semiActiveCount / $totalCustomerCount);
            $semiActiveCustomerRatio = number_format( $semiActivePercent * 100, 2 );

            $inActivePercent = ($inActiveCount / $totalCustomerCount);
            $inActiveCustomerRatio = number_format( $inActivePercent * 100, 2 );

            /*$dormantPercent = ($dormantCount / $totalCustomerCount);
            $dormantCustomerRatio = number_format($dormantPercent * 100, 2 );*/



            $totalActiveCount = $activeCount+$semiActiveCount+$inActiveCount;
            $totalActiveRatio = $activeCustomerRatio+$semiActiveCustomerRatio+$inActiveCustomerRatio;

            $dormantCount = $totalCustomerCount - $totalActiveCount;
            $dormantPercent = ($dormantCount / $totalCustomerCount);
            $dormantCustomerRatio = number_format($dormantPercent * 100, 2 );


            
            $totalCustomerRatio = $activeCustomerRatio+$semiActiveCustomerRatio+$inActiveCustomerRatio+$dormantCustomerRatio;
            $state_name = $state_name != '' ? $state_name : 'Others';
    		$html .= "<tr>
                    <th width='300px' align='left'>".$state_name./*"--".$stateId.*/"</th>
                    <td width='200px' align='left'>".$totalCustomerCount./*"--".implode(',', $customer_ids).*/"</td>
                    <td width='200px' align='center'>".number_format($totalActiveRatio,2)."% (".$totalActiveCount.")</td>
                    <td width='200px' align='center'>".number_format($dormantCustomerRatio,2)."% (".$dormantCount.")</td>
                </tr>";

            $totalCustomers = $totalCustomers + count($customer_ids);
            $totalActiveCustomers = $totalActiveCustomers + $totalActiveCount;
            $totalDormantCustomers = $totalDormantCustomers + $dormantCount;
    		
    	}



        $AffilateCount = (int)$customersCollection->count();
        $othersCount = $AffilateCount - $totalCustomers;
        $othersCount = $othersCount + $countOfStateNotDefined;
        $html .= "<tr>
                    <th width='300px' align='left'>Others</th>
                    <td width='200px' align='center'>".$othersCount."</td>
                    <td width='200px' align='center'>0</td>
                    <td width='200px' align='center'>".$othersCount."</td>
                </tr>";

        $html .= "<tr>
                    <th width='300px' align='left'>Total</th>
                    <td width='200px' align='center'>".$AffilateCount."</td>
                    <td width='200px' align='center'>".$totalActiveCustomers."</td>
                    <td width='200px' align='center'>".$totalDormantCustomers."</td>
                </tr>";
    	$html .= "</table>";
    	return $html;
    }


    /*
    * @author : Sonali Kosrabe
    * @date : 24th Jan 2018
    * @purpose : To check customer is active.
    */

    function checkActiveCustomer($customer_id = NULL,$from = NULL, $to = NULL,$span_to_check = NULL){

    	$orders = Mage::getModel('sales/order')->getCollection()
    		->addFieldToFilter('customer_id',$customer_id)
		    ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to));
		
		$createdAtDates = $orders->getColumnValues('created_at');
		
        $months = array();
		foreach ($createdAtDates as $createdAtDate) {
			$months[] = date('m',strtotime($createdAtDate));
		}
		$months = array_unique($months);
       // echo "<pre><br>Customer Id : ".$customer_id."<br><br>"; print_r($months);
        foreach ($months as $month) {
            if(in_array($month, array(1,2,3))){
                $quarter[1] = $month;
            }
            if(in_array($month, array(4,5,6))){
                $quarter[2] = $month;
            }
            if(in_array($month, array(7,8,9))){
                $quarter[3] = $month;
            }
            if(in_array($month, array(10,11,12))){
                $quarter[4] = $month;
            }
        }
        
        if(count($months) >= 10){ //$span_to_check

        	$customerStatus['active'] =  true;
        }else{
			$customerStatus['active'] =  false;
        }

        if(count($quarter) == 4){
            $customerStatus['semi-active'] =  true;
        }else{
            $customerStatus['semi-active'] =  false;
        }
        
        if(count($months) == 1){
            $customerStatus['in-active'] =  true;
        }else{
            $customerStatus['in-active'] =  false;
        }

        if(count($months) == 0){
            $customerStatus['dormant'] =  true;
        }else{
            $customerStatus['dormant'] =  false;
        }

        return $customerStatus;
    }

    public function getStatewiseCustomerRegisteredReport(){
        $regionCollection = Mage::getModel('directory/region_api')->items('IN');
        
        $html = "<table border='1' width='100%' cellpadding='5px' cellspacing='0' style='font-size:12px; margin-top:20px;'>
                    <tr>
                    <th width='300px' align='center' rowspan='2'>State</th>
                    <th width='230px' align='center' colspan='2'>Daily</th>
                    <th width='230px' align='center' colspan='2'>Week Till Date</th>
                    <th width='250px' align='center' colspan='2'>Month Till Date</th>
                </tr>";
        $html .="<tr>
                    <th width='230px' align='center'>Registered</th>
                    <th width='230px' align='center'>Billed</th>
                    <th width='230px' align='center'>Registered</th>
                    <th width='230px' align='center'>Billed</th>
                    <th width='230px' align='center'>Registered</th>
                    <th width='230px' align='center'>Billed</th>
                </tr>";

        $dailyRegisteredTotalCount = 0; $weeklyRegisteredTotalCount = 0; $monthlyRegisteredTotalCount = 0;
        $dailyBilledTotalCount = 0; $weeklyBilledTotalCount = 0; $monthlyBilledTotalCount = 0;

        foreach ($regionCollection as $region) {
            $region = Mage::getModel('directory/region')->load($region['region_id']);
            $state_name = $region->getName(); 
            $dailyRegistered = $this->getStatewiseCustomerRegistrationReport('Daily',$region['region_id']);
            $dailyRegisteredTotalCount = $dailyRegisteredTotalCount + $dailyRegistered['customerCollectionForTypeCount'];
            $dailyBilledTotalCount = $dailyBilledTotalCount + $dailyRegistered['billedCustomers'];

            $weeklyRegistered = $this->getStatewiseCustomerRegistrationReport('Weekly',$region['region_id']);
            $weeklyRegisteredTotalCount = $weeklyRegisteredTotalCount + $weeklyRegistered['customerCollectionForTypeCount'];
            $weeklyBilledTotalCount = $weeklyBilledTotalCount + $weeklyRegistered['billedCustomers'];

            $monthlyRegistered = $this->getStatewiseCustomerRegistrationReport('Monthly',$region['region_id']);
            $monthlyRegisteredTotalCount = $monthlyRegisteredTotalCount + $monthlyRegistered['customerCollectionForTypeCount'];
            $monthlyBilledTotalCount = $monthlyBilledTotalCount + $monthlyRegistered['billedCustomers'];

            $html .= "<tr>
                    <th width='300px' align='left'>".$state_name."</th>
                    <td width='230px' align='center'>".$dailyRegistered['customerCollectionForTypeCount']."</td>
                    <td width='230px' align='center'>".$dailyRegistered['billedCustomers']."</td>
                    <td width='230px' align='center'>".$weeklyRegistered['customerCollectionForTypeCount']."</td>
                    <td width='230px' align='center'>".$weeklyRegistered['billedCustomers']."</td>
                    <td width='250px' align='center'>".$monthlyRegistered['customerCollectionForTypeCount']."</td>
                    <td width='230px' align='center'>".$monthlyRegistered['billedCustomers']."</td>
                </tr>";
        }

        $html .= "<tr>
                    <th width='300px' align='left'>Total</th>
                    <th width='230px' align='center'>".$dailyRegisteredTotalCount."</th>
                    <th width='230px' align='center'>".$dailyBilledTotalCount."</th>
                    <th width='230px' align='center'>".$weeklyRegisteredTotalCount."</th>
                    <th width='230px' align='center'>".$weeklyBilledTotalCount."</th>
                    <th width='250px' align='center'>".$monthlyRegisteredTotalCount."</th>
                    <th width='230px' align='center'>".$monthlyBilledTotalCount."</th>
                </tr>";

        $html .= "</table>";

        return $html;
    }

    /* 
    @author : Sonali Kosrabe
    @date : 14th Feb 2018
    @purpose : To get product stock report
    */
    
    public function productReport(){
        date_default_timezone_set('Asia/Kolkata');
        $products = Mage::getSingleton('catalog/product')
        ->getCollection()
        ->addFieldToFilter('attribute_set_id',array('nin'=>array('48')))
        ->addAttributeToSelect(array('name', 'sku', 'price','description','brands','category_ids'))
        ->joinField(
            'qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );
        $products->getSelect()->order('entity_id DESC');
        
        $yesterdayDate = date('Y-m-d', strtotime('-1 day'));

        $weekDates = $this->getWeekDates();
        $weekFrom = $weekDates['weekFrom'];
        $weekTo = $weekDates['weekTo'];

        $weekFrom = gmdate('Y-m-d H:i:s',strtotime($weekFrom));
        $weekTo = gmdate('Y-m-d H:i:s',strtotime($weekTo));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=stock.csv');
        $export = fopen('php://output', 'w');
        
        $data = array();

        $data = array('Product Id','Sku','Name','Brands','Category','Price','Yesterday Price','Last Week Price','Qty','Yesterday Qty','Last Week Qty');

        fputcsv($export, $data);
        foreach ($products as $product) {
            $product_id = $product->getId();
           
            $categoryName = '';
            foreach ($product->getCategoryIds() as $category_id) {
                $category=Mage::getModel('catalog/category')->load($category_id);
                $categoryName .= $category->getName().' | ';
            }
            $categoryName = trim($categoryName," | ");
            
            $yesterdayPrice = Mage::getModel('productpricereport/productpricereport')->getCollection()
                            ->addFieldToFilter('date',array('like'=>$yesterdayDate."%"))
                            ->addFieldToFilter('product_id',$product_id);
            $yesterdayPrice->getSelect()->order('id DESC')->limit(1);
            $yesterdayPrice = $yesterdayPrice->getData();
            
            if(!empty($yesterdayPrice)){
                $yesterdayPrice = $yesterdayPrice[0]['to_price'];    
            }else{
                $yesterdayPrice = $product->getPrice();
            }


            $lastWeekPrice = Mage::getSingleton('productpricereport/productpricereport')->getCollection()
                            ->addFieldToFilter('date',array('from'=>$weekFrom."%",'to'=>$weekTo."%"))
                            ->addFieldToFilter('product_id',$product_id);
            $lastWeekPrice->getSelect()->order('id DESC')->limit(1);
            $lastWeekPrice = $lastWeekPrice->getData();
            if(!empty($lastWeekPrice)){
                $lastWeekPrice = $lastWeekPrice[0]['to_price'];    
            }else{
                $lastWeekPrice = $product->getPrice();
            }

            $yesterdayQtySold = Mage::getSingleton('sales/order_item')->getCollection()
                            ->addFieldToSelect('qty_ordered')
                            ->addFieldToFilter('created_at',array('like'=>$yesterdayDate."%"))
                            ->addFieldToFilter('product_id',$product_id);
            $yesterdayQtySold->getSelect()->columns('SUM(qty_ordered) as yest_qty_ordered');
            $yesterdayQtySold = $yesterdayQtySold->getData();
            if(!empty($yesterdayQtySold)){
                $yesterdayQtySold = $yesterdayQtySold[0]['yest_qty_ordered'];    
            }else{
                $yesterdayQtySold = 0;
            }

            $lastWeekQtySold = Mage::getSingleton('sales/order_item')->getCollection()
                            ->addFieldToSelect('qty_ordered')
                            ->addFieldToFilter('created_at',array('from'=>$weekFrom."%",'to'=>$weekTo."%"))
                            ->addFieldToFilter('product_id',$product_id);
            $lastWeekQtySold->getSelect()->columns('SUM(qty_ordered) as last_week_qty_ordered');
            $lastWeekQtySold = $lastWeekQtySold->getData();
            if(!empty($lastWeekQtySold)){
                $lastWeekQtySold = $lastWeekQtySold[0]['last_week_qty_ordered'];    
            }else{
                $lastWeekQtySold = 0;
            }

            $data = array($product->getId(),$product->getSku(),$product->getName(),$product->getAttributeText('brands'),$categoryName,$product->getPrice(),$yesterdayPrice,$lastWeekPrice,(int)$product->getQty(),(int)$yesterdayQtySold,(int)$lastWeekQtySold);
            fputcsv($export, $data);
        
        }
        fclose($export );
        

    }

    /* 
    @author : Sonali Kosrabe
    @date : 16th Feb 2018
    @purpose : To get ASM and RSM report
    */
    public function asmRsmReport(){


	    $entityType = 'customer';
		$attributeCode = 'asm_map';
		$attributeInfo = Mage::getModel('eav/entity_attribute')
		                ->loadByCode($entityType, $attributeCode);
		$asms = $attributeInfo->getSource()->getAllOptions(false);
 		
 		$asmRevenueGenerated = "<table border='1'><tr><td>ASM</td><td>NO. OF PARTNERS BILLED DAILY</td><td>DAILY REVENUE GENERATED</td><td>NO. OF PARTNERS BILLED MONTHLY</td><td>MONTHLY REVENUE GENERATED</td><td>NO. OF PARTNERS ACTIVATED DAILY</td><td>NO. OF PARTNERS ACTIVATED MONTHLY</td></tr>";
        $csvData = "<table border='1'><tr><td>ARM/CRM</td><td>City</td><td>Partner Billed Store Name</td><td>Daily Revenue</td><td>Monthly Revenue</td><td>Last Billed Date</td></tr>";

        $today =  date('Y-m-d');
        date_default_timezone_set('Asia/Kolkata');
        
        $toDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
        $fromDate = date('Y-m-d 00:00:00', strtotime($toDate));
        $fromDate = gmdate('Y-m-d H:i:s',strtotime($fromDate));
        $toDate = gmdate('Y-m-d H:i:s',strtotime($toDate));


        $monthToDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
        $monthFromDate = date('Y-m-d 00:00:00', strtotime("first day of this month",strtotime($monthToDate)));
        $monthFromDate = gmdate('Y-m-d H:i:s',strtotime($monthFromDate));
        $monthToDate = gmdate('Y-m-d H:i:s',strtotime($monthToDate));

        $span_to_check = 12; // check active customer since $span_to_check months
        $to = date('Y-m-d 23:59:59', strtotime("last day of last month")); //$from
        $from = date("Y-m-d 00:00:00", strtotime("-".$span_to_check." months", strtotime($to)));
        $from = date("Y-m-d 00:00:00", strtotime("+1 day", strtotime($from)));
        
        foreach ($asms as $key => $value) {
      		if($value['value'] == '') continue;
      		$customers = Mage::getModel('customer/customer')->getCollection()
      						->addAttributeToSelect('*')
      						->addAttributeToFilter('asm_map',$value['value']);
      		$asmMonthlyRevenue = 0; $asmDailyRevenue = 0; $asmsCustomerDailyCount = 0; $asmsCustomerMonthlyCount = 0; $dailyBilledCustomerId = array(); $monthlyBilledCustomerId = array();
      		foreach ($customers as $customer) {
      			$storeName = $customer->getData('affiliate_store');
      			$city = $customer->getData('cus_city');
      			$asm_mapped = $customer->getResource()->getAttribute('asm_map')->getFrontend()->getValue($customer); 
      			
      			$dailyRevenue = Mage::getModel('sales/order')->getCollection();
      			$dailyRevenue->addFieldToSelect(array('grand_total'));
      			$dailyRevenue->addFieldToFilter('customer_id',$customer->getId());
                $dailyRevenue->addFieldToFilter('status',array('neq'=>'canceled'));
      			$dailyRevenue->addFieldToFilter('created_at',array('from'=>$fromDate,'to'=>$toDate));
      			$dailyRevenue->getSelect()->columns('SUM(grand_total) as total_revenue');
      			$dailyRevenue = $dailyRevenue->getData();
      			$asmDailyRevenue = $asmDailyRevenue + $dailyRevenue[0]['total_revenue'];
      			if($dailyRevenue[0]['total_revenue'] > 0){
      				$asmsCustomerDailyCount = $asmsCustomerDailyCount + 1; 
                    $dailyBilledCustomerId[] = $customer->getId(); 
      			}

      			
            	$monthlyRevenue = Mage::getModel('sales/order')->getCollection();
      			$monthlyRevenue->addFieldToSelect(array('grand_total'));
      			$monthlyRevenue->addFieldToFilter('customer_id',$customer->getId());
      			$monthlyRevenue->addFieldToFilter('status',array('neq'=>'canceled'));
                $monthlyRevenue->addFieldToFilter('created_at',array('from'=>$monthFromDate,'to'=>$monthToDate));
      			$monthlyRevenue->getSelect()->columns('SUM(grand_total) as total_revenue');
      			$monthlyRevenue = $monthlyRevenue->getData();
      			$asmMonthlyRevenue = $asmMonthlyRevenue + $monthlyRevenue[0]['total_revenue'];
      			if($monthlyRevenue[0]['total_revenue'] > 0){
      				$asmsCustomerMonthlyCount = $asmsCustomerMonthlyCount + 1; 
                    $monthlyBilledCustomerId[] = $customer->getId(); 
      			}


                $lastBilledDate = Mage::getModel('sales/order')->getCollection();
                $lastBilledDate->addFieldToSelect(array('created_at'));
                $lastBilledDate->addFieldToFilter('customer_id',$customer->getId());
                //$lastBilledDate->addFieldToFilter('created_at',array('lt'=>$from));
                $lastBilledDate->getSelect()->order('created_at DESC')->limit(10);
                $lastBilledDate = $lastBilledDate->getColumnValues('created_at');
                $lastBilledDates = array();
                foreach ($lastBilledDate as $key => $value) {
                    $lastBilledDates[] = date('Y-m-d',strtotime($value)); 
                }
                $lastBilledDates = implode(' <br style="mso-data-placement:same-cell;" /> ',$lastBilledDates);
                $data = array($asm_mapped,$city,$storeName,$dailyRevenue[0]['total_revenue'],$monthlyRevenue[0]['total_revenue']);
				$csvData .= "<tr><td>".$asm_mapped."</td><td>".$city."</td><td>".$storeName."</td><td>".$dailyRevenue[0]['total_revenue']."</td><td>".$monthlyRevenue[0]['total_revenue']."</td><td>".$lastBilledDates."</td></tr>";
			}


            if(count($dailyBilledCustomerId) > 0){
                $dailyReActivatedCustomer = Mage::getResourceModel('sales/order_collection')
                                    ->addFieldToSelect(array('customer_id','created_at'))
                                    ->addFieldToFilter('customer_id',array('in'=>$dailyBilledCustomerId))
                                    ->addFieldToFilter('created_at',array('gt'=>$from))
                                    ->addFieldToFilter('created_at',array('lt'=>$to));

                $dailyReActivatedCustomer->getSelect()->columns('group_concat(customer_id separator ",") as total_activated_customer');//->where("created_at NOT BETWEEN '".$from."' AND '".$to."'");
                $dailyReActivatedCustomer = $dailyReActivatedCustomer->getData();
                $dailyReActivatedCustomer = array_unique(explode(',',$dailyReActivatedCustomer[0]['total_activated_customer']));

                $dailyReActivatedCustomer = count($dailyBilledCustomerId) - count($dailyReActivatedCustomer);

                $dailyReActivatedCustomer = Mage::getResourceModel('sales/order_collection')
                                ->addFieldToSelect('customer_id')
                                ->addFieldToFilter('main_table.customer_id',array('in'=>$dailyBilledCustomerId))
                                ->addFieldToFilter('main_table.created_at',array('gt'=>$from))
                                ->addFieldToFilter('main_table.created_at',array('lt'=>$to));

                $dailyReActivatedCustomer = array_unique($dailyReActivatedCustomer->getColumnValues('customer_id'));
                
                $dailyReActivatedCustomerIds = array_diff($dailyBilledCustomerId,$dailyReActivatedCustomer);
                $dailyReActivatedCustomerColl = Mage::getResourceModel('customer/customer_collection')
                                    ->addFieldToFilter('entity_id',array('in'=>$dailyReActivatedCustomerIds))
                                    ->addFieldToFilter('created_at',array('lt'=>$from));

                $dailyReActivatedCustomers = count($dailyReActivatedCustomerColl->getColumnValues('entity_id'));
            }else{
                $dailyReActivatedCustomers = 0;
            }

            if(count($monthlyBilledCustomerId) > 0){

                $monthlyReActivatedCustomer = Mage::getResourceModel('sales/order_collection')
                                    ->addFieldToSelect('customer_id')
                                    ->addFieldToFilter('main_table.customer_id',array('in'=>$monthlyBilledCustomerId))
                                    ->addFieldToFilter('main_table.created_at',array('gt'=>$from))
                                    ->addFieldToFilter('main_table.created_at',array('lt'=>$to));

                $monthlyReActivatedCustomer = array_unique($monthlyReActivatedCustomer->getColumnValues('customer_id'));
                
                $monthlyReActivatedCustomerIds = array_diff($monthlyBilledCustomerId,$monthlyReActivatedCustomer);
                $monthlyReActivatedCustomerColl = Mage::getResourceModel('customer/customer_collection')
                                    ->addFieldToFilter('entity_id',array('in'=>$monthlyReActivatedCustomerIds))
                                    ->addFieldToFilter('created_at',array('lt'=>$from));

                $monthlyReActivatedCustomers = count($monthlyReActivatedCustomerColl->getColumnValues('entity_id'));
                
            }else{
                $monthlyReActivatedCustomers = 0;
            }

      		$asmRevenueGenerated .= "<tr><td>".$asm_mapped."</td><td>".$asmsCustomerDailyCount."</td><td>".$asmDailyRevenue."</td><td>".$asmsCustomerMonthlyCount."</td><td>".$asmMonthlyRevenue."</td><td>".$dailyReActivatedCustomers."</td><td>".$monthlyReActivatedCustomers."</td></tr>"; 
      		
            $totalasmsCustomerDailyCount = $totalasmsCustomerDailyCount + $asmsCustomerDailyCount;
            $totalasmDailyRevenue = $totalasmDailyRevenue + $asmDailyRevenue;
            $totalasmsCustomerMonthlyCount = $totalasmsCustomerMonthlyCount + $asmsCustomerMonthlyCount;
            $totalasmMonthlyRevenue = $totalasmMonthlyRevenue + $asmMonthlyRevenue;
            $totaldailyReActivatedCustomers = $totaldailyReActivatedCustomers + $dailyReActivatedCustomers;
            $totalmonthlyReActivatedCustomers = $totalmonthlyReActivatedCustomers + $monthlyReActivatedCustomers; 
        }
        $asmRevenueGenerated .= "<tr><td>Total</td><td>".$totalasmsCustomerDailyCount."</td><td>".$totalasmDailyRevenue."</td><td>".$totalasmsCustomerMonthlyCount."</td><td>".$totalasmMonthlyRevenue."</td><td>".$totaldailyReActivatedCustomers."</td><td>".$totalmonthlyReActivatedCustomers."</td></tr>"; 
            
        $asmRevenueGenerated .= '</table>';
        $csvData .= '</table>';
        return array('csvData'=>$csvData,'asmRevenueGenerated'=>$asmRevenueGenerated);
    }


    /* 
    @author : Sonali Kosrabe
    @date : 13th Mar 2018
    @purpose : To get customer data in xls
    */

    public function getCustomerData($type = NULL){
        $today =  date('Y-m-d');
        if($type == 'Monthly'){
            $toDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
            $fromDate = date('Y-m-d 00:00:00', strtotime("first day of this month",strtotime($toDate)));
        }

        date_default_timezone_set('Asia/Kolkata');
        $fromDate = gmdate('Y-m-d H:i:s',strtotime($fromDate));
        $toDate = gmdate('Y-m-d H:i:s',strtotime($toDate));
        $customers = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addAttributeToFilter('group_id','4')
                                   ->addAttributeToSelect('created_at');
        $customers = $customers->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));
       
        $csvData = "<table border='0' cellpadding='5px'>
                        <tr>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Group</th>
                            <th>Store Name</th>
                            <th>Mobile</th>
                            <th>Referred By ASM</th>
                            <th>Pincode</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Registered On</th>
                            <th>First Billed On</th>
                        </tr>";
        
        foreach ($customers as $customer) {
            $groupname = Mage::getModel('customer/group')->load($customer->getGroupId())->getCustomerGroupCode();
            $region = Mage::getModel('directory/region')->load($customer->getData('cus_state'));
            $state_name = $region->getName();


            $customerOrders = Mage::getModel('sales/order')->getCollection();
            $customerOrders->addFieldToSelect(array('created_at','entity_id','customer_id'));
            $customerOrders->addFieldToFilter('customer_id',$customer->getId());
            $customerOrders->getSelect()->order('entity_id DESC')->limit(1);
            if($customerOrders->count() > 0){
                $customerOrders = $customerOrders->getData();
                $orderCreatedAt = Mage::getModel('core/date')->date('Y-m-d H:i:s',strtotime($customerOrders[0]['created_at']));
            }else{
                $orderCreatedAt = '';
            }

            $csvData .= "<tr>
                        <td>".$customer->getEmail()."</td>
                        <td>".$customer->getName()."</td>
                        <td>".$groupname."</td>
                        <td>".$customer->getData('affiliate_store')."</td>
                        <td>".$customer->getMobile()."</td>
                        <td>".$customer->getResource()->getAttribute('asm_map')->getFrontend()->getValue($customer)."</td>
                        <td>".$customer->getPincode()."</td>
                        <td>".$customer->getData('cus_city')."</td>
                        <td>".$state_name."</td>
                        <td>".Mage::getModel('core/date')->date('Y-m-d H:i:s',strtotime($customer->getCreatedAt()))."</td>
                        <td>".$orderCreatedAt."</td>
                        </tr>";


        }

        $csvData .= "</table>";
        return $csvData;
    }


    


}
	 
