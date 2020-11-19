<?php
class Neo_Gadget_LaptopsController extends Mage_Core_Controller_Front_Action{

public function AcerAction() {  
	  	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Acer"));   
	      $this->renderLayout(); 
    }

public function AsusAction() {  
	  	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Asus"));   
	      $this->renderLayout(); 
    }

    public function DellAction() {  
	  	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Dell"));   
	      $this->renderLayout(); 
    } 

    public function HPAction() {  
	  	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("HP"));   
	      $this->renderLayout(); 
    }

    public function LenovoAction() {  
	  	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Lenovo"));   
	      $this->renderLayout(); 
    }

    public function otherAction() {  
	  	  $this->loadLayout();   
	  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Other"));   
	      $this->renderLayout(); 
    }

}

