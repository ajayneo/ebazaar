<?php
class SSTech_BulkUploadProduct_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_CANCEL_ORDER     = 'bulkuploadproduct/general/enabled';
    public function preDispatch()
    {
        $enabled = Mage::getStoreConfigFlag(self::XML_PATH_CANCEL_ORDER, Mage::app()->getStore()->getStoreId());
        if(!$enabled){
             $this->_forward('defaultNoRoute');
        }
    }

    public function IndexAction() 
    {  
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Bulk Upload Product"));
      $this->renderLayout(); 
    }
}