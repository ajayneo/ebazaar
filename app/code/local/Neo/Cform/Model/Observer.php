<?php
    class Neo_Cform_Model_Observer {
    	
		public function newTax($observer){
			$quote = $observer->getQuote();
			foreach ($quote->getAllItems() as $quoteItem) {
				if ($quote->getData('customer_cform') === 'true') {
					$product = $quoteItem->getProduct();
					//Mage::log($product->getSku() , null , 'sku_product.log');
					Mage::log('List of Category Ids',null,'productcatids.log');
					Mage::log($product->getCategoryIds(),null,'productcatids.log');
					$product_catids = end($product->getCategoryIds());
					Mage::log('Single Category Id',null,'productcatids.log');
					Mage::log($product_catids,null,'productcatids.log');
					$shipping_regionid = $quote->getShippingAddress()->getRegionId();
					$get_percentage_model = Mage::getModel('cform/cst');
					Mage::log($shipping_regionid , null , 'Shipping_region.log');
					Mage::log($product_catids , null , 'Product_category.log');
					$get_percentage = $get_percentage_model->unsetData()->getCollection()->addFieldToFilter('state',$shipping_regionid)->addFieldToFilter('category',array('finset'=>$product_catids));
					Mage::log($get_percentage->getData(),null,'productcatids.log');
					if(count($get_percentage)){
						if(count($get_percentage) > 1){
							$get_percentage_actual = $get_percentage_model->unsetData()->getCollection()->addFieldToFilter('state',$shipping_regionid)->addFieldToFilter('category',array('finset'=>$product_catids))->addFieldToFilter('min_amount',array('lteq' => $product->getFinalPrice()))->addFieldToFilter(array('max_amount','max_amount'),array(array('gt' =>$product->getFinalPrice()),array('null' => 'null')))->getFirstItem();
							Mage::log('If More than 1',null,'productcatids.log');
							Mage::log($get_percentage_actual->getData(),null,'productcatids.log');
							//$get_percentage = $get_percentage->addFieldToFilter('min_amount',array('lteq' => $product->getFinalPrice()))->addFieldToFilter(array('max_amount','max_amount'),array(array('gt' =>$product->getFinalPrice()),array('null' => 'null')));
						}elseif(count($get_percentage) == 1){
							$get_percentage_actual = $get_percentage->getFirstItem();
						}
						$cst_category = $get_percentage_actual->getCategory();
						$cst_category_array = explode(',',$cst_category);
						if(in_array($product_catids,$cst_category_array)){
							Mage::log($product->getName(),null,'CForm.log');
							Mage::log($product->getFinalPrice(),null,'CForm.log');
							Mage::log($get_percentage_actual->getPercentage(),null,'CForm.log');
							$cst_percentage = $get_percentage_actual->getPercentage();
							$product->setTaxClassId(0);
							$basePrice = $product->getFinalPrice() / (1+($cst_percentage/100));
							//$newTaxAmt = $basePrice * (2/100);
							//$finalPrice = $basePrice + $newTaxAmt;
							//Mage::log($finalPrice , null , 'newamount.log');
					        $product->setPrice($basePrice);
							$product->setTaxClassId(8);
						}
					}else{
						Mage::log('Not there',null,'cformnotavailable.log');
						$quote->setCustomerCform('false');
					}
	        	}
			}
		}
    }
?>