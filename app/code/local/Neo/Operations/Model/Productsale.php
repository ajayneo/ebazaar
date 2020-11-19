<?php

class Neo_Operations_Model_Productsale extends Mage_Core_Model_Abstract
{
	/*
    * @author : Sonali Kosrabe
    * @date : 3rd Jan 2018
    * @purpose : Product sale report.
    */
    public function getCategorySaleReport($to = NULL, $from = NULL, $freq = NULL){

    	$collection = Mage::getModel('sales/order_item')->getCollection();
				
		$collection->getSelect()->joinLeft(array('sfo' => 'sales_flat_order'), 'main_table.order_id = sfo.entity_id', array('sfo.customer_id','sfo.status'));
    	$collection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.order_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region'));
    	$collection->getSelect()->group('sfoa.region');

    	$collection->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
		$collection->addAttributeToFilter('sfo.status',array('neq'=>'canceled'));

		$collection->getSelect()->columns('SUM(main_table.qty_ordered) as total_qty, SUM(main_table.base_row_total_incl_tax) as total_sale,SUM(main_table.base_discount_amount) as total_discount,group_concat(main_table.item_id separator ",") as item_ids,group_concat(main_table.product_id separator ",") as product_ids, group_concat(main_table.order_id separator ",") as order_ids');

		$states = array();$data = array(); $titles = array();
		//echo "<pre>"; print_r($collection->getData());die;

		$category = Mage::getModel('catalog/category')->load(4); /// Mobile
		$subcategories = $category->getChildrenCategories(); 
		foreach ($subcategories as $subcategories) {
			if($subcategories->getLevel()==3){
				$_category = Mage::getModel('catalog/category')->load($subcategories->getId()); 
				$categoryName = $_category->getName();
				$cats[$_category->getId()] = $categoryName;
			}
		}
		$category = Mage::getModel('catalog/category')->load(3); /// Laptop
		$subcategories = $category->getChildrenCategories(); 
		foreach ($subcategories as $subcategories) {
			if($subcategories->getLevel()==3){
				$_category = Mage::getModel('catalog/category')->load($subcategories->getId()); 
				$categoryName = $_category->getName();
				$cats[$_category->getId()] = $categoryName;
			}
		}
		$category = Mage::getModel('catalog/category')->load(5); /// Tablet
		$subcategories = $category->getChildrenCategories(); 
		foreach ($subcategories as $subcategories) {
			if($subcategories->getLevel()==3){
				$_category = Mage::getModel('catalog/category')->load($subcategories->getId()); 
				$categoryName = $_category->getName();
				$cats[$_category->getId()] = $categoryName;
			}
		}
		$category = Mage::getModel('catalog/category')->load(7); /// Accessories
		$subcategories = $category->getChildrenCategories(); 
		foreach ($subcategories as $subcategories) {
			if($subcategories->getLevel()==3){
				$_category = Mage::getModel('catalog/category')->load($subcategories->getId()); 
				$categoryName = $_category->getName();
				$cats[$_category->getId()] = $categoryName;
			}
		}

		$cats[''] = 'Other';


			
		foreach ($collection as $item) {
			$itemIds = explode(',',$item->getItemIds());
			foreach ($itemIds as $itemId) {
				$orderItem = Mage::getModel('sales/order_item')->load($itemId);
				$_product = Mage::getModel('catalog/product')->load($orderItem->getProductId()); 
				$categories = $_product->getCategoryIds();
				$_category = Mage::getModel('catalog/category')->load($categories[1]); 
				$categoryName = $_category->getName();
				if(in_array($categories[1], $data[$item->getRegion()]['category'])){
					$data[$item->getRegion()]['qty'][$categories[1]] = $data[$item->getRegion()]['qty'][$categories[1]] + $orderItem->getQtyOrdered();
					$data[$item->getRegion()]['amount'][$categories[1]] = $data[$item->getRegion()]['amount'][$categories[1]] + $orderItem->getbase_row_total_incl_tax();
				}else{
					$data[$item->getRegion()]['category'][] = $categories[1]; 
					$data[$item->getRegion()]['qty'][$categories[1]] = $orderItem->getQtyOrdered();
					$data[$item->getRegion()]['amount'][$categories[1]] = $orderItem->getbase_row_total_incl_tax()-$orderItem->getbase_discount_amount();

				}
			}
			$data[$item->getRegion()]['total_qty'] = $item->getTotalQty();
			$data[$item->getRegion()]['total_sale'] = $item->getTotalSale() - $item->getTotalDiscount();
			$data[$item->getRegion()]['total_orders'] = count(array_unique(explode(',', $item->getOrderIds())));
		}
		
		
		$date1=date_create($from);
		$date2=date_create($to);
		$diff=date_diff($date1,$date2);
		
		$heading = '';$no_cat_column_display = 0;
		if($freq == 'Daily'){
			$heading = 'Daily Sales Report';
			$table_width = '100%';
			$caption_columns = (count($cats)*2) + 4;
			$no_cat_column_display = 0;
		}
		if($freq == 'Weekly'){
			$heading = 'Week Till Date';
			$table_width = '50%';
			$caption_columns = 4;
			$no_cat_column_display = 1;
		}
		if($freq == 'Monthly'){
			$heading = 'Month Till Date';
			$table_width = '50%';
			$caption_columns = 4;
			$no_cat_column_display = 1;
		}
		
		$html = "<table width='".$table_width."' border='1' cellpadding='5px' cellspacing='0' style='float:left;margin-right:30px;margin-bottom:30px; font-size:12px'><tr><td colspan='".$caption_columns."' style='padding:5px;'><h3 style='text-align:left;'>".$heading."</h3></td></tr>";
		if($no_cat_column_display == 0){
			$html .= "<tr>";
			$html .= "<th>&nbsp;</th>";
			foreach ($cats as $cat_id => $cat_name) {
				$html .= "<th colspan='2' width='100px' align='center'>".$cat_name."</th>";
			}
			$html .= "<th colspan='3' width='100px' align='center'>Total<br> Qty / Sale(in Rs.) / <br>Orders</th>";
			$html .= "</tr>";
		}

		$html .= "<tr>";
		$html .= "<th align='left'>State</th>";
		if($no_cat_column_display == 0){
			foreach ($cats as $cat_id => $cat_name) {
				$html .= "<th align='center' width='100px'>Qty</th>";
				$html .= "<th align='center' width='100px'>Sale</th>";
			}
		}
		$html .= "<th align='center'>Qty</th>";
		if($no_cat_column_display == 0){
			$html .= "<th align='right'>Sale</th>";
		}else{
			$html .= "<th align='right'>Sale (in Rs.)</th>";
		}
		$html .= "<th align='center'>Orders</th>";
		$html .= "</tr>";
		$stateTotalQty = 0; $stateTotalSale = 0;$stateTotalOrders =0;
		foreach ($data as $state => $value) {
			$html .= "<tr>";
			$html .= "<th align='left'>".$state."</th>";
			foreach ($cats as $cat_id => $cat_name) {
				if($no_cat_column_display == 0){
					if(in_array($cat_id, $value['category'])){
						$html .= "<td width='20px' align='center'>".(int)$data[$state]['qty'][$cat_id]."</td>";
						$html .= "<td width='80px' align='center'>".round($data[$state]['amount'][$cat_id])."</td>";
					}else{
						$html .= "<td width='20px' align='center'>&nbsp;</td>";
						$html .= "<td width='80px' align='center'>&nbsp;</td>";
					}
				}

				$categ[$cat_id]['totalQty'] = $categ[$cat_id]['totalQty'] + $data[$state]['qty'][$cat_id];
				$categ[$cat_id]['totalSale'] = $categ[$cat_id]['totalSale'] + round($data[$state]['amount'][$cat_id]);
			}
			$stateTotalQty = $stateTotalQty + $data[$state]['total_qty'];
			$stateTotalSale = $stateTotalSale + $data[$state]['total_sale'];
			$stateTotalOrders = $stateTotalOrders + $data[$state]['total_orders'];

			$html .= "<td width='20px' align='center'>".(int)$data[$state]['total_qty']."</td>";
			$html .= "<td width='80px' align='right'>".round($data[$state]['total_sale'])."</td>";
			$html .= "<td width='80px' align='center'>".$data[$state]['total_orders']."</td>";
			
			$html .= "</tr>";
		}

		$html .= "<tr><th width='100px' align='left'>Total Qty / Sale (in Rs.)</th>";
		if($no_cat_column_display == 0){
			
			foreach ($categ as $cat_id => $value) {
				$html .= "<th width='100px' align='center'>".$value['totalQty']."</th>";
				$html .= "<th width='100px' align='center'>".round($value['totalSale'])."</th>";
			}
		}
		$html .= "<th width='100px' align='center'>".$stateTotalQty."</th>";
		$html .= "<th width='100px' align='right'>".round($stateTotalSale)."</th>";
		$html .= "<th width='80px' align='center'>".$stateTotalOrders."</th>";
		$html .= "</tr>";
		$html .= "</table>";
		return $html;
		
    }


