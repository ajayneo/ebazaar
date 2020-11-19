<?php
class Neo_Gadget_Model_Observer
{

    const XML_PATH_EMAIL_RECIPIENT  = 'gadget/gadget/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'gadget/gadget/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'gadget/gadget/email_template';
    const XML_PATH_ENABLED          = 'gadget/gadget/enabled';

   public function logCartAdd(Varien_Event_Observer $observer) {

	    //Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/onepage"));
   	    $item = $observer->getQuoteItem();
	    $product = $item->getProduct();

	    $postData = Mage::app()->getRequest()->getPost();

	    $options = $product->getTypeInstance(true)->getOrderOptions($product);

	    $html = '';
	    if ($options)
	    {
	        if (isset($options['options'])) 
	        {
	            $result = $options['options'];
	        }
	        if(count($result)>0){
	            foreach($result as $key =>$value){
	            	$resultoptionlabel =  $value['label'];
	                $resultoptionvalue =  $value['value'];
	                if(strpos($resultoptionvalue, ',') != false){
	                	$html .= $resultoptionlabel.'<br>';
	                	$html .= $resultoptionvalue.'<br>';
	                }else{
	                	$html .= $resultoptionlabel.'<br>';
	                	$html .= $resultoptionvalue.'<br>';
	                }
	                
        		}
	    	}
	    }
	   	
        if ($postData) { 

            try { 
                $baseUrl = Mage::getBaseUrl();
                $post['name'] = $postData['name'];       
                $post['brand'] = 'Apple';       
                $post['price'] = 'Rs 1000';       
                $post['switch'] = $html;       
                $post['pincode'] = $postData['pincode'];       
                $post['city'] = $postData['city'];       
                $post['email'] = $postData['email'];       
                $post['mobile'] = $postData['mobile'];   
                
                $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
                $mediaUrl = Mage::getBaseUrl().'neweb/media/';
                $product = Mage::getModel("gadget/gadget")->load($post_data['reference_id'])->getData();
                //$post['image'] = 'http://www.electronicsbazaar.com/media/emailcampaigns/Affiliates/site/iphone.png';       
                $post['image'] = $mediaUrl . $product['image'];       
                $post['image'] = 'http://cdn.electronicsbazaar.com/media/catalog/product/cache/1/image/9df78eab33525d08d6e5fb8d27136e95/s/a/samsung-tab-s2-1_1.png';       

                $model = Mage::getModel("gadget/request")
                ->addData($post)
                ->save();

                if($model->getId()>0){

                    $postObject = new Varien_Object();
                    $postObject->setData($post); 

                    $emailId = $post['email'].','.Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
                    $recipients = explode(",",$emailId);
                    $recipients[] = $post['email'];                      

                    $mailTemplate = Mage::getModel('core/email_template');
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */
                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email']) 
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $recipients,
                        null,
                        array('data' => $postObject) 
                    );
                }
  

                Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/success/")->sendResponse();
                exit; 
            } 
            catch (Exception $e) {
                //$this->_redirect("gadget/index/failure/");
                Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/failure/")->sendResponse();
                exit; 
            }

        }

         Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/success/")->sendResponse();
        //Mage::app()->getResponse()->sendResponse();
        exit;

   }

   public function logCartAdd___(Varien_Event_Observer $observer) {

	    //Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/onepage"));
   	    $item = $observer->getQuoteItem();
	    $product = $item->getProduct();

	    $ss = Mage::app()->getRequest()->getPost();

        print_r($ss);
        

	    $options = $product->getTypeInstance(true)->getOrderOptions($product);

	    if ($options)
	    {
	        if (isset($options['options'])) 
	        {
	            $result = $options['options'];
	        }
	        if(count($result)>0){
	            foreach($result as $key =>$value){
	            	echo '<br>'.$resultoption =  $value['label'];
	                echo '<br>'.$resultoption =  $value['value'];
	                
	        }
	    	}
	    }
	    exit;
	   

	         Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."gadget/index/success/")->sendResponse();
            //Mage::app()->getResponse()->sendResponse();
            exit;

   }
		 
}
