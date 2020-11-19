<?php
class Neo_Operations_Adminhtml_BluedartpincodesController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('operations/operationsbackend');
		return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Manage Bluedart Pincodes"));
	   $this->renderLayout();
    }

   
    
    public function updateBluedartPincodesAction()
    {
    	$file = $_FILES;
    	ini_set('memory_limit', '-1');
		ini_set('max_execution_time','1000'); 
		ini_set('innodb_lock_wait_timeout','500'); 

    	if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
		    try {  
		    	$uploader = new Varien_File_Uploader('file');
		        $uploader->setAllowedExtensions(array('csv'));
		        $uploader->setAllowRenameFiles(false);
		        $uploader->setFilesDispersion(false);
		        $path = Mage::getBaseDir('media') . DS.'bluedart'. DS;
		        
		        $fileName = $_FILES['file']['name'];
		        $filePath = $path.$fileName;
		        $uploader->save($path, $fileName);

		        $file_handle = fopen($filePath, 'r');
		       	while (!feof($file_handle) ) {
			 		$data[] = fgetcsv($file_handle, 1024);
			  	}
				fclose($file_handle);
				
				$i = 1;
				foreach ($data as $key => $value) {
					if($i == 1) { $i++; continue;}
					
					$pincodeData['pincode']=$value[0];
					if(ucwords(trim($value[1])) == 'EDL'){
						$pincodeData['shipment_type'] = 'EDL';
					}else{
						$pincodeData['shipment_type'] = 'APEX';
					}

					$bluedartPincodes = Mage::getModel('operations/bluedartpincodes')->load($value[0],'pincode');
					if(!$bluedartPincodes->getId()){
						$bluedartPincodes->setData($pincodeData)->save();
					}
					$i++;
				}
				Mage::getSingleton("adminhtml/session")->addSuccess('Bludart pincodes updated successfully.');
		    	 
		    } catch (Exception $e) {
		        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		    }
		}else{
			Mage::getSingleton("adminhtml/session")->addError("Please selet File to upload.");
		}
    	
    	$this->_redirect("*/*/");
    	
    }

}