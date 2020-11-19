<?php
class Neo_Easyfinance_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Easy Finance"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Easy Finance"),
                "title" => $this->__("Easy Finance")
		   ));

      $this->renderLayout(); 
	  
    }

    public function saveAction()
    {
      $post_data=$this->getRequest()->getPost();
      if ($post_data) {
        try {
            $model = Mage::getModel("easyfinance/easyfinance")
            ->addData($post_data)
            ->setId($this->getRequest()->getParam("id"))
            ->save();

            Mage::getSingleton("core/session")->addSuccess("Easyfinance was successfully saved");
            Mage::getSingleton("core/session")->setEasyfinanceData(false);

            if ($this->getRequest()->getParam("back")) {
              $this->_redirect("*/*/edit", array("id" => $model->getId()));
              return;
            }
            $this->_redirect("*/*/");
            return;
          } 
          catch (Exception $e) {
            Mage::getSingleton("core/session")->addError($e->getMessage());
            Mage::getSingleton("core/session")->setEasyfinanceData($this->getRequest()->getPost());
            $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
          return;
          }

        }
        $this->_redirect("*/*/");
    }
}