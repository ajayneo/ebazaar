<?php

class Neo_Asmdetail_Adminhtml_AsmController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('asmdetail/asm');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("asmdetail/asm")->_addBreadcrumb(Mage::helper("adminhtml")->__("Asm  Manager"),Mage::helper("adminhtml")->__("Asm Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Asmdetail"));
			    $this->_title($this->__("Manager Asm"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Asmdetail"));
				$this->_title($this->__("Asm"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("asmdetail/asm")->load($id);
				if ($model->getId()) {
					Mage::register("asm_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("asmdetail/asm");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Asm Manager"), Mage::helper("adminhtml")->__("Asm Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Asm Description"), Mage::helper("adminhtml")->__("Asm Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("asmdetail/adminhtml_asm_edit"))->_addLeft($this->getLayout()->createBlock("asmdetail/adminhtml_asm_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("asmdetail")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Asmdetail"));
		$this->_title($this->__("Asm"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("asmdetail/asm")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("asm_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("asmdetail/asm");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Asm Manager"), Mage::helper("adminhtml")->__("Asm Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Asm Description"), Mage::helper("adminhtml")->__("Asm Description"));


		$this->_addContent($this->getLayout()->createBlock("asmdetail/adminhtml_asm_edit"))->_addLeft($this->getLayout()->createBlock("asmdetail/adminhtml_asm_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("asmdetail/asm")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Asm was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setAsmData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setAsmData($this->getRequest()->getPost());
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
						$model = Mage::getModel("asmdetail/asm");
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
                      $model = Mage::getModel("asmdetail/asm");
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
			$fileName   = 'asm.csv';
			$grid       = $this->getLayout()->createBlock('asmdetail/adminhtml_asm_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'asm.xml';
			$grid       = $this->getLayout()->createBlock('asmdetail/adminhtml_asm_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
