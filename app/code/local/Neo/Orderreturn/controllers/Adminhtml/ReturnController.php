<?php

class Neo_Orderreturn_Adminhtml_ReturnController extends Mage_Adminhtml_Controller_Action
{

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("orderreturn/return")->_addBreadcrumb(Mage::helper("adminhtml")->__("Return  Manager"),Mage::helper("adminhtml")->__("Return Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Orderreturn"));
			    $this->_title($this->__("Manager Return"));
				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Orderreturn"));
				$this->_title($this->__("Return"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("orderreturn/return")->load($id);
				if ($model->getId()) {
					Mage::register("return_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("orderreturn/return");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Return Manager"), Mage::helper("adminhtml")->__("Return Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Return Description"), Mage::helper("adminhtml")->__("Return Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("orderreturn/adminhtml_return_edit"))->_addLeft($this->getLayout()->createBlock("orderreturn/adminhtml_return_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("orderreturn")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Orderreturn"));
		$this->_title($this->__("Return"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("orderreturn/return")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("return_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("orderreturn/return");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Return Manager"), Mage::helper("adminhtml")->__("Return Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Return Description"), Mage::helper("adminhtml")->__("Return Description"));


		$this->_addContent($this->getLayout()->createBlock("orderreturn/adminhtml_return_edit"))->_addLeft($this->getLayout()->createBlock("orderreturn/adminhtml_return_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{
			$post_data = $this->getRequest()->getPost();

			// echo "<pre>"; print_r($post_data); echo "</pre>";
			$request_id = $this->getRequest()->getParam("id");
				if ($post_data) {
					$pickup_address = '';
					if($post_data['default_billing_address']){
						$address = Mage::helper("orderreturn")->getCustomerAddress($request_id);
						$pickup_address = $address;
					}else if($post_data['pickup_address']){
						$pickup_address = $post_data['pickup_address'];
					}
					

					$json_utr = array();
					if(!empty($_FILES['refund_filepath']['name'])){
						foreach($_FILES['refund_filepath']['name'] as $imei => $value){
							// echo "file name = ".$_FILES['refund_filepath']['name'][$imei];
							if($_FILES['refund_filepath']['name'][$imei]){
								$uploader = new Varien_File_Uploader(
				                array(
				                    'name' => $_FILES['refund_filepath']['name'][$imei], 
				                    'type' => $_FILES['refund_filepath']['type'][$imei],
				                    'tmp_name' => $_FILES['refund_filepath']['tmp_name'][$imei],
				                    'error' => $_FILES['refund_filepath']['error'][$imei],
				                    'size' => $_FILES['refund_filepath']['size'][$imei]
				                  	)
				              	); 
				              	$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
				              	$uploader->setAllowRenameFiles(true); 
				              	$uploader->setFilesDispersion(false);
				              	$path = Mage::getBaseDir('media') . DS . 'order_return_utr' . DS . $imei . DS;
				              	$img = $uploader->save($path, $_FILES['refund_filepath']['name'][$imei]);
								$json_utr[$imei] = 'media/order_return_utr/'.$imei.'/'.$img['file'];
							}	
						}
					}
					//initial status
					$json_status = array();
					if($post_data['status']){
						foreach ($post_data['status'] as $imei => $value) {
							$json_status[$imei] = $value;
						}
					}

					$json_remarks = array();
					if($post_data['remarks']){
						foreach ($post_data['remarks'] as $imei => $value) {
							$json_remarks[$imei] = trim($value);
						}
					}

					$json_docket_no = array();
					if($post_data['docket_no']){
						foreach ($post_data['docket_no'] as $imei => $value) {
							if($value){ $json_docket_no[$imei] = (int)$value; }
						}
					}

					$sales_entry_pass = array();
					if($post_data['sales_entry_pass']){
						foreach ($post_data['sales_entry_pass'] as $imei => $value) {
							if($value){ $sales_entry_pass[$imei] = $value; }
						}
					}

					$sales_entry_no = array();
					if($post_data['sales_entry_no']){
						foreach ($post_data['sales_entry_no'] as $imei => $value) {
							if($value){ $sales_entry_no[$imei] = (int) $value; }
						}
					}

					$payment_done = array();
					if($post_data['payment_done']){
						foreach ($post_data['payment_done'] as $imei => $value) {
							if($value){ $payment_done[$imei] = $value; }
						}
					}

					$pay_reference_no = array();
					if($post_data['pay_reference_no']){
						foreach ($post_data['pay_reference_no'] as $imei => $value) {
							if($value){ $pay_reference_no[$imei] = (int)$value; }
						}
					}

					$json_increment_id = array();
					if(!empty($post_data['return_increment_id'])){
						foreach ($post_data['return_increment_id'] as $imei => $value) {
							if($value){
								$json_increment_id[$imei] = $value; 
							}
						}
					}

					
					//set status to pickup to docket number imei
					if(!empty($json_docket_no)){
						$shipped_imei = array_keys($json_docket_no);
						
						foreach ($post_data['status'] as $imei => $value) {
							if(in_array($imei, $shipped_imei)){
								$json_status[$imei] = 4; 
							}
						}
					}

					//set status to payment whose sales entry number sent
					if(!empty($sales_entry_no)){
						$sales_entry_imei = array_keys($sales_entry_no);
						foreach ($post_data['status'] as $imei => $value) {
							if(in_array($imei, $sales_entry_imei)){
								$json_status[$imei] = 5; 
							}
						}
					}

					//set status for imei whose payment number exists
					if(!empty($pay_reference_no)){
						$imei_with_payments = array_keys($pay_reference_no);
						foreach ($post_data['status'] as $imei => $value) {
							if(in_array($imei, $imei_with_payments)){
								$json_status[$imei] = 6; 
							}
						}
						
					}
					// print_r($json_status); die('change status');
					try {

						$model = Mage::getModel("orderreturn/return")->load($request_id);

						$order_id = $model->getOrderNumber();

						$order_line = '&nbsp;&nbsp;Your refund/replacement request for order number #'.$order_id;
						//logic to send email when there is change in status of any imeibrZZ
						$old_status = $model->getStatus();

						$decode_old = json_decode($old_status,1);
						$flag_email = false;
						$status_to_send_email = array();
						$message = '';
						$informCustomer = false;
						foreach ($decode_old as $imei => $oldstatus) {
							//echo "oldstatus ".$oldstatus." new status";
							if($oldstatus !== $json_status[$imei]){
								//"There is change in status";
								$flag_email = true;
								if($json_status[$imei] == 4){
									$informCustomer = true;
									$number_items = count($json_docket_no);

									if($number_items == 1){
										$units = 'units';
									}else{
										$units = 'units';
									}
									$email_content_1 = '';
									$email_content_1 = $order_line;
									$email_content_1 .= '&nbsp;for '.$number_items.' '.$units.' has been approved and pickup for same has been arranged. <br/><br/><br/>';
									$email_content_1 .= '&nbsp;&nbsp;Kindly ensure';
									$email_content_1 .= '<ul><li>IMEI number on mobile & Invoice are matching. </li>';
									$email_content_1 .= '<li>Ensure all accessories are sent together with mobile.</li></ul><br/><br/>';
									$email_content_1 .= '  In case any of above things are mismatched, will lead to rejection for refund/replacement request.';

									$message = $email_content_1;
								}else if($json_status[$imei] == 6){
									$informCustomer = true;
									$complete_items_count = count($pay_reference_no);
									if($complete_items_count > 1){
										$units2 = 'units';
									}else{
										$units2 = 'unit';
									}

									// $email_content_2 = $order_line;
									$email_content_2 = '';
									$email_content_2 .= $order_line;
									$email_content_2 .= '&nbsp;for '.$complete_items_count.' '.$units2.'&nbsp; complete and payment processed.<br/><br/>';
									$email_content_2 .= 'Find payment details for IMEI as below<br/><br/>';
									$email_content_2 .= '<table cellpadding="0" cellspacing="0" style="border:1px solid #212121;"><tr><td style="font-weight:bold;border-right: 1px solid #212121;border-bottom: 1px solid #212121;padding:5px 20px;">IMEI</td><td style="font-weight:bold;padding:5px 20px;border-bottom: 1px solid #212121;">UTR</td></tr>';
									$cnt = 1;
									foreach ($pay_reference_no as $imei_no => $utr_number) {
										if($cnt%2 == 0){
											$email_content_2 .= '<tr><td style="border-right: 1px solid #212121;padding:5px 20px;">'.$imei_no.'</td><td style="padding:5px 20px;">'.$utr_number.'</td></tr>';
										}else{
											$email_content_2 .= '<tr><td style="border-right: 1px solid #212121;padding: 5px 20px;background: #e5e5e5;">'.$imei_no.'</td><td style="padding:5px 20px; background:#e5e5e5;">'.$utr_number.'</td></tr>';
										}

										$cnt++;
									}
									$email_content_2 .= '</table>';
									$message = $email_content_2;
								}

								break;
							}//end if
						}
					//start setting values in table model	

					//pickup address					
						if(!empty($pickup_address)){
							$model->setPickupAddress($pickup_address);
						}
						
						//UTR number	
						// if(!empty($json_utr)){
						// 	$json_utr = json_encode($json_utr);
						// 	$model->setUtrFilepath($json_utr);
						// }

						if(!empty($json_increment_id)){
							$json_increment_id = json_encode($json_increment_id);
							$model->setReplaceIncrementId($json_increment_id);
						}

						if(!empty($json_status)){
							$json_status = json_encode($json_status);
							$model->setStatus($json_status);
						}

						if(!empty($json_remarks)){
							$json_remarks = json_encode($json_remarks);
							$model->setRemarks($json_remarks);
						}
						
						if(!empty($json_docket_no)){
							$json_docket_no = json_encode($json_docket_no);
							$model->setDocketNo($json_docket_no);
						}
						
						if(!empty($sales_entry_pass)){
							$sales_entry_pass = json_encode($sales_entry_pass);
							$model->setSalesEntryPass($sales_entry_pass);
						}

						if(!empty($sales_entry_no)){
							$sales_entry_no = json_encode($sales_entry_no);
							$model->setSalesEntryNo($sales_entry_no);
						}

						if(!empty($payment_done)){
							$payment_done = json_encode($payment_done);
							$model->setPaymentDone($payment_done);
						}

						if(!empty($pay_reference_no)){
							$pay_reference_no = json_encode($pay_reference_no);
							$model->setPayReferenceNo($pay_reference_no);
						}

						$updatedt_at = Mage::getModel('core/date')->date('Y-m-d H:i:s');
						

						$model->setUpdatedAt($updatedt_at);

						$model->save();

						//send mail with updated data
						if($flag_email){
							
							$updated_refund_request = $model->getData();
							$send_mail2 = Mage::helper('orderreturn')->sendAdminEmail($updated_refund_request);
							// $send_mail = Mage::helper('orderreturn')->sendEmail($updated_refund_request, $informCustomer, $message);
							if($informCustomer){
								$send_mail1 = Mage::helper('orderreturn')->sendCustomerEmail($updated_refund_request, $informCustomer, $message);
							}	
						}

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Return was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setReturnData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setReturnData($this->getRequest()->getPost());
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
						$model = Mage::getModel("orderreturn/return");
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
                      $model = Mage::getModel("orderreturn/return");
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
		public function exportCsvActionOld()
		{
			$fileName   = 'return.csv';
			$grid       = $this->getLayout()->createBlock('orderreturn/adminhtml_return_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		}

		public function exportCsvAction(){
			$params = $this->getRequest()->getParams();
			$filter = $params['filter'];
		    $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter); 
		    // echo "<pre>"; print_r($filter_data); exit;
    		$customGrid = Mage::helper('orderreturn')->getCustomGrid($filter_data);
    		$prepareCsvFile = Mage::helper('orderreturn')->getCustomCsv($customGrid);
		}

		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'return.xml';
			$grid       = $this->getLayout()->createBlock('orderreturn/adminhtml_return_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}

		protected function _isAllowed(){
	        return true;
	    }

	    public function pickupAction(){
	    	$post = $this->getRequest()->getPost();
    		$request_id = (int) $post['ids']; 
    		// exit;
    		// $result = $request_id; 
    		$response_msg = array();
    		$result = '';
    		if($request_id > 0){
	    		$orderReturn = Mage::getModel('orderreturn/return')->load($request_id);
	    		// print_r($orderReturn->getData()); exit;
	    		// $result = $orderreturnModel->getCanceledImei();
	    		if(!$orderReturn->getDocketNo()){
	    		// if(1==2){
		    		$response_msg = Mage::getModel('shippinge/ecom')->sendQCRVP($request_id);
		    		$json_docket_no = array();
		    		if($response_msg){
			    		foreach ($response_msg as $key => $value) {
							$json_docket_no[$value['serial']] = (int)$value['msg'];
			    		}
		    		}

		    		if($json_docket_no){

		    			$orderReturn->setData('docket_no',json_encode($json_docket_no));
		    			try{
		    				$orderReturn->save();
		    			}catch(Exception $e){
		    				//
		    				$response_msg['error'] = 1;
		    				$response_msg['msg'] = $e->getMessage();
		    			}		
		    		}

	    		}//end of set docket no


    		}


    		$this->getResponse()->setHeader('Content-type', 'application/json', true);
	        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response_msg));
    	}
}
