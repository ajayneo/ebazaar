<?php

class Neo_Productpricereport_Adminhtml_ProductpricereportController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('productpricereport/productpricereport');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("productpricereport/productpricereport")->_addBreadcrumb(Mage::helper("adminhtml")->__("Productpricereport  Manager"),Mage::helper("adminhtml")->__("Productpricereport Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Productpricereport"));
			    $this->_title($this->__("Manager Productpricereport"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Productpricereport"));
				$this->_title($this->__("Productpricereport"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("productpricereport/productpricereport")->load($id);
				if ($model->getId()) {
					Mage::register("productpricereport_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("productpricereport/productpricereport");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Productpricereport Manager"), Mage::helper("adminhtml")->__("Productpricereport Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Productpricereport Description"), Mage::helper("adminhtml")->__("Productpricereport Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("productpricereport/adminhtml_productpricereport_edit"))->_addLeft($this->getLayout()->createBlock("productpricereport/adminhtml_productpricereport_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("productpricereport")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Productpricereport"));
		$this->_title($this->__("Productpricereport"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("productpricereport/productpricereport")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("productpricereport_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("productpricereport/productpricereport");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Productpricereport Manager"), Mage::helper("adminhtml")->__("Productpricereport Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Productpricereport Description"), Mage::helper("adminhtml")->__("Productpricereport Description"));


		$this->_addContent($this->getLayout()->createBlock("productpricereport/adminhtml_productpricereport_edit"))->_addLeft($this->getLayout()->createBlock("productpricereport/adminhtml_productpricereport_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("productpricereport/productpricereport")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Productpricereport was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setProductpricereportData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setProductpricereportData($this->getRequest()->getPost());
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
						$model = Mage::getModel("productpricereport/productpricereport");
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
                      $model = Mage::getModel("productpricereport/productpricereport");
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
			$fileName   = 'productpricereport.csv';
			$grid       = $this->getLayout()->createBlock('productpricereport/adminhtml_productpricereport_grid');
			$this->_prepareDownloadResponsenew($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'productpricereport.xml';
			$grid       = $this->getLayout()->createBlock('productpricereport/adminhtml_productpricereport_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
