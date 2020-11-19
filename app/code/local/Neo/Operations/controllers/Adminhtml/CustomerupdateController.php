<?php
class Neo_Operations_Adminhtml_CustomerupdateController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('operations/operationsbackend');
		return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Update Customer"));
	   $this->renderLayout();
    }

   
    
    public function uploadCustomersAction()
    {
    	$post = $this->getRequest()->getPost();
    	
    	$file = $_FILES;
    	ini_set('memory_limit', '-1');
		ini_set('max_execution_time','60000'); 
		ini_set('innodb_lock_wait_timeout','50000000'); 

    	if (isset($_FILES['customerFiles']['name']) && $_FILES['customerFiles']['name'] != '') {
		    try {  
		    	$uploader = new Varien_File_Uploader('customerFiles');
		        $uploader->setAllowedExtensions(array('csv'));
		        $uploader->setAllowRenameFiles(false);
		        $uploader->setFilesDispersion(false);
		        $path = Mage::getBaseDir('media') . DS.'customer'. DS;
		        // $path = Mage::getBaseDir('media') . DS . 'logo' . DS;
		        $fileName = $_FILES['customerFiles']['name'];
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
					
					$customer = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail(trim($value[0]));
					if($customer->getId()){


						$fieldValue = Mage::getResourceModel('customer/customer')
					        ->getAttribute($post['field'])
					        ->getSource()
					        ->getOptionId(trim($value[1]));
					    if($post['field'] == 'asm_map')
							$customer->setAsmMap($fieldValue);
						else
							$customer->setZone($fieldValue);

						$customer->setUpdateAt(date('Y-m-d H:i:s'));
						try{
							$customer->save();
							Mage::log("Customer ID : ".$customer->getEmail(),null,'customer_update.log',true);
						}catch(Exception $e){
							Mage::log("Customer ID : ".$e->getMessage(),null,'customer_update.log',true);
						}
					}
					$i++;

				}
				Mage::getSingleton("adminhtml/session")->addSuccess('Asm mapped to customer successfully.');
		    	 
		    } catch (Exception $e) {
		        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		    }
		}else{
			Mage::getSingleton("adminhtml/session")->addError("Please selet File to upload.");
		}
    	
    	$this->_redirect("*/*/");
    	
    }

}