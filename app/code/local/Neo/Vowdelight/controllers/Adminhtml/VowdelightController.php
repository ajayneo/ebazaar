<?php

class Neo_Vowdelight_Adminhtml_VowdelightController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('vowdelight/vowdelight');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("vowdelight/vowdelight")->_addBreadcrumb(Mage::helper("adminhtml")->__("Vowdelight  Manager"),Mage::helper("adminhtml")->__("Vowdelight Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Vowdelight"));
			    $this->_title($this->__("Manager Vowdelight"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Vowdelight"));
				$this->_title($this->__("Vowdelight"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("vowdelight/vowdelight")->load($id);
				if ($model->getId()) {
					Mage::register("vowdelight_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("vowdelight/vowdelight");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Vowdelight Manager"), Mage::helper("adminhtml")->__("Vowdelight Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Vowdelight Description"), Mage::helper("adminhtml")->__("Vowdelight Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("vowdelight/adminhtml_vowdelight_edit"))->_addLeft($this->getLayout()->createBlock("vowdelight/adminhtml_vowdelight_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("vowdelight")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Vowdelight"));
		$this->_title($this->__("Vowdelight"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("vowdelight/vowdelight")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("vowdelight_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("vowdelight/vowdelight");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Vowdelight Manager"), Mage::helper("adminhtml")->__("Vowdelight Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Vowdelight Description"), Mage::helper("adminhtml")->__("Vowdelight Description"));


		$this->_addContent($this->getLayout()->createBlock("vowdelight/adminhtml_vowdelight_edit"))->_addLeft($this->getLayout()->createBlock("vowdelight/adminhtml_vowdelight_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("vowdelight/vowdelight")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Vowdelight was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setVowdelightData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setVowdelightData($this->getRequest()->getPost());
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
						$model = Mage::getModel("vowdelight/vowdelight");
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
                      $model = Mage::getModel("vowdelight/vowdelight");
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
			$fileName   = 'vowdelight.csv';
			$grid       = $this->getLayout()->createBlock('vowdelight/adminhtml_vowdelight_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'vowdelight.xml';
			$grid       = $this->getLayout()->createBlock('vowdelight/adminhtml_vowdelight_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}

		/* ##### Start custom code for Reverse pickup option for manually.(Code by JP.) ##### */

        public function reversePickupAction()
        {   
		    $request         = $this->getRequest()->getParams();
		    $request_id      = '';
		    $oldorder_number = '';		    
		    // $vow_order_data = Mage::getModel('vowdelight/vowdelight')->load($request['id']);
		    $resouce = Mage::getResourceModel('vowdelight/vowdelight_collection');
		    $resouce->addFieldToFilter('request_id',$request['id']);

		    foreach ($resouce as $data) {
		    	# code...
		    	$request_id = $data->getRequestId();
		    	$oldorder_number = $data->getOldOrderNo();
		    	break;
		    }

             //code for reverse pickup.
             $reverse_response    = array();
             $pincode_serviceable = '';
            	
         	 // $request_id                  = $vow_order_data['request_id'];
         	 // $oldorder_number = $vow_order_data['old_order_no'];
             if($request_id != '' && $oldorder_number != '')
             {
                Mage::log('Backend Reverse Pickup Old Order ID : '.$oldorder_number, null, 'vowdelight-module.log', true);
                $pincode_serviceable = Mage::helper('orderreturn')->pincodeEcomQcRv($oldorder_number);
                Mage::log('Backend Pincode Serviceable : '.$pincode_serviceable, null, 'vowdelight-module.log', true);
                if($pincode_serviceable == 1)
                {                
                  Mage::log('Backend Reverse Pickup Request ID : '.$request_id, null, 'vowdelight-module.log', true);
				    try
				    {
	                  $reverse_response = Mage::getModel('vowdelight/vowdelight')->ecom_reverse_pickup($request_id);  
	                  Mage::log('Backend Reverse Pickup Response : ', null, 'vowdelight-module.log', true);
	                  Mage::log($reverse_response, null, 'vowdelight-module.log', true);
			          Mage::getSingleton('core/session')->addSuccess('Reverse pickup done successfully.');
				    }
				    catch(Exception $error)
				    {
				        Mage::getSingleton('core/session')->addError($error->getMessage());
				        return false;
				    }	                  
                }
                else
                {
					Mage::getSingleton('core/session')->addError('Reverse pickup not possible, Pincode not serviceable for this order.');
					return false;	                	
                }
             }
             else
             {
				Mage::getSingleton('core/session')->addError('Order details not found.');
				return false;
             }		    

		    $this->_redirect('*/*/');
        }		
}
