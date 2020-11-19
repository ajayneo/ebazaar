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

		$collection ->getSelect()->columns('SUM(main_table.qty_ordered) as total_qty, SUM(main_table.base_row_total) as total_sale,group_concat(main_table.item_id separator ",") as item_ids,group_concat(main_table.product_id separator ",") as product_ids');
		//echo "<pre>";
		//print_r($collection->getData());
        
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
			}
		//}
		//print_r($data);
		$date1=date_create($from);
		$date2=date_create($to);
		$diff=date_diff($date1,$date2);
		//echo $diff->format("%R%a days");
		$freq = '';
		if($diff->format("%a days") == '1 days'){
			$freq = 'One Day';
		}
		if($diff->format("%a days") == '7 days'){
			$freq = 'Weekly';
		}
		if($diff->format("%a days") == '30 days'){
			$freq = 'Monthly';
		}
		
		$html = "<table width='100%' border='1' cellpadding='2px' cellspacing='0' style='float:left;'><caption><h1 style='text-align:left;'>".$freq." Sales Report from ".date('d/m/Y',strtotime($from))." to ".date('d/m/Y',strtotime($to))."</h1></caption>";
		$html .= "<tr>";
		$html .= "<th>&nbsp;</th>";
		foreach ($cats as $cat_id => $cat_name) {
			$html .= "<th colspan='2' width='100px' align='center'>".$cat_name."</th>";
		}
		$html .= "<th colspan='2' width='100px' align='center'>Total Qty/Sale</th>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<th>&nbsp;</th>";
		foreach ($cats as $cat_id => $cat_name) {
			$html .= "<th align='center'>Qty</th>";
			$html .= "<th align='center'>Sale</th>";
		}
		$html .= "<th align='center'>Qty</th>";
		$html .= "<th align='center'>Sale</th>";
		$html .= "</tr>";
		
		foreach ($data as $state => $value) {
			$html .= "<tr>";
			$html .= "<th>".$state."</th>";
			foreach ($cats as $cat_id => $cat_name) {

				if(in_array($cat_id, $value['category'])){
					$html .= "<td width='20px' align='center'>".(int)$data[$state]['qty'][$cat_id]."</td>";
					$html .= "<td width='80px' align='center'>".Mage::helper('core')->currency($data[$state]['amount'][$cat_id],true,false)."</td>";
				}else{
					$html .= "<td width='20px' align='center'>&nbsp;</td>";
					$html .= "<td width='80px' align='center'>&nbsp;</td>";
				}

				$categ[$cat_id]['totalQty'] = $categ[$cat_id]['totalQty'] + $data[$state]['qty'][$cat_id];
				$categ[$cat_id]['totalSale'] = $categ[$cat_id]['totalSale'] + $data[$state]['amount'][$cat_id];
			}
			$html .= "<th width='20px'>".(int)$data[$state]['total_qty']."</th>";
			$html .= "<th width='80px'>".Mage::helper('core')->currency($data[$state]['total_sale'],true,false)."</th>";

			$html .= "</tr>";
		}

		$html .= "<tr><th width='100px'>Total Qty/Sale</th>";
		foreach ($categ as $cat_id => $value) {
			$html .= "<th width='100px' align='center'>".$value['totalQty']."</th>";
			$html .= "<th width='100px' align='center'>".Mage::helper('core')->currency($value['totalSale'],true,false)."</th>";
		}
		$html .= "<th align='center' colspan='2'>&nbsp;</th>";
		$html .= "</tr>";
		$html .= "</table>";
		return $html;
    }

    

}
	 