<?php class Neo_Customapiv6_Model_Cart extends Mage_Core_Model_Abstract{
	//Function to validate preowned laptops quantities in cart to set 5
	//Mahesh Gurav
	//14th May 2018
	public function preownedLaptops($quote){
		if($quote->hasItems()){
        	$items = $quote->getAllItems();
        	$check_count = 0;
            $count_two = 0;
            $high_end = array('QCNBAG00706','QCNBBG00007','QCNBBG00066','QCNBAG00697','QCNBAG00708','QCNBBG00009','QCNBBG00082','QCNBAG00701','QCNBBG00017','QCNBBG00035','QCNBBG00071','QCNBAG00696','QCNBA000493','QCNBBG00020','QCNBBG00164','QCNBAG00520','QCNBAG00700','QCNBBG00018','QCNBBG00019','QCNBBG00025','QCNBBG00096','QCNBBG00021');

            $preowned_laptops = array('11','148');
            $error_message = "";
            $error = false;
            $count_free = 0;
            foreach ($items as $item) {
                //new logic of 1 units on high end products on 23rd Aug 2018 Mahesh Gurav
                // if(in_array($item->getSku(), $high_end)){
                $item_price = (int) $item->getPriceInclTax();
                // var_dump($item_price); exit;
                if($item_price >= 20000){
                    $count_two += $item->getQty();
                }
                

                $category_ids = $item->getProduct()->getCategoryIds();
                $is_preowned_laptops = array_intersect($preowned_laptops, $category_ids);
                if(count($is_preowned_laptops) > 0 && $item_price < 20000){
                    $check_count += $item->getQty();
                }

                if($item->getSku() == "FREEVOW103"){
                    $count_free += $item->getQty();
                }
            }

            //logic set on 23rd Aug 2018 Mahesh Gurav
            if($count_two > 0){
                $error = false;
            }else{
                if($check_count > 0 && $check_count < 5){
                $error = true;
                $error_message = "For Preowned Laptops requires minimum 5 quantites for checkout";
                }

                if($check_count > 0 && $count_free > 0 && $check_count !== $count_free){
                $error = true;
                $error_message = "Free Product's quantity is invalid for this cart";
                }      
            }
            
        }

    	$result = json_encode(array("status"=>$error,"message"=>$error_message));
        return $result;
	}	

    //Add MOQ function for Openbox Laptops and Refurbished Laptops combined
    //Mahesh Gurav
    //10 Jul 18
    public function openboxLaptops($quote){
        if($quote->hasItems()){
            $items = $quote->getAllItems();
            $check_count = 0;
            foreach ($items as $item) {
                $category_ids = $item->getProduct()->getCategoryIds();
                //check openbox laptops category present
                // if(in_array(60, $category_ids)){
                //     $check_count += $item->getQty();
                // }

                //check refurbished laptops category present on 10 Jul 18
                // if(in_array(99, $category_ids)){
                //     $check_count += $item->getQty();
                // }
            }
            if($check_count > 0 && $check_count < 5){
                return false;
            }
        }
        return true;
    }

    //offered latops on which free vow103 is available
    public function getOfferProduct(){
        return $this->getpreownedlist();
        // return array('23468');
    }

    public function getFreeProduct(){
        // return '21842';
        return '23596';
    }

    public function getFreeSku(){
        // return 'NEWMOB00481';
        return 'FREEVOW103';
    }

    public function getFreeRule(){
        return '3652';
    }

    public function getpreownedlist(){
        $storeId = Mage::app()->getStore()->getId();
        //Preowned laptops
        $categoryIds = array('11','148');
        $productCollection = Mage::getModel('catalog/product')->getCollection();

        $joinConditions = array(
            'cat_index.product_id=e.entity_id',
            $productCollection->getConnection()->quoteInto('cat_index.category_id IN (?)', $categoryIds),
            $productCollection->getConnection()->quoteInto('cat_index.store_id=?', $storeId)
        );

        $productCollection->getSelect()->join(
                array('cat_index' => $productCollection->getTable('catalog/category_product_index')),
                implode(' '.Zend_Db_Select::SQL_AND.' ', $joinConditions),
                array('cat_index_position' => 'position', 'cat_id' => 'category_id')
            )
            ->order('cat_index.category_id');

        $productCollection
            ->addAttributeToFilter('status', 1)
            // ->addAttributeToSelect(array('id','name','price','small_image'))
            ->addAttributeToSelect(array('id'))
            ->addUrlRewrite();
        $product_array = array();
        foreach ($productCollection as $item) {
            $product_array[] = $item->getId();
        }

        return $product_array;
    }

    public function desktops($quote){
        if($quote->hasItems()){
            $items = $quote->getAllItems();
            $count_desktops = 0;

            foreach ($items as $item) {
                $attribute_set = (int) $item->getProduct()->getAttributeSetId();
                $category_ids = $item->getProduct()->getCategoryIds();
                if(in_array(113, $category_ids)){
                    $count_desktops += $item->getQty();
                }
            }

            if($count_desktops > 0 && $count_desktops < 3){
                return false;
            } 
            
        }
        return true;
    }

    //vow mobiles validation on checkout
    public function validateVow($quote){
        if($quote->hasItems()){
            $items = $quote->getAllItems();
            $check_count = 0;
            $count_free = 0;
            $preowned_laptops = array('11','148');
            foreach ($items as $item) {
                $category_ids = $item->getProduct()->getCategoryIds();
                $is_preowned_laptops = array_intersect($preowned_laptops, $category_ids);
                if(count($is_preowned_laptops) > 0){
                    $check_count += $item->getQty();
                }

                if($item->getSku() == "FREEVOW103"){
                    $count_free += $item->getQty();
                }
            }

            if($check_count > 0 && $check_count !== $count_free){
                return false;
            }    
            
        }

        return true;
    }
}
?>