<?php
class Neo_Lenovoprogram_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	     $this->loadLayout();   
	     $this->getLayout()->getBlock("head")->setTitle($this->__("Lenovo Program"));

      $this->renderLayout(); 
	  
    }
}