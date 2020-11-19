<?php
class Neo_Microsoft_Model_Observer
{

    public function cartlimit(Varien_Event_Observer  $observer)
    {
        $product = $observer->getProduct();

        $post = Mage::app()->getRequest()->getPost();

        if(false){
        
            //$cart = Mage::getSingleton('checkout/cart');
            //$cart->truncate();
            //$cart->add($product->getEntityId());

            $url = Mage::getUrl('checkout/onepage');
            $response = Mage::app()->getFrontController()->getResponse();
            $response->setRedirect($url);
            $response->sendResponse();
            //exit;

        } 
       
    }

    public function redirectToCheckout(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();

        if($product->getAttributeSetId() == 49){

            $response = $observer->getResponse();
            $request = $observer->getRequest();
            $response->setRedirect(Mage::getUrl('checkout/onepage'));
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
            Mage::getSingleton('checkout/session')->setMicrosoftDiscountRedirect(true);
        } 
    }

	public function salesOrderPlaceBefore(Varien_Event_Observer $observer)
	{
		//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
		//$user = $observer->getEvent()->getUser();
		//$user->doSomething();
	}

	public function in_array_r($item , $array){
	    return preg_match('/"'.$item.'"/i' , json_encode($array));
	}
		
	public function cartlimit__(Varien_Event_Observer  $observer)
    {
        $category_ids = array();
        $check = 0;

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach($quote->getAllVisibleItems() as $item){
              $product = Mage::getModel('catalog/product')->load($item->getProductId());
              //$product_category_ids = explode(",", $product->getCategoryIds());
              $product_category_ids = $product->getCategoryIds();

              array_push($category_ids, $product_category_ids);
                if($this->in_array_r(82 , $category_ids)){
				    $check = 1;
				}
      
        }



        $justAdded = $observer->getQuoteItem();


        $productJustAdded = Mage::getModel('catalog/product')->load($justAdded->getProductId());
        $productJustAdded_category_ids = $productJustAdded->getCategoryIds();
        $flat = call_user_func_array('array_merge', $category_ids);
        $uniqueArray = array_unique($flat);


    	if(count($uniqueArray) > 1 && $check == 1){
    		Mage::throwException('Cant Be Added');
    	}


        //total the category id in $category_ids
        //if $productJustAdded->getCategoryIds exist in $category_ids, 
        //then check to see if category id count greater than 3
        // if true then add error msg and try setting the qty to 0

        return $this;
    }
}
