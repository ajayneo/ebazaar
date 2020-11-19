<?php
class Neo_Ebautomation_CustomerController extends Mage_Core_Controller_Front_Action{
	

	//customer adding in navision
	public function customerImportAction(){
	  	if(Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
	      Mage::getModel('ebautomation/customer')->customerImport();
	    }
	}

	public function getCreditLimitAction(){
      //if(Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
          $user_id = $this->getRequest()->getParam('user_id');
         Mage::getModel('ebautomation/customer')->getCreditLimit($user_id);
      //}
      
  }

  
  public function specificCustomerMappingAction(){
      //if(Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
         
         Mage::getModel('ebautomation/customer')->specificCustomerMapping();
      //}
      
  }


}
?>