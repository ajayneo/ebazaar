<?php
class Neo_Quikr_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Quikr"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Quikr"),
                "title" => $this->__("Quikr")
		   ));

      $this->renderLayout(); 
	  
    }

    public function successAction()
    {
      $this->loadLayout();   
      $this->getLayout()->getBlock("head")->setTitle($this->__("Quikr"));
          $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
       ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Quikr"),
                "title" => $this->__("Quikr")
       ));

      $this->renderLayout(); 
    }

    public function failureAction()
    {
      $this->loadLayout();   
      $this->getLayout()->getBlock("head")->setTitle($this->__("Quikr"));
          $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
       ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Quikr"),
                "title" => $this->__("Quikr")
       ));

      $this->renderLayout(); 
    }
}