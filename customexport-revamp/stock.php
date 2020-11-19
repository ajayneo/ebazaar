<?php require_once('../app/Mage.php');
umask(0);
Mage::app();

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
$collection->getSelect()->order('name ASC')->having('qty>0');

    
$filename = "stock_".date('dmY').".csv";
$file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;

$filepath = fopen($file,"w");
$header = array('SKU','PRODUCT NAME','CATEGORY','QTY','PRICE');     
fputcsv($filepath, $header);
 foreach ($collection as $product) {
    $categories = $product->getCategoryIds();
    if($categories[1]){
        $parent_cat = $categories[1];
    }else{
        $parent_cat = $categories[0];
    }

    $_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($parent_cat);
    //$_product = Mage::getModel('catalog/product')->load($product->getId());
    $category_name = $_cat->getName();
    $product_qty = $product->getQty();
   // $product_price = $product->getPrice();
    $product_name = $product->getName();
    $product_price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice());
    $sku = $product->getSku();
 	fputcsv($filepath, array($sku,$product_name,$category_name,$product_qty,$product_price));
 	// exit;
 }
 fclose($filepath);
// echo $file;

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($file)); 
echo readfile($file);
exit;
?>