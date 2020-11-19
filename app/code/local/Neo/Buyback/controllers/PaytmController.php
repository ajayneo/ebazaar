<?php
class Neo_Buyback_PaytmController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Assured Buyback - Paytm"));
	    $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

     //  $breadcrumbs->addCrumb("buback", array(
     //      "label" => $this->__("Buyback"),
     //      "title" => $this->__("Buyback"),
     //      "link"  => Mage::getBaseUrl().'buyback'
		   // ));

      $breadcrumbs->addCrumb("paytm", array(
                "label" => $this->__("Paytm"),
                "title" => $this->__("Assured Buyback Paytm")
       ));

      $this->renderLayout(); 
	  
    }

    public function postAction(){
        if($request = $this->getRequest()->getPost()){
                $type = 'paytm_invoice';
            if (isset($_FILES[$type]['name']) && $_FILES[$type]['name'] != '') {
                
                if($_FILES[$type]['type'] !=="application/pdf"){
                  Mage::getSingleton('core/session')->addError('Please upload PDF file only.');
                  $this->_redirect('*/*/');
                  return;
                }

                $data = array();
                try {
                    $uploader = new Varien_File_Uploader($type);
                    $uploader->setAllowedExtensions(array('pdf'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $path = Mage::getBaseDir('media') . DS . 'buybackpaytm';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    $uploader->save($path, $_FILES[$type]['name']);
                    $filename = $uploader->getUploadedFileName();
                    // echo "Uploaded File => ".$filename;
                    $data['paytm_invoice'] = $filename;
                    $data['customer_name'] = $request['customer_name'];
                    $data['email'] = $request['email'];
                    $data['mobile'] = $request['mobile'];
                    $data['address'] = strip_tags($request['mobile']);
                    $data['buyback_due'] = (int) $request['buyback_due'];

                    $model = Mage::getModel('buyback/buyback')->load();
                    $model->addData($data);
                    $model->save();

                    Mage::getSingleton('core/session')->addSuccess('Paytm Invoice Saved Successfully for Assured Buyback');
                    $this->_redirect("/");
                    return;

                } catch (Exception $e) {
                    // Mage::log($e->getMessage(), null, $this->_logFile);
                  Mage::getSingleton('core/session')->addError($e->getMessage());
                    $this->_redirect("/");
                    return;
                }
            }else{
                Mage::getSingleton('core/session')->addError('Please upload PDF file only.');
                  $this->_redirect('*/*/');
                  return;
            }
        }

    }
}