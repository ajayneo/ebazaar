<?php

class Neo_Ebautomation_Adminhtml_PriceupdatelogController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('ebautomation/priceupdatelog');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout();//->_setActiveMenu("ebautomation/priceupdatelog")->_addBreadcrumb(Mage::helper("adminhtml")->__("Priceupdatelog  Manager"),Mage::helper("adminhtml")->__("Priceupdatelog Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Ebautomation"));
			    $this->_title($this->__("Manager Priceupdatelog"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Ebautomation"));
				$this->_title($this->__("Priceupdatelog"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("ebautomation/priceupdatelog")->load($id);
				if ($model->getId()) {
					Mage::register("priceupdatelog_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("ebautomation/priceupdatelog");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Priceupdatelog Manager"), Mage::helper("adminhtml")->__("Priceupdatelog Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Priceupdatelog Description"), Mage::helper("adminhtml")->__("Priceupdatelog Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("ebautomation/adminhtml_priceupdatelog_edit"))->_addLeft($this->getLayout()->createBlock("ebautomation/adminhtml_priceupdatelog_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("ebautomation")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Ebautomation"));
		$this->_title($this->__("Priceupdatelog"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("ebautomation/priceupdatelog")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("priceupdatelog_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("ebautomation/priceupdatelog");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Priceupdatelog Manager"), Mage::helper("adminhtml")->__("Priceupdatelog Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Priceupdatelog Description"), Mage::helper("adminhtml")->__("Priceupdatelog Description"));


		$this->_addContent($this->getLayout()->createBlock("ebautomation/adminhtml_priceupdatelog_edit"))->_addLeft($this->getLayout()->createBlock("ebautomation/adminhtml_priceupdatelog_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						$model = Mage::getModel("ebautomation/priceupdatelog")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Priceupdatelog was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setPriceupdatelogData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setPriceupdatelogData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("ebautomation/priceupdatelog");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
}
