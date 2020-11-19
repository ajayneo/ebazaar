<?php

class Neo_Gadget_Block_Adminhtml_Request_Renderer_Reversepickup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		//$media = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$value =  $row->getValue();
		
		return '<script type="text/javascript">alert("reverse pikcup");</script>';   
 
        //return $out;
	}
 
} 
      