<?php require_once('../app/Mage.php');
umask(0);
Mage::app();
date_default_timezone_set('Asia/Calcutta');
//Mage::getModel('navisionexport/customexport')->bestsellers();
//database read adapter 
$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
  $last_order_id = Mage::getStoreConfig('sidebanner_section/dashboard/dailysales');
// $query = "SELECT a.city, SUM(i.row_total_incl_tax) as amount, 500000 as target, concat(round(( SUM(i.row_total_incl_tax)/500000 * 100 ),2),'%') as 'achieved percent' FROM sales_flat_order_item as i INNER JOIN catalog_category_product as c ON c.product_id = i.product_id LEFT JOIN sales_flat_order as o ON o.entity_id = i.order_id INNER JOIN sales_flat_order_address as a ON a.parent_id = o.entity_id AND a.city IN ('Mumbai','Chennai','Hyderabad','Bangalore','Calcutta','Pune','Ahmedabad','Jaipur','Surat','Lucknow','Delhi-NCR','Patna','Bhopal','Indore','Kanpur') AND a.address_type='shipping' WHERE o.increment_id > '".$last_order_id."' GROUP BY a.city";
$query = "SELECT a.city, SUM(i.row_total_incl_tax) as amount FROM sales_flat_order_item as i INNER JOIN catalog_category_product as c ON c.product_id = i.product_id LEFT JOIN sales_flat_order as o ON o.entity_id = i.order_id INNER JOIN sales_flat_order_address as a ON a.parent_id = o.entity_id AND a.city IN ('Mumbai','Chennai','Hyderabad','Bangalore','Calcutta','Pune','Ahmedabad','Jaipur','Surat','Lucknow','Delhi-NCR','Patna','Bhopal','Indore','Kanpur') AND a.address_type='shipping' WHERE o.increment_id > '".$last_order_id."' GROUP BY a.city";

$results = $read->fetchAll($query); 
echo "<pre>";
// print_r($results);
//var_dump($results[0]);
// $cnt = 0;
$cat_array = array();
$target = 5000000;
foreach($results as $r)
{
    $cat_array[$r['city']]['Item Total'] = $r['amount'];
    $cat_array[$r['city']]['Target'] = $target;
    $percent = round(($r['amount']/$target * 100),2);   
    $cat_array[$r['city']]['Percent'] = $percent."%";
}
// echo "<pre>";
// print_r($cat_array);
$table_html ='';
$table_html .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
$table_html .= '<tr>';
$table_html .= '<th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">City</th><th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Item Total</th><th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Target</th><th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Target Achieved</th>';
$table_html .= '</tr>';
foreach ($cat_array as $city => $value) {
    $table_html .= '<tr>';
    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$city.'</td>';
    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.(int)$value['Item Total'].'</td>';
    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value['Target'].'</td>';
    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value['Percent'].'</td>';
    $table_html .= '</tr>';
}

$table_html .="</table>";
echo $table_html;
exit;
$header = array_keys($cat_array);
$table_html ='';
$table_html .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
$table_html .= '<tr>';
$table_html .= '<th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;"></th>';
foreach ($header as $key => $value) {
    $table_html .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value.'</th>';
}
$table_html .= '</tr>';

$rows = array('Item Total','Target','Percent');

foreach ($rows as $row) {
$table_html .= '<tr>';
$table_html .='<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$row.'</td>';
    foreach ($cat_array as $key => $value) {
        if($row == 'Percent'){
            $number = $value[$row];
        }else{
            $number = (int)$value[$row];
        }
    $table_html .='<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$number.'</td>';
    }
$table_html .= '</tr>';    
}

$table_html .= '</table>';


echo $table_html;
exit;
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

?>        