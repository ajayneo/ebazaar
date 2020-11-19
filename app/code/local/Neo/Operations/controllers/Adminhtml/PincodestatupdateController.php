<?php
class Neo_Operations_Adminhtml_PincodestatupdateController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('operations/operationsbackend');
		return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Manage Shipment Pincodes TAT"));
	   $this->renderLayout();
    }

   
    
    public function updatePincodesTATAction()
    {
    	$file = $_FILES;
    	ini_set('memory_limit', '-1');
		$postData = $this->getRequest()->getPost();

		 
		//if($postData['tracking_partner'] == ''){
		//	Mage::getSingleton("adminhtml/session")->addError("Select Tracking Partner.");
		//	$this->_redirect("*/*/");
		//}
		if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
		    try {  
		    	$uploader = new Varien_File_Uploader('file');
		        $uploader->setAllowedExtensions(array('csv'));
		        $uploader->setAllowRenameFiles(false);
		        $uploader->setFilesDispersion(false);
		        $path = Mage::getBaseDir('media') . DS.'PincodesTAT'. DS;
		        
		        $fileName = $_FILES['file']['name'];
		        $filePath = $path.$fileName;
		        $uploader->save($path, $fileName);

		        $file_handle = fopen($filePath, 'r');
		       	while (!feof($file_handle) ) {
			 		$data[] = fgetcsv($file_handle, 1024);
			  	}
				fclose($file_handle);
				
				/*echo "<pre>";
				print_r($data);
				die;*/
				
				$i = 1;

				/*$serviceablepincodes = Mage::getModel('operations/serviceablepincodes')->getCollection();
				//print_r($serviceablepincodes->getData());

				/*if($postData['tracking_partner'] == 'ecom'){
					foreach ($serviceablepincodes as $pincode) {
						$pincode->setEcom('#N/A')->save();
					}	
				}elseif($postData['tracking_partner'] == 'vexpress'){
					foreach ($serviceablepincodes as $pincode) {
						$pincode->setVexpress('#N/A')->save();
					}	
				}elseif($postData['tracking_partner'] == 'bluedart'){
					foreach ($serviceablepincodes as $pincode) {
						$pincode->setDhlBluedart('#N/A')->save();
					}	
				}*/

				$not_updated = array();				
				foreach ($data as $key => $value) {
					if($i == 1) { $i++; continue;}
					
					/*if($postData['tracking_partner'] == 'ecom'){
						$updatepincodes = Mage::getModel('operations/serviceablepincodes')->load($value[0],'pincode');
						if(!$updatepincodes->getId()){
							//$updatepincodes->setEcom($value[1])->save();
						}
						$i++;
					}elseif($postData['tracking_partner'] == 'vexpress'){
						$updatepincodes = Mage::getModel('operations/serviceablepincodes')->load($value[0],'pincode');
						if(!$updatepincodes->getId()){
							//$updatepincodes->setvexpress($value[1])->save();
						}
						$i++;
					}elseif($postData['tracking_partner'] == 'bluedart'){
						$updatepincodes = Mage::getModel('operations/serviceablepincodes')->load($value[0],'pincode');
						if(!$updatepincodes->getId()){
							$updatepincodes->setDhlBluedart(trim($value[1]))->save();
						}
						$i++;
					}*/

					/*$shtPincode = str_replace('< br />', '', trim($value[0]));
					$shtPincode = str_replace('< br>', '', trim($value[0]));
					$shtPincode = str_replace('<b>', '', trim($value[0]));
					$shtPincode = str_replace(' ', '', trim($value[0]));*/
					//$shtPincode = str_replace('< br>', '', trim($value[0]));

					$shtPincode = strip_tags(trim($value[0]));

					$updatepincodes = Mage::getModel('operations/serviceablepincodes')->load($shtPincode,'pincode');
					if($updatepincodes->getId()){
						$updatepincodes->setStateCode(trim($value[1]));
						$updatepincodes->setCity(trim($value[2]));
						$updatepincodes->setEcom(trim($value[3]));
						$updatepincodes->setDhlBluedart(trim($value[4]));
						$updatepincodes->setVexpress(trim($value[5]));
						$updatepincodes->save();

					}else{
						$addpincode = Mage::getModel('operations/serviceablepincodes');
						$addpincode->setPincode(trim($value[0]));
						$addpincode->setStateCode(trim($value[1]));
						$addpincode->setCity(trim($value[2]));
						$addpincode->setEcom(trim($value[3]));
						$addpincode->setDhlBluedart(trim($value[4]));
						$addpincode->setVexpress(trim($value[5]));
						try{
							$addpincode->save();
						}catch(Exception $e){
							$not_updated[] = $shtPincode;
						}
					}
					$i++;
				}
				
				$emailBody = "Hello,<br />Please check below pincodes as not updated due to city and state not available in uploaded sheet and pincodes are not available in current database.<br /><br />Not Updated Pincode List(".count($not_updated).") : <br />";

				$notUpdatedPincodes = implode(' ', $not_updated);

				$emailBody .= '<span style="font-weight:normal">'.$notUpdatedPincodes."</span>";

				$emailBody .= "<h3>Note : If you are providing list with city and state, development team have to make changes in code.</h3>";

				// Always set content-type when sending HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

				// More headers
				$headers .= 'From: support@electronicsbazaar.com' . "\r\n";
				$headers .= 'Cc: maheshkumar.gurav@neosofttech.com' . "\r\n";


				mail("web.maheshgurav@gmail.com","Not Mapped Pincodes",$emailBody,$headers);
				Mage::getSingleton("adminhtml/session")->addSuccess('Pincodes TAT updated successfully.');
		    	 
		    } catch (Exception $e) {
		        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		    }
		}else{
			Mage::getSingleton("adminhtml/session")->addError("Please selet File to upload.");
		}
    	
    	$this->_redirect("*/*/");
    	
    }

}