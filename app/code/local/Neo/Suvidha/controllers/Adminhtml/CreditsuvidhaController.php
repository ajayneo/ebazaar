<?php

class Neo_Suvidha_Adminhtml_CreditsuvidhaController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('suvidha/creditsuvidha');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("suvidha/creditsuvidha")->_addBreadcrumb(Mage::helper("adminhtml")->__("Creditsuvidha  Manager"),Mage::helper("adminhtml")->__("Creditsuvidha Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Suvidha"));
			    $this->_title($this->__("Manager Creditsuvidha"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Suvidha"));
				$this->_title($this->__("Creditsuvidha"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("suvidha/creditsuvidha")->load($id);
				if ($model->getId()) {
					Mage::register("creditsuvidha_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("suvidha/creditsuvidha");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Creditsuvidha Manager"), Mage::helper("adminhtml")->__("Creditsuvidha Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Creditsuvidha Description"), Mage::helper("adminhtml")->__("Creditsuvidha Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("suvidha/adminhtml_creditsuvidha_edit"))->_addLeft($this->getLayout()->createBlock("suvidha/adminhtml_creditsuvidha_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("suvidha")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Suvidha"));
		$this->_title($this->__("Creditsuvidha"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("suvidha/creditsuvidha")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("creditsuvidha_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("suvidha/creditsuvidha");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Creditsuvidha Manager"), Mage::helper("adminhtml")->__("Creditsuvidha Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Creditsuvidha Description"), Mage::helper("adminhtml")->__("Creditsuvidha Description"));


		$this->_addContent($this->getLayout()->createBlock("suvidha/adminhtml_creditsuvidha_edit"))->_addLeft($this->getLayout()->createBlock("suvidha/adminhtml_creditsuvidha_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						
				 //save image
		try{

if((bool)$post_data['sign_file1']['delete']==1) {

	        $post_data['sign_file1']='';

}
else {

	unset($post_data['sign_file1']);

	if (isset($_FILES)){

		if ($_FILES['sign_file1']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("suvidha/creditsuvidha")->load($this->getRequest()->getParam("id"));
				if($model->getData('sign_file1')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS .implode(DS,explode('/',$model->getData('sign_file1'))));	
				}
			}
						$path = Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS . 'suvidha' . DS .'creditsuvidha'.DS;
						$uploader = new Varien_File_Uploader('sign_file1');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['sign_file1']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['sign_file1']='suvidha/creditsuvidha/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image

				 //save image
		try{

if((bool)$post_data['sign_file2']['delete']==1) {

	        $post_data['sign_file2']='';

}
else {

	unset($post_data['sign_file2']);

	if (isset($_FILES)){

		if ($_FILES['sign_file2']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("suvidha/creditsuvidha")->load($this->getRequest()->getParam("id"));
				if($model->getData('sign_file2')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS .implode(DS,explode('/',$model->getData('sign_file2'))));	
				}
			}
						$path = Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS . 'suvidha' . DS .'creditsuvidha'.DS;
						$uploader = new Varien_File_Uploader('sign_file2');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['sign_file2']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['sign_file2']='suvidha/creditsuvidha/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image

				 //save image
		try{

if((bool)$post_data['sign_file3']['delete']==1) {

	        $post_data['sign_file3']='';

}
else {

	unset($post_data['sign_file3']);

	if (isset($_FILES)){

		if ($_FILES['sign_file3']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("suvidha/creditsuvidha")->load($this->getRequest()->getParam("id"));
				if($model->getData('sign_file3')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS .implode(DS,explode('/',$model->getData('sign_file3'))));	
				}
			}
						$path = Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS . 'suvidha' . DS .'creditsuvidha'.DS;
						$uploader = new Varien_File_Uploader('sign_file3');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['sign_file3']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['sign_file3']='suvidha/creditsuvidha/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image

				 //save image
		try{

if((bool)$post_data['aadhar']['delete']==1) {

	        $post_data['aadhar']='';

}
else {

	unset($post_data['aadhar']);

	if (isset($_FILES)){

		if ($_FILES['aadhar']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("suvidha/creditsuvidha")->load($this->getRequest()->getParam("id"));
				if($model->getData('aadhar')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS .implode(DS,explode('/',$model->getData('aadhar'))));	
				}
			}
						$path = Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS . 'suvidha' . DS .'creditsuvidha'.DS;
						$uploader = new Varien_File_Uploader('aadhar');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['aadhar']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['aadhar']='suvidha/creditsuvidha/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image

				 //save image
		try{

if((bool)$post_data['pancard']['delete']==1) {

	        $post_data['pancard']='';

}
else {

	unset($post_data['pancard']);

	if (isset($_FILES)){

		if ($_FILES['pancard']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("suvidha/creditsuvidha")->load($this->getRequest()->getParam("id"));
				if($model->getData('pancard')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS .implode(DS,explode('/',$model->getData('pancard'))));	
				}
			}
						$path = Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS . 'suvidha' . DS .'creditsuvidha'.DS;
						$uploader = new Varien_File_Uploader('pancard');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['pancard']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['pancard']='suvidha/creditsuvidha/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image

				 //save image
		try{

if((bool)$post_data['postcheque']['delete']==1) {

	        $post_data['postcheque']='';

}
else {

	unset($post_data['postcheque']);

	if (isset($_FILES)){

		if ($_FILES['postcheque']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("suvidha/creditsuvidha")->load($this->getRequest()->getParam("id"));
				if($model->getData('postcheque')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS .implode(DS,explode('/',$model->getData('postcheque'))));	
				}
			}
						$path = Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS . 'suvidha' . DS .'creditsuvidha'.DS;
						$uploader = new Varien_File_Uploader('postcheque');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['postcheque']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['postcheque']='suvidha/creditsuvidha/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image

				 //save image
		try{

if((bool)$post_data['bankst']['delete']==1) {

	        $post_data['bankst']='';

}
else {

	unset($post_data['bankst']);

	if (isset($_FILES)){

		if ($_FILES['bankst']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("suvidha/creditsuvidha")->load($this->getRequest()->getParam("id"));
				if($model->getData('bankst')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS .implode(DS,explode('/',$model->getData('bankst'))));	
				}
			}
						$path = Mage::getBaseDir() . DS . 'kaw17842knlpREGdasp9045' . DS . 'suvidha' . DS .'creditsuvidha'.DS;
						$uploader = new Varien_File_Uploader('bankst');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['bankst']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['bankst']='suvidha/creditsuvidha/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image


						$model = Mage::getModel("suvidha/creditsuvidha")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Creditsuvidha was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setCreditsuvidhaData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setCreditsuvidhaData($this->getRequest()->getPost());
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
						$model = Mage::getModel("suvidha/creditsuvidha");
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
                      $model = Mage::getModel("suvidha/creditsuvidha");
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
			$fileName   = 'creditsuvidha.csv';
			$grid       = $this->getLayout()->createBlock('suvidha/adminhtml_creditsuvidha_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'creditsuvidha.xml';
			$grid       = $this->getLayout()->createBlock('suvidha/adminhtml_creditsuvidha_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}

		public function approvecreditAction()
		{
			$request = $this->getRequest()->getPost();
			$form_key = Mage::getSingleton('core/session')->getFormKey();
			$request_key = $request['form_key'];
			$arm = '';
			$crm = '';
			if(isset($request['form_key']) && $request['form_key'] == $form_key && !empty($request['credit']) && $request['id']){
				$creditsuvidhaResource = Mage::getResourceModel('suvidha/creditsuvidha_collection');
				$creditsuvidhaResource->addFieldToFilter('id',$request['id']);
				foreach ($creditsuvidhaResource as $model) {
					$email_id = $model->getEmailId();
					$arm = $model->getArmEmail();
					$crm = $model->getCrmEmail();
					$new_credit = (int) $request['credit'];
					$model->setData('assigned_credit_limit',$new_credit);
					$model->setstatus(1);
					$model->save();
				}

				$customer = Mage::getModel('customer/customer_api');
    			$data = $customer->items(array('email' => $email_id));
				foreach ($data as $key => $customer) {
    				$repcode = $customer['repcode'];
    				$name = $customer['firstname'];
    				$customer_mobile = $customer['mobile'];
    			}

    			$mailer['arm'] = $arm;
    			$mailer['crm'] = $crm;

    			$mailer['customer_name'] = $name;
    			$mailer['repcode'] = $repcode;
    			$mailer['credit'] = $new_credit;
    			$mailer['email'] = $email_id;

    			$error_sms = Mage::helper('suvidha')->approvalSMS($customer_mobile,$new_credit);
    			
    			$successCode = '';
    			//send approval email to customer
    			if(Mage::helper('suvidha')->sendApproval($mailer)){
					$successCode .= 'Email sent successfully.';
    			}else{
					$successCode .= 'Email failed to send.';
    			}
    			//send SMS to customer
    			if($error_sms == 1){
					$successCode .= ' SMS failed to send.';
    			}else{
					$successCode .= ' SMS sent successfully.';
    			}
			}
        	Mage::app()->getResponse()->setBody($successCode);
		}

		public function rejectcreditAction()
		{
			$request = $this->getRequest()->getPost();
			$form_key = Mage::getSingleton('core/session')->getFormKey();
			$request_key = $request['form_key'];
			$arm = '';
			$crm = '';
			if(isset($request['form_key']) && $request['form_key'] == $form_key && $request['id']){
				$creditsuvidhaResource = Mage::getResourceModel('suvidha/creditsuvidha_collection');
				$creditsuvidhaResource->addFieldToFilter('id',$request['id']);
				foreach ($creditsuvidhaResource as $model) {
					$email_id = $model->getEmailId();
					$arm = $model->getArmEmail();
					$crm = $model->getCrmEmail();
					$new_credit = (int) $request['credit'];
					$model->setstatus(2);
					$model->save();
				}

				$customer = Mage::getModel('customer/customer_api');
    			$data = $customer->items(array('email' => $email_id));
				foreach ($data as $key => $customer) {
    				$repcode = $customer['repcode'];
    				$name = $customer['firstname'];
    				$customer_mobile = $customer['mobile'];
    			}


    			$mailer['customer_name'] = $name;
    			$mailer['repcode'] = $repcode;
    			$mailer['credit'] = $new_credit;
    			$mailer['email'] = $email_id;
    			$mailer['arm'] = $arm;
    			$mailer['crm'] = $crm;

    			$error_sms = Mage::helper('suvidha')->removalSMS($customer_mobile);
    			
    			$successCode = '';
    			//send approval email to customer
    			if(Mage::helper('suvidha')->sendRemoval($mailer)){
					$successCode .= 'Email sent successfully.';
    			}else{
					$successCode .= 'Email failed to send.';
    			}
    			//send SMS to customer
    			if($error_sms == 1){
					$successCode .= ' SMS failed to send.';
    			}else{
					$successCode .= ' SMS sent successfully.';
    			}
			}
        	Mage::app()->getResponse()->setBody($successCode);
		}
}
