<?php

class Neo_Cform_Adminhtml_CstController extends Mage_Adminhtml_Controller_Action
{

		protected function _isAllowed(){
	        return true;
	    }
	    
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("cform/cst")->_addBreadcrumb(Mage::helper("adminhtml")->__("Cst  Manager"),Mage::helper("adminhtml")->__("Cst Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Cform"));
			    $this->_title($this->__("Manager Cst"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Cform"));
				$this->_title($this->__("Cst"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("cform/cst")->load($id);
				if ($model->getId()) {
					Mage::register("cst_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("cform/cst");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Cst Manager"), Mage::helper("adminhtml")->__("Cst Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Cst Description"), Mage::helper("adminhtml")->__("Cst Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("cform/adminhtml_cst_edit"))->_addLeft($this->getLayout()->createBlock("cform/adminhtml_cst_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("cform")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Cform"));
		$this->_title($this->__("Cst"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("cform/cst")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("cst_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("cform/cst");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Cst Manager"), Mage::helper("adminhtml")->__("Cst Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Cst Description"), Mage::helper("adminhtml")->__("Cst Description"));


		$this->_addContent($this->getLayout()->createBlock("cform/adminhtml_cst_edit"))->_addLeft($this->getLayout()->createBlock("cform/adminhtml_cst_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						
					$post_data['category']=implode(',',$post_data['category']);

						$model = Mage::getModel("cform/cst")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Cst was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setCstData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setCstData($this->getRequest()->getPost());
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
						$model = Mage::getModel("cform/cst");
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

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('neocform_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("cform/cst");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'cst.csv';
			$grid       = $this->getLayout()->createBlock('cform/adminhtml_cst_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'cst.xml';
			$grid       = $this->getLayout()->createBlock('cform/adminhtml_cst_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
