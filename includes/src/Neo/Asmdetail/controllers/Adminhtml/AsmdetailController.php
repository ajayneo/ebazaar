<?php

class Neo_Asmdetail_Adminhtml_AsmdetailController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
	    return true; 

	}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("asmdetail/asmdetail")->_addBreadcrumb(Mage::helper("adminhtml")->__("ASM Detail  Manager"),Mage::helper("adminhtml")->__("ASM Detail Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("ASM Detail"));
			    $this->_title($this->__("Manager ASM Detail"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("ASM Detail"));
				$this->_title($this->__("ASM Detail"));
			    $this->_title($this->__("Edit Item")); 
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("asmdetail/asmdetail")->load($id);
				if ($model->getId()) {
					Mage::register("asmdetail_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("asmdetail/asmdetail");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("ASM Detail Manager"), Mage::helper("adminhtml")->__("ASM Detail Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("ASM Detail Description"), Mage::helper("adminhtml")->__("ASM Detail Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("asmdetail/adminhtml_asmdetail_edit"))->_addLeft($this->getLayout()->createBlock("asmdetail/adminhtml_asmdetail_edit_tabs"));
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
		$this->_title($this->__("Asmdetail"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("asmdetail/asmdetail")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("asmdetail_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("asmdetail/asmdetail");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Asmdetail Manager"), Mage::helper("adminhtml")->__("Asmdetail Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Asmdetail Description"), Mage::helper("adminhtml")->__("Asmdetail Description"));


		$this->_addContent($this->getLayout()->createBlock("asmdetail/adminhtml_asmdetail_edit"))->_addLeft($this->getLayout()->createBlock("asmdetail/adminhtml_asmdetail_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("asmdetail/asmdetail")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("ASM Detail was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setAsmdetailData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setAsmdetailData($this->getRequest()->getPost());
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
						$model = Mage::getModel("asmdetail/asmdetail");
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
                      $model = Mage::getModel("asmdetail/asmdetail");
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
			$fileName   = 'asmdetail.csv';
			$grid       = $this->getLayout()->createBlock('asmdetail/adminhtml_asmdetail_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'asmdetail.xml';
			$grid       = $this->getLayout()->createBlock('asmdetail/adminhtml_asmdetail_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
