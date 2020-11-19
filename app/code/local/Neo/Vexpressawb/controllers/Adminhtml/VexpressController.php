<?php

class Neo_Vexpressawb_Adminhtml_VexpressController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		    //return Mage::getSingleton('admin/session')->isAllowed('erp/stock_management/firtal_deadstock');  
		    //or at least
		    return true;     

		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("vexpressawb/vexpress")->_addBreadcrumb(Mage::helper("adminhtml")->__("Vexpress  Manager"),Mage::helper("adminhtml")->__("Vexpress Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Vexpressawb"));
			    $this->_title($this->__("Manager Vexpress"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Vexpressawb"));
				$this->_title($this->__("Vexpress"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("vexpressawb/vexpress")->load($id);
				if ($model->getId()) {
					Mage::register("vexpress_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("vexpressawb/vexpress");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Vexpress Manager"), Mage::helper("adminhtml")->__("Vexpress Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Vexpress Description"), Mage::helper("adminhtml")->__("Vexpress Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("vexpressawb/adminhtml_vexpress_edit"))->_addLeft($this->getLayout()->createBlock("vexpressawb/adminhtml_vexpress_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("vexpressawb")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Vexpressawb"));
		$this->_title($this->__("Vexpress"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("vexpressawb/vexpress")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("vexpress_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("vexpressawb/vexpress");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Vexpress Manager"), Mage::helper("adminhtml")->__("Vexpress Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Vexpress Description"), Mage::helper("adminhtml")->__("Vexpress Description"));


		$this->_addContent($this->getLayout()->createBlock("vexpressawb/adminhtml_vexpress_edit"))->_addLeft($this->getLayout()->createBlock("vexpressawb/adminhtml_vexpress_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("vexpressawb/vexpress")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Vexpress was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setVexpressData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setVexpressData($this->getRequest()->getPost());
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
						$model = Mage::getModel("vexpressawb/vexpress");
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
                      $model = Mage::getModel("vexpressawb/vexpress");
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
			$fileName   = 'vexpress.csv';
			$grid       = $this->getLayout()->createBlock('vexpressawb/adminhtml_vexpress_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'vexpress.xml';
			$grid       = $this->getLayout()->createBlock('vexpressawb/adminhtml_vexpress_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}

		public function uploadAction()
		{
			if(isset($_FILES['import_file']['name']) && $_FILES['import_file']['name'] != '')
			{
			    $uploaderFile = new Varien_File_Uploader('import_file');
			    $uploaderFile->setAllowedExtensions(array());
			    $uploaderFile->setAllowRenameFiles(false);
			    $uploaderFile->setFilesDispersion(false);
			    $uploaderFilepath = Mage::getBaseDir('media') . DS . 'importcsv' . DS ;
			    $uploaderFile->save($uploaderFilepath, $_FILES['import_file']['name'] );
			    $file =  $_FILES['import_file']['name'];
			    $filepath = $uploaderFilepath.$file;
			    $i = 0;
			    if(($handle = fopen("$filepath", "r")) !== FALSE) {
			        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){             
			            if($i>0 && count($data)>1){
			            	echo "<pre>";
			                print_r($data);
			                exit;
			            }          
			            $i++;
			        }
			    }
			    else{
			        Mage::getSingleton('adminhtml/session')->addError("There is some Error");
			        $this->_redirect('*/*/index');
			    }
			}

		}
}
