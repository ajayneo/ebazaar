<?php

class Neo_Groupproductpurchase_Model_Observer

{



			public function salesOrderPlaceBefore(Varien_Event_Observer $observer)

			{//echo 'asd';exit;

				$cat_id = 112; //ASIS cat

				$category_ids = array();

				$qty = 0; 



		        $quote = Mage::getSingleton('checkout/session')->getQuote();

		        foreach($quote->getAllVisibleItems() as $item){



		        	  $qty = $qty + $item->getQty();

		              $product = Mage::getModel('catalog/product')->load($item->getProductId());

		              //$product_category_ids = explode(",", $product->getCategoryIds());

		              $product_category_ids = $product->getCategoryIds();



		              //print_r($product_category_ids);exit;



		              array_push($category_ids, $product_category_ids);

		        } 



		        //print_r($category_ids);exit;



		        foreach ($category_ids as  $value) {

		        	 if (in_array($cat_id, $value)){

		        	 	if($qty < 50){

		        	 		//Mage::getSingleton('checkout/session')->setPreownedAsis(null);

							$error = Mage::helper('shipping')->__('Asis product minimum qty is 50');
							//condition removed on Kushlesh Sir's Approval 10th April 2018
    						// Mage::throwException($error);

		        	 	}

		        	 }

		        }



				return $this;

			}



			public function cartlimitbefore(Varien_Event_Observer  $observer)

		    {//echo 'asdsss';exit;



		    	$cat_id = 112; //ASIS cat

		    	$cond = 0;

		        $category_ids = array();



		        $justAdded = $observer->getEvent()->getProduct();





		        $quote = Mage::getSingleton('checkout/session')->getQuote();

		        foreach($quote->getAllVisibleItems() as $item){



		        	  $qty = $qty + $item->getQty();



		        	  	$cond = 2;

		        	  	$product = Mage::getModel('catalog/product')->load($item->getProductId());

		              	//$product_category_ids = explode(",", $product->getCategoryIds());

		              	$product_category_ids = $product->getCategoryIds();



		              	array_push($category_ids, $product_category_ids);

		              

		        }





		        

		        foreach ($category_ids as  $value) {



		        	if (in_array($cat_id, $value)){

		        	 	

		        	 	$cond = 1;

		        	 }

		        }



		        $categoryIdAssis = $cat_id;    

				$productsInAssisCat = Mage::getSingleton('catalog/category')->load($categoryIdAssis)

				            ->getProductCollection()

				            ->addAttributeToSelect('entity_id');

				$checkNotAssisPro = array();

				foreach ($productsInAssisCat as $key => $value) {

					$checkNotAssisPro[] = $value['entity_id'];

				}

				     



                $product_category_ids_ja = $justAdded->getCategoryIds();



		        if($cond == 1 && !in_array($justAdded->getEntityId(), $checkNotAssisPro)){

		        	$error = Mage::helper('shipping')->__('Other product cant be added with Asis category product');

					Mage::throwException($error);

		        }elseif($cond == 2 && in_array($cat_id, $product_category_ids_ja)){  



		        	//echo $cond;exit;

		        	$error = Mage::helper('shipping')->__('Asis category product cant be added with other product');

					Mage::throwException($error);

		        }





		        return $this;

		    }



			public function cartlimit__(Varien_Event_Observer  $observer)

