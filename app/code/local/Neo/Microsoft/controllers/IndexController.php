<?php
class Neo_Microsoft_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
       
  	  $this->loadLayout();   
  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Microsoft Validation"));

      $this->renderLayout(); 
	  
    }

    public function valAction()
    {
      $_SESSION['microsoft_validation'] = 'microsoft_validation';
      echo $_SESSION['microsoft_validation'];
    }
}  