    public function productWiseSaleReport(){

    	$countryCode = 'IN';
    	$country = Mage::getModel('directory/country')->loadByCode($countryCode);
    	
    	$products =  Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect(array('name'))
			       // ->addAttributeToFilter('sku', array("in" => array('NEWMOB00412','NEWMOB00410','NEWMOB00454')));
			       ->addAttributeToFilter('sku', array("in" => array('NEWMOB00412','NEWMOB00481','NEWMOB00454')));

    	$html .= "<table border='1' cellspacing='0' cellpadding='10px' width='50%'>
    				<tr>
    				<th align='center'>".date('F',strtotime(date('Y-m-d')))."  ".date('Y',strtotime(date('Y-m-d')))."</th>";
    	foreach ($products as $product) {
    		$html .= "<th colspan='3' align='center'>".$product->getName()."</th>";
    	}
    	$html .= "</tr>";
    	

		$html .="<tr><th>State</th>";
		foreach ($products as $product) {
			
			$html .= "<th align='center'>".date('dS',strtotime(date('Y-m-d',strtotime('-1 day')))).' '.date('F',strtotime(date('Y-m-d')))."</th>
				<th align='center'>WTD</th>
				<th align='center'>MTD</th>";
				
		}
		$html .= "</tr>";
	    	
    	foreach ($country->getRegions() as $region) {
    		$html .="<tr><th>".$region->getName()."</th>";
    		foreach ($products as $product) {
    			$dailySale = $this->getProductSale($product->getSku(),$region->getRegionId(),'Daily');
    			$weeklySale = $this->getProductSale($product->getSku(),$region->getRegionId(),'Weekly');
    			$monthlySale = $this->getProductSale($product->getSku(),$region->getRegionId(),'Monthly');
    			$html .= "<td align='center'>".$dailySale."</td>
    				<td align='center'>".$weeklySale."</td>
    				<td align='center'>".$monthlySale."</td>";

    			$totalSale[$product->getSku()]['daily'] = $totalSale[$product->getSku()]['daily'] + $dailySale;
    			$totalSale[$product->getSku()]['weekly'] = $totalSale[$product->getSku()]['weekly'] + $weeklySale;
    			$totalSale[$product->getSku()]['monthly'] = $totalSale[$product->getSku()]['monthly'] + $monthlySale;  
			}
    		$html .= "</tr>";
    	}

    	$html .="<tr><th>Total</th>";
		foreach ($products as $product) {
			$html .= "<th align='center'>".$totalSale[$product->getSku()]['daily']."</th>
				<th align='center'>".$totalSale[$product->getSku()]['weekly']."</th>
				<th align='center'>".$totalSale[$product->getSku()]['monthly']."</th>";
				
		}
		$html .= "</tr>";

    	return $html .= "</table>";
    }

    function getProductSale($sku,$regionId,$type){
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


        $collection = Mage::getModel('sales/order_item')->getCollection();
				
		$collection->getSelect()->joinLeft(array('sfo' => 'sales_flat_order'), 'main_table.order_id = sfo.entity_id', array('sfo.customer_id','sfo.status'));
    	$collection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.order_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region'));
    	$collection->getSelect()->group('sfoa.region');

    	$collection->addAttributeToFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate));
		$collection->addAttributeToFilter('sfo.status',array('neq'=>'canceled'));
		$collection->addAttributeToFilter('sfoa.region_id',$regionId);
		$collection->addAttributeToFilter('main_table.sku',$sku);

		$collection->getSelect()->columns('SUM(main_table.qty_ordered) as total_qty');

		$collection = $collection->getData();
		$total_qty = $collection[0]['total_qty'];
		return (int)$total_qty;
    }

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

}
