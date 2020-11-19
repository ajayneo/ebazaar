<?php
class Neo_Trackyourorder_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	    $this->loadLayout();   
	    $this->getLayout()->getBlock("head")->setTitle($this->__("Track Your Order"));

       	$this->renderLayout(); 
	  
    }

    public function trackorderAction() {
    	$request = $this->getRequest()->getParams();
        if($request['type'] == ''){
            $order_collection = Mage::getModel('sales/order')->getCollection()
                            ->addFieldToFilter('increment_id',$request['order'])
                            ->getFirstItem();
            // echo $order_collection->getShippingAddress()->getTelephone();exit; || $order_collection->getShippingAddress()->getTelephone() == $request['mobile']
            $tele = '1';
            if($order_collection->getShippingAddress()){
                $tele = $order_collection->getShippingAddress()->getTelephone();
            }
            if(count($order_collection) == 1){  
                    if($order_collection['customer_email'] == $request['email'] || $tele == $request['mobile']){
                        $this->loadLayout();   
                        $this->getLayout()->getBlock("head")->setTitle($this->__("Track Your Order"));
                        $this->renderLayout(); 
                    }else{                        
                        Mage::getSingleton('core/session')->addError('Order id and entered data does not match.');
                        $this->_redirect('track/order/index');
                    }
            }else{
                Mage::getSingleton('core/session')->addError('Order id does not exist.');
                $this->_redirectReferer(($_SESSION['last_url']));
            }
        }else{
            $this->loadLayout();   
            $this->getLayout()->getBlock("head")->setTitle($this->__("Track Your Order"));
            $this->renderLayout();
        }
    	    
    }
}