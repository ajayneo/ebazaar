<?php
class Neo_Deliveryvalidator_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("deliveryvalidator"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("deliveryvalidator", array(
                "label" => $this->__("deliveryvalidator"),
                "title" => $this->__("deliveryvalidator")
		   ));

      $this->renderLayout(); 
	  
    }
	public function validatorAction(){
		$pincode = Mage::getModel('deliveryvalidator/deliveryvalidator')->getCollection()->addFieldToFilter('status',array('eq' => 0));
			//echo "<pre>";
			$postpin=$this->getRequest()->getParam('pincode');;
			foreach ($pincode as $data) {
				$pinarr=explode(',', $data['pincodes']);
				if(in_array($postpin, $pinarr)){
					echo "Delivery done in ".$data['rules']." days";
					break;
				}
				else{
					echo "Delivery not available";
				}				
			}
			
		}


}