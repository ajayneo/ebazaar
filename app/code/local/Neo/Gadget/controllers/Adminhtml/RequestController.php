<?php

class Neo_Gadget_Adminhtml_RequestController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("gadget/request")->_addBreadcrumb(Mage::helper("adminhtml")->__("Request  Manager"),Mage::helper("adminhtml")->__("Request Manager"));
				return $this;
		}

		protected function _isAllowed()
		{
			//return Mage::getSingleton('admin/session')->isAllowed('gstdetails/gstdetailsbackend');
			return true;
		}
		
		public function indexAction() 
		{
			    $this->_title($this->__("Gadget"));
			    $this->_title($this->__("Manager Request"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Gadget"));
				$this->_title($this->__("Request"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("gadget/request")->load($id);
				if ($model->getId()) {
					Mage::register("request_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("gadget/request");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Request Manager"), Mage::helper("adminhtml")->__("Request Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Request Description"), Mage::helper("adminhtml")->__("Request Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("gadget/adminhtml_request_edit"))->_addLeft($this->getLayout()->createBlock("gadget/adminhtml_request_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("gadget")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Gadget"));
		$this->_title($this->__("Request"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("gadget/request")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("request_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("gadget/request");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Request Manager"), Mage::helper("adminhtml")->__("Request Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Request Description"), Mage::helper("adminhtml")->__("Request Description"));


		$this->_addContent($this->getLayout()->createBlock("gadget/adminhtml_request_edit"))->_addLeft($this->getLayout()->createBlock("gadget/adminhtml_request_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("gadget/request")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();


						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Request was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setRequestData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setRequestData($this->getRequest()->getPost());
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
						$model = Mage::getModel("gadget/request");
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

		public function array_insert($arr, $insert, $position) {

		    foreach ($arr as $key => $value) {
		            if ($key == $position) {
		                    foreach ($insert as $ikey => $ivalue) {
		                            $ret[$ikey] = $ivalue;
		                    }
		            }
		            $ret[$key] = $value;
		    }
		    return $ret;
		}

		public function array_to_csv_download($newArray, $filename = "export.csv", $delimiter=",") {

		    header('Content-Type: application/csv');
		    header('Content-Disposition: attachment; filename="'.$filename.'";');

		    // open the "output" stream
		    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
		    $f = fopen('php://output', 'w');

		    // print_r($newArray); exit;
		    foreach($newArray as $key => $array){
			    // foreach ($array as $line) {
			        fputcsv($f, $array, $delimiter);
			    // }
			}

		    exit;
		}   

		public function massExportAction() 
		{
			try {
					$ids = $this->getRequest()->getPost('ids', array());
					$i = 1;
					$model = Mage::getModel("gadget/request");
					foreach ($ids as $id) {
	                      $gadgetModel = $model->load($id);
	                      
						  $new = json_decode($gadgetModel->getOptions());
						  $fullArray = $this->array_insert($gadgetModel->getData(),$new,'option');
						  unset($fullArray['option']);
						  unset($fullArray['options']);

						  $dataKey = array_keys($fullArray);
						  $dataValues = array_values($fullArray);

						  $condition = json_decode(json_encode($fullArray[0]), True);

						  $dataValuesNew = array();
						  
						  $dataValuesNew[] = $fullArray['created_at'];
						  $dataValuesNew[] = 'Lenovo Buyback Program';//$fullArray['program'];
						  $dataValuesNew[] = $fullArray['id'];						  
						  $dataValuesNew[] = $fullArray['sku'];
						  $dataValuesNew[] = $fullArray['proname'];    
						  $dataValuesNew[] = 'Rs '.$fullArray['price']; //format price
						  $dataValuesNew[] = $fullArray['bank_customer_name'];
						  
						  $addressCustomer = Mage::getModel('customer/address')->load($fullArray['address_id'])->getData(); 
						  $dataValuesNew[] = $addressCustomer['company'];
						  $dataValuesNew[] = $addressCustomer['street'].' '.$addressCustomer['region'].' '.$addressCustomer['country_id'];
						  //$dataValuesNew[] = '';
						  $dataValuesNew[] = $addressCustomer['postcode'];
						  $dataValuesNew[] = $addressCustomer['city'];
						  $dataValuesNew[] = $fullArray['email'];
						  $dataValuesNew[] = $addressCustomer['fax'];
						  $dataValuesNew[] = $fullArray['bank_customer_name'];
						  $dataValuesNew[] = $fullArray['bank_name'];
						  $dataValuesNew[] = $fullArray['bank_ifsc'];
						  $dataValuesNew[] = $fullArray['bank_account_no'];
						  $dataValuesNew[] = $fullArray['serial_number'];
						  $dataValuesNew[] = $condition['Condition'];
						  $dataValuesNew[] = $condition['Charger'];

						  



						  if($i == 1){
						  	$dataKeyNew[] = 'Order Date';
						  	$dataKeyNew[] = 'Program';
						  	$dataKeyNew[] = 'Order #';
						  	$dataKeyNew[] = 'SKU Code';
						  	$dataKeyNew[] = 'SKU Description';
						  	$dataKeyNew[] = 'Price';
						  	$dataKeyNew[] = 'Customer Name';
						  	$dataKeyNew[] = 'Store Name';
						  	$dataKeyNew[] = 'Customer Address';
						  	//$dataKeyNew[] = 'Landmark';
						  	$dataKeyNew[] = 'PIN Code';
						  	$dataKeyNew[] = 'City';
						  	$dataKeyNew[] = 'Email';
						  	$dataKeyNew[] = 'Mobile Number';
						  	$dataKeyNew[] = 'Customer Name';
						  	$dataKeyNew[] = 'Bank Name';
						  	$dataKeyNew[] = 'IFSC Code';
						  	$dataKeyNew[] = 'A/C No';
						  	$dataKeyNew[] = 'Serial / IMEI Number';
						  	$dataKeyNew[] = 'Material Condition';
						  	$dataKeyNew[] = 'Charger/Battery';
						  	// $newArray = array($dataKeyNew,$dataValuesNew);
						  	$newArray = array($dataKeyNew);
						  	$newArray[] =$dataValuesNew;
						  	$i++;
						  }else{
						  	$newArray[] =$dataValuesNew;
						  }
						  


					}  
					// echo "<pre>"; print_r($newArray); exit;

					//=================
					  	$this->array_to_csv_download(

					  		$newArray
					  		, 
							  "export_gadget_buy_request_".date("Y/m/d").".csv" 
							);
				    //=================


					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully exported"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}

		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("gadget/request");
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
			$fileName   = 'request.csv';
			$grid       = $this->getLayout()->createBlock('gadget/adminhtml_request_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'request.xml';
			$grid       = $this->getLayout()->createBlock('gadget/adminhtml_request_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}

		/* 
		@Author : Sonali Kosrabe
		@date : 22-12-17
		@purpose : To send request for pickup
		*/
		
		public function sendForPickupAction(){
			$productId = $this->getRequest()->getParam('productId');
			$gadget = Mage::getModel('gadget/request')->load($productId);
			$response = Mage::getModel('shippinge/ecom')->sendRvpRequestGadget($productId);
			$message = trim($response['message'],',');
			$awb_response = serialize($response);
			$awb_request_date = date('Y-m-d H:i:s');
			$awb_number = $response['airwaybill_number'];

			if($response['success'] == 0){
				$data['awb_number'] = $gadget->getAwbNumber();
				$data['status'] = $gadget->getStatus();
				$data['message'] = "Error : ".$message;
				$data['success'] = $response['success'];
				echo json_encode($data);
				exit;
			}else{
				
				$gadget->setAwbNumber($awb_number);
				$gadget->setAwbRequestDate($awb_request_date);
				$gadget->setAwbResponse($awb_response);
				$gadget->setStatus('processing');
				$gadget->save();

				$data['awb_number'] = $gadget->getAwbNumber();
				$data['status'] = $gadget->getStatus();
				$data['message'] = $message;
				$data['success'] = $response['success'];
				echo json_encode($data);
				exit;
			
			}
			
		}
}
