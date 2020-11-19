<?php 
/**
** @Des: Stock list download
** @Auth: Mahesh Gurav
** @Date: 07 Feb 2018
**/
?>
<?php class Neo_Customapiv6_StockController extends Mage_Core_Controller_Front_Action{

	public function downloadAction(){
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
        $collection->getSelect()->order('name ASC')->having('qty>0');

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
}?>