<?php require_once('../app/Mage.php');
umask(0);
Mage::app();
date_default_timezone_set('Asia/Calcutta');


//phpinfo();


exit();

$customer_mobile = array('9001431438','8104448533','9928984363','9828045598','9828130474','8302606899','7014371800','9785600445','7378105784','7742680911','9887774007','9785821829','9828100695','8078648347','9351355551','8209872396','8290731108','7300050069','9660059109','9950117401','9413280000','8104223084');

echo "count customers = ".count($customer_mobile);

$customerCollection = Mage::getModel('customer/customer')->getCollection();
$customerCollection->addFieldToFilter('mobile',array('IN'=>$customer_mobile));
// $customers = $customerCollection->getData();
// print_r($customers);

foreach ($customerCollection as $customer) {
    // echo "<br>".$customer->getEmail();
    //echo "<pre>"; print_r($customer->getData());
    //existig asmp_map == 57 //amit bhushan sharma
    //new asm_map == 39 shailesh singhal 
    $customerModel = Mage::getModel('customer/customer')->load($customer->getEntityId());
    $customerModel->setAsmMap('39');
    $customerModel->save();
    echo "<pre>"; 
    print_r($customerModel->getData());
    exit;
}
exit();

$banner_model = Mage::getModel('bannerslider/banner')->getCollection()->addFieldToFilter('bannerslider_id','1');
        $banners = array();
        $i = 0;
        $nes = '';
        foreach($banner_model as $banner){
            if($banner['status'] == '0'){   
                $productName = explode('/', $banner['click_url']);
                
                echo $productName = end($productName);
                $productRewrite = Mage::getModel('core/url_rewrite');
                $productRewrite->setStoreId(1);
                $productRewrite->loadByRequestPath($productName);
                echo "<pre>";
                // print_r($productRewrite->getData());
                echo $product_id = $productRewrite->getProductId();

            }        
        }
exit();
//Mage::getModel('navisionexport/customexport')->bestsellers();
//database read adapter 
$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
  $last_order_id = Mage::getStoreConfig('sidebanner_section/dashboard/dailysales');
$query = "SELECT cvar.name as 'category_name', a.city, i.row_total_incl_tax as amount, i.qty_ordered as qty FROM sales_flat_order_item as i INNER JOIN catalog_category_product as c ON c.product_id = i.product_id INNER JOIN catalog_category_flat_store_1 as cvar ON c.category_id = cvar.entity_id LEFT JOIN sales_flat_order as o ON o.entity_id = i.order_id INNER JOIN sales_flat_order_address as a ON a.parent_id = o.entity_id AND a.city IN ('Mumbai','Chennai','Hyderabad','Bangalore','Calcutta','Pune','Ahmedabad','Jaipur','Surat','Lucknow','Delhi-NCR','Patna','Bhopal','Indore','Kanpur') AND a.address_type='shipping' WHERE o.increment_id > '".$last_order_id."'";

$results = $read->fetchAll($query); 
// print_r($results[0]);
//var_dump($results[0]);
// $cnt = 0;

$cat_array = array();
foreach($results as $r)
{
    // echo "<pre>";
    // print_r($r);
    // echo "</pre>";
    $cat_array[$r['category_name']][$r['city']]['qty'] += $r['qty'];
    $cat_array[$r['category_name']][$r['city']]['amount'] += $r['amount'];

    //$cnt++;
    //if($cnt==10) break;
}
echo "<pre>";
// print_r($cat_array);
$categories_head = array_keys($cat_array);
// print_r($categories);
$table_html ='';
$table_html .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
$table_html .= '<tr>';
$table_html .= '<th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">QTY/AMOUNT</th>';
foreach ($categories_head as $key => $value) {
    $table_html .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value.'</th>';
}
$table_html .= '</tr>';

foreach ($cat_array as $categories => $cities) {
    // echo $categories;
    foreach ($cities as $city => $value) {
        // echo trim($city).'=> '.$value['qty'].'/'.$value['amount'].'<br>';
        //print_r($value);
        $new_array[$city][$categories] = $value['qty'].'/'.$value['amount'];
    }

    //exit;
}
    // print_r($new_array);
foreach ($new_array as $city => $value) {
    $table_html .= '<tr>'; 
    $table_html .='<td style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$city.'</td>'; 
    foreach ($categories_head as $key => $cat){
        // print_r($value); exit;
        $table_html .='<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value[$cat].'</td>';
    }  
    $table_html .= '</tr>';    
}
$table_html .= '</table>';
echo $table_html;

exit;
// date_default_timezone_set('Asia/Calcutta');
	$curr_time = Mage::getModel('core/date')->gmtDate();

	$from = date('Y-m-d 00:00:00');
	$to = date('Y-m-d 23:59:59');

 	// number of products to display
    $productCount = 25; 
    
    // store ID
    $storeId    = Mage::app()->getStore()->getId();

 	$products = Mage::getResourceModel('reports/product_collection')
                    ->addAttributeToSelect('sku')        
                    ->addOrderedQty($from, $to)
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)                    
                    ->setOrder('ordered_qty', 'desc')
                    ->setPageSize($productCount); 
    
    Mage::getSingleton('catalog/product_status')
            ->addVisibleFilterToCollection($products);
    Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInCatalogFilterToCollection($products);
    $ordered_items = $products->getData();
    $print_array = array();
    foreach ($ordered_items as $key => $item) {
    	$print_array[$key]['sku'] = $item['sku']; 
    	$print_array[$key]['order_items_name'] = $item['order_items_name']; 
    	$print_array[$key]['ordered_qty'] = $item['ordered_qty']; 
    }

    $filename = "best_sellers_".date('Y_m_d').".csv";
    $filepath = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;
    
    $file = fopen($filepath,"w");
    $header = array('SKU','Product Name','Ordered Qty');
    fputcsv($file, $header);

    foreach ($print_array as $key => $value) {
         fputcsv($file, $value);
    }

    fclose($file);

    echo $url = Mage::getBaseUrl().'kaw17842knlpREGdasp9045/navision/export/'.$filename;

    //Mage::app()->getResponse()->setRedirect($url);

    ?>        