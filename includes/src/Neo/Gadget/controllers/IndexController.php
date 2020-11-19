<?php
class Neo_Gadget_IndexController extends Mage_Core_Controller_Front_Action{

    const XML_PATH_EMAIL_RECIPIENT  = 'gadget/gadget/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'gadget/gadget/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'gadget/gadget/email_template';
    const XML_PATH_ENABLED          = 'gadget/gadget/enabled';  

    public function IndexAction() {  
	  	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Gadget"));
	      $this->renderLayout(); 
	  
    }

    public function mobileAction(){
    	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Mobile"));
	      $this->renderLayout();
    }

    public function gadgetpostAction(){

    	$post_data=$this->getRequest()->getPost();

  
        if ($post_data) { 

            try { 
                $baseUrl = Mage::getBaseUrl();
                $post['name'] = $post_data['name'];       
                $post['brand'] = $post_data['brand'];       
                $post['price'] = $post_data['price'];       
                $post['switch'] = $post_data['switch'];       
                $post['pincode'] = $post_data['pincode'];       
                $post['city'] = $post_data['city'];       
                $post['email'] = $post_data['email'];       
                $post['mobile'] = $post_data['mobile'];   
                
                $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
                $product = Mage::getModel("gadget/gadget")->load($post_data['reference_id'])->getData();
                //$post['image'] = 'http://www.electronicsbazaar.com/media/emailcampaigns/Affiliates/site/iphone.png';       
                $post['image'] = $mediaUrl . $product['image'];       

                $model = Mage::getModel("gadget/request")
                ->addData($post_data)
                ->save();

                if($model->getId()>0){

                    $postObject = new Varien_Object();
                    $postObject->setData($post); 

                    $emailId = $post_data['email'].','.Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
                    $recipients = explode(",",$emailId);
                    $recipients[] = $post_data['email'];                      

                    $mailTemplate = Mage::getModel('core/email_template');
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */
                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post_data['email']) 
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $recipients,
                        null,
                        array('data' => $postObject) 
                    );
                }
                Mage::app()->getResponse()->setRedirect($baseUrl."gadget/index/success/")->sendResponse();
                
                return;
            } 
            catch (Exception $e) {
                $this->_redirect($baseUrl."gadget/index/failure/");
                return;
            }

        }
        $this->_redirect($baseUrl."gadget/index/failure/"); 
    }

    public function successAction(){
        $this->loadLayout();   
          $this->getLayout()->getBlock("head")->setTitle($this->__("Success"));
          $this->renderLayout();
    }

    public function failureAction(){
          $this->loadLayout();   
          $this->getLayout()->getBlock("head")->setTitle($this->__("Failure"));
          $this->renderLayout();
    }
     
    public function searchgadgetAction()
    {                     

        $attr = 'gadget'; 
        $brand = $this->getRequest()->getPost();
        $_product = Mage::getModel('catalog/product');
        $attr = $_product->getResource()->getAttribute($attr);
        if ($attr->usesSource()) {
            $gadgetId = $attr->getSource()->getOptionId($brand['brand']); 
        }

        $products = Mage::getModel('catalog/product')
                        ->getCollection() 
                        ->addAttributeToSelect('name')
                        ->addAttributeToFilter('name', array('like' => '%'.$brand['string'].'%'))
                        ->addAttributeToFilter($attr, $gadgetId);    

                   

        $html = '';
 


        foreach($products as $product)
        {  
            //target="_blank"
            $html .= '<a  href='.$product->getUrlInStore().' onclick="fillme('."'".$product->getName()."'".');">';
            $html .= '<div class="user_div">';
            //$html .= '<img src="'.(string)Mage::helper('catalog/image')->init($product, 'image')->resize(30).'" style="border-radius:5px;float:left;">';
            $html .= '<div class="name">'.$product->getName().'</div>';
            $html .= '</div>';
            $html .= '</a>';
        }
        
        if($html == ''){
            $html = '<div class="no_data">No Result Found !</div>';
        }
        echo $html;
    }
}  