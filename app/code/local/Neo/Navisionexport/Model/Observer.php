<?php
class Neo_Navisionexport_Model_Observer
{	
	public function addHandler($observer)
    {
    	//$_SESSION['microsoft_validation'] = 'microsoft_validation';

       	$catName = Mage::getModel('catalog/layer')->getCurrentCategory()->getName();
    	if($catName == 'MICROSOFT SURFACE PRO 4' && $_SESSION['microsoft_validation'] != 'microsoft_validation')
    	{
    		//echo "asd";exit;
    		Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl()."microsoft-validation")->sendResponse();
            exit; 
    	}
    }
}  