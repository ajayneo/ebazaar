<?php

class Neo_Operations_Model_Reports extends Mage_Core_Model_Abstract
{
    
	
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
            /*$weekDay = $this->getWeekday($today);
            if($weekDay == 1){
                $weekStart = 7;
            }else{
                $weekStart = $weekDay - 1;
            }
            
            $toDate = date('Y-m-d 23:59:59', strtotime('-1 day'));
            $fromDate = date("Y-m-d 00:00:00",strtotime('-'.$weekStart.' day'));*/
            $weekDates = $this->getWeekDates();
            $fromDate = $weekDates['weekFrom'];
            $toDate = $weekDates['weekTo'];
            $tableTitle =  "Week from ".date('d/m/Y',strtotime($fromDate)).' to '.date('d/m/Y',strtotime($toDate));
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
		/*echo "<pre>";
        print_r($customerids);*/

        $customersOrders = Mage::getResourceModel('sales/order_collection');//->getCollection();
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
       // $customerCollectionForType->getSelect();

        $customerCollectionForTypeCount = $customerCollectionForType->count();

        $customerids = $customerCollectionForType->getColumnValues('entity_id');
       // print_r($customerids);
        $customersOrders = Mage::getModel('sales/order')->getCollection();
        
        $customersOrders->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region_id'));
       // $customersOrders->getSelect()->group('sfoa.region_id');
        $customersOrders->addFieldToSelect(array('customer_id'));
        $customersOrders->addFieldToFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate));
        $customersOrders->addAttributeToFilter('sfoa.region_id',$stateId);
        $customersOrders->addFieldToFilter('customer_id',array('in'=>$customerids));
        $billedCustomers = array_unique($customersOrders->getColumnValues('customer_id'));
        //print_r($billedCustomers);
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
        
    	$orderCollection = Mage::getModel('sales/order')->getCollection();
    	
    	$orderCollection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region_id'));
    	$orderCollection->getSelect()->group('sfoa.region_id');
    	$orderCollection->addFieldToSelect(array('customer_id'));
    	$orderCollection->addFieldToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
    	$orderCollection->getSelect()->columns('group_concat(DISTINCT(main_table.customer_id) separator ",") as customer_id,group_concat(DISTINCT(main_table.increment_id) separator ",") as increment_id');
    	
        $html = "<table border='1' width='100%' cellpadding='5px' cellspacing='0' style='font-size:12px'>
					<tr>
					<th width='300px' align='center'>State</th>
                    <th width='200px' align='center'>Total Customers</th>
					<th width='200px' align='center'>Active Customers</th>
                    <th width='200px' align='center'>Dormant Customers</th>
				</tr>";

    	$data = array();
    	foreach ($orderCollection as $order) {
    		$activeCount = 0; $semiActiveCount = 0; $inActiveCount = 0; $dormantCount = 0;
    		$customer_ids = explode(',', $order->getCustomerId());
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
                /*if($customerStatus['dormant']){
                    $dormantCount++;
                }*/

    		}

    		
            /*$activePercent = ($activeCount / $totalCustomerForRegion);
            $activeCustomerRatio = number_format( $activePercent * 100, 2 );

            $semiActivePercent = ($semiActiveCount / $totalCustomerForRegion);
            $semiActiveCustomerRatio = number_format( $semiActivePercent * 100, 2 );

            $inActivePercent = ($inActiveCount / $totalCustomerForRegion);
            $inActiveCustomerRatio = number_format( $inActivePercent * 100, 2 );*/

            /*$dormantPercent = ($dormantCount / $totalCustomerForRegion);
            $dormantCustomerRatio = number_format( $dormantPercent * 100, 2 );*/
            
            $region = Mage::getModel('directory/region')->load($order->getRegionId());
            $state_name = $region->getName(); 

            

            $customerCollectionForDormant = Mage::getModel('customer/customer')->getCollection()
                                    ->addAttributeToFilter('cus_state',$order->getRegionId())
                                    ->addAttributeToFilter('group_id','4')
                                    ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to));
            $customerCollectionForDormantCount = $customerCollectionForDormant->count();

            $customerids = $customerCollectionForDormant->getColumnValues('entity_id');
           // print_r($customerids);
            $customersOrders = Mage::getModel('sales/order')->getCollection();
            
            $customersOrders->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region_id'));
           // $customersOrders->getSelect()->group('sfoa.region_id');
            $customersOrders->addFieldToSelect(array('customer_id'));
            $customersOrders->addAttributeToFilter('sfoa.region_id',$order->getRegionId());
            $customersOrders->addFieldToFilter('customer_id',array('in'=>$customerids));
            $customersOrders->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
            $customersOrdersCount = count(array_unique($customersOrders->getColumnValues('customer_id')));
            //print_r($billedCustomers);
            $dormantCount = $customerCollectionForDormantCount - $customersOrdersCount;

            $totalCustomerCount = $activeCount+$semiActiveCount+$inActiveCount+$dormantCount;




            $activePercent = ($activeCount / $totalCustomerCount);
            $activeCustomerRatio = number_format( $activePercent * 100, 2 );

            $semiActivePercent = ($semiActiveCount / $totalCustomerCount);
            $semiActiveCustomerRatio = number_format( $semiActivePercent * 100, 2 );

            $inActivePercent = ($inActiveCount / $totalCustomerCount);
            $inActiveCustomerRatio = number_format( $inActivePercent * 100, 2 );

            $dormantPercent = ($dormantCount / $totalCustomerCount);
            $dormantCustomerRatio = number_format($dormantPercent * 100, 2 );


            $totalActiveCount = $activeCount+$semiActiveCount+$inActiveCount;
            $totalActiveRatio = $activeCustomerRatio+$semiActiveCustomerRatio+$inActiveCustomerRatio;

            
            $totalCustomerRatio = $activeCustomerRatio+$semiActiveCustomerRatio+$inActiveCustomerRatio+$dormantCustomerRatio;

    		$html .= "<tr>
                    <th width='300px' align='left'>".$state_name."</th>
                    <td width='200px' align='center'>".$totalCustomerCount."</td>
                    <td width='200px' align='center'>".number_format($totalActiveRatio,2)."% (".$totalActiveCount.")</td>
                    <td width='200px' align='center'>".number_format($dormantCustomerRatio,2)."% (".$dormantCount.")</td>
                </tr>";
    		
    	}

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
        //Mage::app()->getStore()->setConfig('catalog/frontend/flat_catalog_product', 0);
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
		//echo "<pre>";
	 	//print_r($attributeInfo->getSource()->getAllOptions(false));die;
      	$asms = $attributeInfo->getSource()->getAllOptions(false);
 		
 		/*header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=stock.csv');
        $export = fopen('php://output', 'w');*/
        
        //$header = array('ARM/CRM','City','Partner Billed Store Name','Daily Revenue','Monthly Revenue');
        $asmRevenueGenerated = "<table border='1'><tr><td>ASM</td><td>NO. OF PARTNERS BILLED DAILY</td><td>DAILY REVENUE GENERATED</td><td>NO. OF PARTNERS BILLED MONTHLY</td><td>MONTHLY REVENUE GENERATED</td></tr>";
        $csvData = "<table border='1'><tr><td>ARM/CRM</td><td>City</td><td>Partner Billed Store Name</td><td>Daily Revenue</td><td>Monthly Revenue</td></tr>";
        //fputcsv($export, $header);
      	foreach ($asms as $key => $value) {
      		if($value['value'] == '') continue;
      		$customers = Mage::getModel('customer/customer')->getCollection()
      						->addAttributeToSelect('*')
      						->addAttributeToFilter('asm_map',$value['value']);
      		$asmMonthlyRevenue = 0; $asmDailyRevenue = 0; $asmsCustomerDailyCount = 0; $asmsCustomerMonthlyCount = 0;
      		foreach ($customers as $customer) {
      			$storeName = $customer->getData('affiliate_store');
      			$city = $customer->getData('cus_city');
      			$asm_mapped = $customer->getResource()->getAttribute('asm_map')->getFrontend()->getValue($customer); 
      			
      			$today =  date('Y-m-d');
      			date_default_timezone_set('Asia/Kolkata');
		        
      			$toDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
            	$fromDate = date('Y-m-d 00:00:00', strtotime($toDate));
            	$fromDate = gmdate('Y-m-d H:i:s',strtotime($fromDate));
		        $toDate = gmdate('Y-m-d H:i:s',strtotime($toDate));
            
      			$dailyRevenue = Mage::getModel('sales/order')->getCollection();
      			$dailyRevenue->addFieldToSelect(array('grand_total'));
      			$dailyRevenue->addFieldToFilter('customer_id',$customer->getId());
      			$dailyRevenue->addFieldToFilter('created_at',array('from'=>$fromDate,'to'=>$toDate));
      			$dailyRevenue->getSelect()->columns('SUM(grand_total) as total_revenue');
      			$dailyRevenue = $dailyRevenue->getData();
      			$asmDailyRevenue = $asmDailyRevenue + $dailyRevenue[0]['total_revenue'];
      			if($dailyRevenue[0]['total_revenue'] > 0){
      				$asmsCustomerDailyCount = $asmsCustomerDailyCount + 1; 
      			}

      			$monthToDate = date('Y-m-d 23:59:59', strtotime("-1 day",strtotime($today)));
            	$monthFromDate = date('Y-m-d 00:00:00', strtotime("first day of this month",strtotime($monthToDate)));
            	$monthFromDate = gmdate('Y-m-d H:i:s',strtotime($monthFromDate));
		        $monthToDate = gmdate('Y-m-d H:i:s',strtotime($monthToDate));
            
      			$monthlyRevenue = Mage::getModel('sales/order')->getCollection();
      			$monthlyRevenue->addFieldToSelect(array('grand_total'));
      			$monthlyRevenue->addFieldToFilter('customer_id',$customer->getId());
      			$monthlyRevenue->addFieldToFilter('created_at',array('from'=>$monthFromDate,'to'=>$monthToDate));
      			$monthlyRevenue->getSelect()->columns('SUM(grand_total) as total_revenue');
      			$monthlyRevenue = $monthlyRevenue->getData();
      			$asmMonthlyRevenue = $asmMonthlyRevenue + $monthlyRevenue[0]['total_revenue'];
      			if($monthlyRevenue[0]['total_revenue'] > 0){
      				$asmsCustomerMonthlyCount = $asmsCustomerMonthlyCount + 1; 
      			}

				$data = array($asm_mapped,$city,$storeName,$dailyRevenue[0]['total_revenue'],$monthlyRevenue[0]['total_revenue']);
				$csvData .= "<tr><td>".$asm_mapped."</td><td>".$city."</td><td>".$storeName."</td><td>".$dailyRevenue[0]['total_revenue']."</td><td>".$monthlyRevenue[0]['total_revenue']."</td></tr>";
				//print_r($data);
				//fputcsv($export, $data);


				
      		}

      		$asmRevenueGenerated .= "<tr><td>".$value['label']."</td><td>".$asmsCustomerDailyCount."</td><td>".$asmDailyRevenue."</td><td>".$asmsCustomerMonthlyCount."</td><td>".$asmMonthlyRevenue."</td></tr>"; 
      		//$asmRevenueGenerated[$value['value']]['dailyRevenue'] = $asmDailyRevenue; 
      		
      		
           // $data = array($customer->getAttributeText('asm_map'),$customer->getCusCity(),$customer->getStoreName());
        }
        $asmRevenueGenerated .= '</table>';
        $csvData .= '</table>';
        return array('csvData'=>$csvData,'asmRevenueGenerated'=>$asmRevenueGenerated);
        //fclose($export);


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
            //print_r($customerOrders->count());
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
	 