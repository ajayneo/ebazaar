<?php

class Neo_Supersale_Adminhtml_SuperController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		    return Mage::getSingleton('admin/session')->isAllowed('supersale/super');
			//retun true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("supersale/super")->_addBreadcrumb(Mage::helper("adminhtml")->__("Super  Manager"),Mage::helper("adminhtml")->__("Super Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Supersale"));
			    $this->_title($this->__("Manager Super"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Supersale"));
				$this->_title($this->__("Super"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("supersale/super")->load($id);
				if ($model->getId()) {
					Mage::register("super_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("supersale/super");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Super Manager"), Mage::helper("adminhtml")->__("Super Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Super Description"), Mage::helper("adminhtml")->__("Super Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("supersale/adminhtml_super_edit"))->_addLeft($this->getLayout()->createBlock("supersale/adminhtml_super_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("supersale")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Supersale"));
		$this->_title($this->__("Super"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("supersale/super")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("super_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("supersale/super");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Super Manager"), Mage::helper("adminhtml")->__("Super Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Super Description"), Mage::helper("adminhtml")->__("Super Description"));


		$this->_addContent($this->getLayout()->createBlock("supersale/adminhtml_super_edit"))->_addLeft($this->getLayout()->createBlock("supersale/adminhtml_super_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{
			$post_data=$this->getRequest()->getPost();

				if ($post_data) {

					try {						

						$model = Mage::getModel("supersale/super")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						//Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Super was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setSuperData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setSuperData($this->getRequest()->getPost());
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
						$model = Mage::getModel("supersale/super");
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
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("supersale/super");
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
			$fileName   = 'super.csv';
			$grid       = $this->getLayout()->createBlock('supersale/adminhtml_super_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'super.xml';
			$grid       = $this->getLayout()->createBlock('supersale/adminhtml_super_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
