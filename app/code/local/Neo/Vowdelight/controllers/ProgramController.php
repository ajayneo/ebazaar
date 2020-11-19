<?php //proram controller for vow landing page
class Neo_Vowdelight_ProgramController extends Mage_Core_Controller_Front_Action{
	public function indexAction(){
		$this->loadLayout();   
        $this->getLayout()->getBlock("head")->setTitle($this->__("Vow Delight Program"));
        $this->renderLayout();
	}
}?>