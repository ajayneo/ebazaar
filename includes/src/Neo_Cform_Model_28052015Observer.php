<?php
    class Neo_Cform_Model_Observer {
    	
		public function newTax($observer){
			$quote = $observer->getQuote();
			foreach ($quote->getAllItems() as $quoteItem) {
				if ($quote->getData('customer_cform') === 'true') {
					$product = $quoteItem->getProduct();
					$product_catids = $product->getCategoryIds();
					$shipping_regionid = $quote->getShippingAddress()->getRegionId();
					$get_percentage_model = Mage::getModel('cform/cst');
					$get_percentage = $get_percentage_model->unsetData()->getCollection()->addFieldToFilter('state',$shipping_regionid);
					if(count($get_percentage)){
						foreach($product_catids as $product_catid){
							if(count($get_percentage) > 1){
								$get_percentage_actual = $get_percentage_model->unsetData()->getCollection()->addFieldToFilter('state',$shipping_regionid)->addFieldToFilter('min_amount',array('lteq' => $product->getFinalPrice()))->addFieldToFilter(array('max_amount','max_amount'),array(array('gt' =>$product->getFinalPrice()),array('null' => 'null')))->getFirstItem();								
								//$get_percentage = $get_percentage->addFieldToFilter('min_amount',array('lteq' => $product->getFinalPrice()))->addFieldToFilter(array('max_amount','max_amount'),array(array('gt' =>$product->getFinalPrice()),array('null' => 'null')));
							}elseif(count($get_percentage) == 1){
								$get_percentage_actual = $get_percentage->getFirstItem();
							}
							$cst_category = $get_percentage_actual->getCategory();
							$cst_category_array = explode(',',$cst_category);
							if(in_array($product_catid,$cst_category_array)){
								Mage::log($product->getName(),null,'CForm.log');
								Mage::log($product->getFinalPrice(),null,'CForm.log');
								Mage::log($get_percentage_actual->getPercentage(),null,'CForm.log');
								$cst_percentage = $get_percentage_actual->getPercentage();
								$product->setTaxClassId(0);
								$basePrice = $product->getFinalPrice() / (1+($cst_percentage/100));
								$newTaxAmt = $basePrice * (2/100);
								$finalPrice = $basePrice + $newTaxAmt;
					            $product->setPrice($finalPrice);
								$product->setTaxClassId(8);	
							}
						}	
					}
	        	}
			}
		}
    }
?>