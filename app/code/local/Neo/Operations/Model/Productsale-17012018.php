<?php

class Neo_Operations_Model_Productsale extends Mage_Core_Model_Abstract
{
    /*protected function _construct(){
       $this->_init("operations/productsale");

    }*/


    public function getCategorySaleReport($to = NULL, $from = NULL){

    	$collection = Mage::getModel('sales/order_item')->getCollection();
				
		$collection->getSelect()->joinLeft(array('sfo' => 'sales_flat_order'), 'main_table.order_id = sfo.entity_id', array('sfo.customer_id','sfo.status'));
    	$collection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.order_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region'));
    	$collection->getSelect()->group('sfoa.region');

    	$collection->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
		$collection->addAttributeToFilter('sfo.status',array('neq'=>'canceled'));

		$collection->getSelect()->columns('SUM(main_table.qty_ordered) as total_qty, SUM(main_table.base_row_total) as total_sale,group_concat(main_table.item_id separator ",") as item_ids,group_concat(main_table.product_id separator ",") as product_ids, group_concat(main_table.order_id separator ",") as order_ids');

		//$collection->getSelect()->columns();
		/*echo "<pre>";
		print_r($collection->getData());
        die;*/
		$states = array();$data = array(); $titles = array();


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
		
		//if(!empty($posts['group_by']) && $posts['group_by'] == 'state'){
			
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
						$data[$item->getRegion()]['amount'][$categories[1]] = $data[$item->getRegion()]['amount'][$categories[1]] + $orderItem->getRowTotal();
					}else{
						$data[$item->getRegion()]['category'][] = $categories[1]; 
						$data[$item->getRegion()]['qty'][$categories[1]] = $orderItem->getQtyOrdered();
						$data[$item->getRegion()]['amount'][$categories[1]] = $orderItem->getRowTotal();
					}

					//$data[$item->getRegion()]['category'][] = $categories[1].'-'.$orderItem->getQtyOrdered();
				}
				$data[$item->getRegion()]['total_qty'] = $item->getTotalQty();
				$data[$item->getRegion()]['total_sale'] = $item->getTotalSale();
				$data[$item->getRegion()]['total_orders'] = count(array_unique(explode(',', $item->getOrderIds())));
			}
		//}
		//print_r($data);die;
		$date1=date_create($from);
		$date2=date_create($to);
		$diff=date_diff($date1,$date2);
		//echo $diff->format("%R%a days");
		
		$freq = '';
		if($diff->format("%a days") == '1 days'){
			$freq = 'Daily';
			$table_width = '100%';
			$caption_columns = (count($cats)*2) + 4;
		}
		if($diff->format("%a days") == '7 days'){
			$freq = 'Weekly';
			$table_width = '35%';
			$caption_columns = 4;
		}
		if($diff->format("%a days") == '30 days'){
			$freq = 'Monthly';
			$table_width = '35%';
			$caption_columns = 4;
		}
		
		$html = "<table width='".$table_width."' border='1' cellpadding='5px' cellspacing='0' style='float:left;margin-right:20px;margin-top:20px; font-size:12px'><tr><td colspan='".$caption_columns."' style='background-color:rgb(255,192,0);padding:5px;'><h3 style='text-align:left;'>".$freq." Sales Report from ".date('d/m/Y',strtotime($from))." to ".date('d/m/Y',strtotime($to))."</h3></td></tr>";
		if($diff->format("%a days") == '1 days'){
			$html .= "<tr style='background-color:rgb(255,228,118);'>";
			$html .= "<th>&nbsp;</th>";
			foreach ($cats as $cat_id => $cat_name) {
				$html .= "<th colspan='2' width='100px' align='center'>".$cat_name."</th>";
			}
			$html .= "<th colspan='3' width='100px' align='center'>Total Qty/Sale/Orders</th>";
			$html .= "</tr>";
		}

		$html .= "<tr style='background-color:#17B7E8;'>";
		$html .= "<th align='left'>State</th>";
		if($diff->format("%a days") == '1 days'){
			foreach ($cats as $cat_id => $cat_name) {
				$html .= "<th align='right'>Qty</th>";
				$html .= "<th align='right'>Sale</th>";
			}
		}
		$html .= "<th align='right'>Qty</th>";
		$html .= "<th align='right'>Sale</th>";
		$html .= "<th align='right'>Orders</th>";
		$html .= "</tr>";
		$stateTotalQty = 0; $stateTotalSale = 0;$stateTotalOrders =0;
		foreach ($data as $state => $value) {
			$html .= "<tr>";
			$html .= "<th align='left'>".$state."</th>";
			foreach ($cats as $cat_id => $cat_name) {
				if($diff->format("%a days") == '1 days'){
					if(in_array($cat_id, $value['category'])){
						$html .= "<td width='20px' align='right'>".(int)$data[$state]['qty'][$cat_id]."</td>";
						$html .= "<td width='80px' align='right'>".round($data[$state]['amount'][$cat_id])."</td>";
					}else{
						$html .= "<td width='20px' align='right'>&nbsp;</td>";
						$html .= "<td width='80px' align='right'>&nbsp;</td>";
					}
				}

				$categ[$cat_id]['totalQty'] = $categ[$cat_id]['totalQty'] + $data[$state]['qty'][$cat_id];
				$categ[$cat_id]['totalSale'] = $categ[$cat_id]['totalSale'] + round($data[$state]['amount'][$cat_id]);
			}
			$stateTotalQty = $stateTotalQty + $data[$state]['total_qty'];
			$stateTotalSale = $stateTotalSale + $data[$state]['total_sale'];
			$stateTotalOrders = $stateTotalOrders + $data[$state]['total_orders'];

			$html .= "<td width='20px' align='right'>".(int)$data[$state]['total_qty']."</td>";
			$html .= "<td width='80px' align='right'>".round($data[$state]['total_sale'])."</td>";
			$html .= "<td width='80px' align='right'>".$data[$state]['total_orders']."</td>";
			
			$html .= "</tr>";
		}

		$html .= "<tr style='background-color:rgb(0,32,96);color:#fff;'><th width='100px'>Total Qty/Sale</th>";
		if($diff->format("%a days") == '1 days'){
			
			foreach ($categ as $cat_id => $value) {
				$html .= "<th width='100px' align='right'>".$value['totalQty']."</th>";
				$html .= "<th width='100px' align='right'>".round($value['totalSale'])."</th>";
			}
		}
		$html .= "<th width='100px' align='right'>".$stateTotalQty."</th>";
		$html .= "<th width='100px' align='right'>".round($stateTotalSale)."</th>";
		$html .= "<th width='80px' align='right'>".$stateTotalOrders."</th>";
		/*if($diff->format("%a days") == '1 days'){
			$html .= "<th align='center' colspan='2'>&nbsp;</th>";
		}*/
		$html .= "</tr>";
		$html .= "</table>";
		return $html;
    }

    

}
	 