		    {//echo 'asdsss';exit;



		    	$cond = 0;

		        $category_ids = array();



		        $justAdded = $observer->getEvent()->getProduct();





		        $quote = Mage::getSingleton('checkout/session')->getQuote();

		        foreach($quote->getAllVisibleItems() as $item){



		        	  $qty = $qty + $item->getQty();



		        	  if($item->getProductId() != $justAdded->getEntityId()){

		        	  	$cond = 2;

		        	  	$product = Mage::getModel('catalog/product')->load($item->getProductId());

		              	//$product_category_ids = explode(",", $product->getCategoryIds());

		              	$product_category_ids = $product->getCategoryIds();



		              	array_push($category_ids, $product_category_ids);

		        	  }

		              

		        }





		        

		        foreach ($category_ids as  $value) {



		        	if (in_array(112, $value)){

		        	 	

		        	 	$cond = 1;

		        	 }

		        }



		        //echo "<pre>";

		        //echo $justAdded->getEntityId();

		        //print_r($category_ids);

		        //exit;



		        //if($cond == 1 && $justAdded->getEntityId() != '11258' && $justAdded->getEntityId() != '11259' && $justAdded->getEntityId() != '11260' && $justAdded->getEntityId() != '11261' && $justAdded->getEntityId() != '11262' && $justAdded->getEntityId() != '11263' && $justAdded->getEntityId() != '11264' && $justAdded->getEntityId() != '11265' && $justAdded->getEntityId() != '11267' && $justAdded->getEntityId() != '11268' && $justAdded->getEntityId() != '11269' && $justAdded->getEntityId() != '11270' && $justAdded->getEntityId() != '11271' && $justAdded->getEntityId() != '11272' && $justAdded->getEntityId() != '11274' && $justAdded->getEntityId() != '11275' && $justAdded->getEntityId() != '11276' && $justAdded->getEntityId() != '11277' && $justAdded->getEntityId() != '11278' && $justAdded->getEntityId() != '11280' && $justAdded->getEntityId() != '11282' && $justAdded->getEntityId() != '11283' && $justAdded->getEntityId() != '11284' && $justAdded->getEntityId() != '11285' && $justAdded->getEntityId() != '11286'){ 

		        	//echo '111';exit;

		        if($cond == 1 && $justAdded->getEntityId() != '10885'){

		        	$error = Mage::helper('shipping')->__('Other product cant be added with Asis category product');

					Mage::throwException($error);

		        }elseif($cond == 2){  



		        	//echo $cond;exit;

		        	$error = Mage::helper('shipping')->__('Asis category product cant be added with other product');

					Mage::throwException($error);

		        }



		        //echo $justAdded->getEntityId();exit; 

		        //$productJustAdded = Mage::getModel('catalog/product')->load($justAdded->getId());



		        //total the category id in $category_ids

		        //if $productJustAdded->getCategoryIds exist in $category_ids, 

		        //then check to see if category id count greater than 3

		        // if true then add error msg and try setting the qty to 0



		        return $this;

		    }



		    

		

			public function salesQuoteRemoveItem1(Varien_Event_Observer $observer)

			{//echo 'asd';exit;

				$item = $observer->getQuoteItem()->getProduct();

				$itemSku = $item->getSku();



				$isAdmin = Mage::app()->getStore()->isAdmin();



				if($isAdmin == ''){



					if($itemSku == 'MOBILE10007' || $itemSku == 'Mobile10013' || $itemSku == 'MOBILE10008' || $itemSku == 'test-fb'){  

						Mage::getSingleton('checkout/cart')->truncate();

						Mage::getSingleton('core/session')->addError(Mage::helper('groupproductpurchase')->__('Group Product Cannot be Purchased Individually Pleas Purchase below Products Together'));

						Mage::getSingleton('core/session')->addError(Mage::helper('groupproductpurchase')->__('Phicomm Energy 2 E670 (Black, 16 GB)  or Phicomm Energy E653 (Black, 8 GB) <br> And<br>Xiaomi Redmi Note 3 (Gold, 32 GB)'));

					}

				}



			}

		

			public function checkoutCartProductAddAfter(Varien_Event_Observer $observer)

			{

				$Event = $observer->getEvent();

                $product = $Event->getProduct();



                if (in_array(112, $product->getCategoryIds())){

                	$val = Mage::getSingleton('checkout/session')->getPreownedAsis();

                	

                	if($val == 0){



                		$error = Mage::helper('shipping')->__('Asis product cant be added with other category product');

    					Mage::throwException($error);





                	}else{

                		Mage::getSingleton('checkout/session')->setPreownedAsis(1);

                	}

                	//echo $val = Mage::getSingleton('checkout/session')->getPreownedAsis();

                	//echo '<pre>';

	                //print_r($product->getCategoryIds());  

	                exit;

                }else{



                	$val = Mage::getSingleton('checkout/session')->getPreownedAsis();

                	Mage::getSingleton('checkout/session')->setPreownedAsis(0);

                	

                	echo '<pre>';

	                print_r($product->getCategoryIds());

	                exit;

                }



				//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));

				//$user = $observer->getEvent()->getUser();

				//$user->doSomething();

			}



			



		    public function checkoutProductAddAfter11(Varien_Event_Observer  $observer)

		    {

		    	$Event = $observer->getEvent();

                $product = $Event->getProduct();



                print_r($product->getData());exit;

		    }

		

}

