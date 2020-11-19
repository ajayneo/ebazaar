<?php
class Neo_Gadget_LenovoController extends Mage_Core_Controller_Front_Action{

	public function IndexAction() {  
		Mage::getSingleton('core/session')->unsSygform();
		$path = null;
		$domain = null;
		$secure = null;
		$httponly = null;
		Mage::getModel('core/cookie')->delete('syghome', $path, $domain, $secure, $httponly); 
		Mage::getModel('core/cookie')->delete('syg-pincode', $path, $domain, $secure, $httponly); 
        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Lenovo"));
        $this->renderLayout(); 
    }

}?>