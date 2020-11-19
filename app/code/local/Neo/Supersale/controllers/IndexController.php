<?php
class Neo_Supersale_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
      $post_data = $this->getRequest()->getPost();

      $this->saveFormData($post_data);

  	  $this->loadLayout();   
  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Super Sale"));
      $this->renderLayout(); 
	  
    }

    protected function saveFormData($post_data)
    {
		if ($post_data) {

			//print_r($post_data);exit;

			try {				

				$model = Mage::getModel("supersale/super")
				->addData($post_data)
				->save(); 

				return;
			} 
			catch (Exception $e) {
				//Mage::getSingleton("core/session")->addSuccess(Mage::helper("adminhtml")->__("Your Number is already register"));
				$url = Mage::getUrl().'supersale/index/oooh/';  
				Mage::app()->getFrontController()->getResponse()->setRedirect($url); 
				//$this->_redirect("supersale/index/oooh");
			}

		}

		//$this->_redirect("*/*/");

		return;
    }

    public function ooohAction()
    {
    	$this->loadLayout();   
  	  	$this->getLayout()->getBlock("head")->setTitle($this->__("Super Sale"));
      	$this->renderLayout(); 
    }
}