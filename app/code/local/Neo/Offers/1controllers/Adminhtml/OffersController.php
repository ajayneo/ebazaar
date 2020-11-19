<?php

class Neo_Offers_Adminhtml_OffersController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("offers/offers")->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers  Manager"),Mage::helper("adminhtml")->__("Offers Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Offers"));
			    $this->_title($this->__("Manager Offers"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Offers"));
				$this->_title($this->__("Offers"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("offers/offers")->load($id);
				if ($model->getId()) {
					Mage::register("offers_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("offers/offers");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers Manager"), Mage::helper("adminhtml")->__("Offers Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers Description"), Mage::helper("adminhtml")->__("Offers Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("offers/adminhtml_offers_edit"))->_addLeft($this->getLayout()->createBlock("offers/adminhtml_offers_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("offers")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Offers"));
		$this->_title($this->__("Offers"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("offers/offers")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("offers_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("offers/offers");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers Manager"), Mage::helper("adminhtml")->__("Offers Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers Description"), Mage::helper("adminhtml")->__("Offers Description"));


		$this->_addContent($this->getLayout()->createBlock("offers/adminhtml_offers_edit"))->_addLeft($this->getLayout()->createBlock("offers/adminhtml_offers_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("offers/offers")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Offers was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setOffersData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setOffersData($this->getRequest()->getPost());
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
						$model = Mage::getModel("offers/offers");
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
				$ids = $this->getRequest()->getPost('cashback_offers_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("offers/offers");
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
			$fileName   = 'offers.csv';
			$grid       = $this->getLayout()->createBlock('offers/adminhtml_offers_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'offers.xml';
			$grid       = $this->getLayout()->createBlock('offers/adminhtml_offers_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
