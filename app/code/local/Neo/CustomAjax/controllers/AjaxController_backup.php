<?php
class NeoCustom_CustomAjax_AjaxController extends Mage_Core_Controller_Front_Action 
{
	public function indexAction() 
	{
		$this->loadLayout();
		$this->renderLayout();
	}
		
	public function addtobasketAction() 
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function priceupdateAction() 
	{
		$this->loadLayout();
		$this->renderLayout();		
	}
	
	public function setidforcollectionajaxAction() 
	{
		$this->loadLayout();
		$this->renderLayout();		
	}

	public function addwishlisttocartajaxAction() 
	{
		$this->loadLayout();
		$this->renderLayout();		
	}
	
	public function removeitemajaxAction() 
	{
		$this->loadLayout();
		$this->renderLayout();		
	}
	
	public function validatepincodeajaxAction() 
	{
		$this->loadLayout();
		$this->renderLayout();		
	}
	
	public function removeorderajaxAction() 
	{
		$this->loadLayout();
		$this->renderLayout();		
	}			
	
}
