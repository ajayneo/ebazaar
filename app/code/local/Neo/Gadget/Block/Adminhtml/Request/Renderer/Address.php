<?php

class Neo_Gadget_Block_Adminhtml_Request_Renderer_Address extends Varien_Data_Form_Element_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$addressId =  $row->getValue();

		//$addressId = Mage::registry("request_data")->getData('confrm_by_retailer_address_id');
		$customer = Mage::getModel('customer/customer')->load(Mage::registry("request_data")->getData('confirmed_by_user_id'));
		$retAddresss = Mage::getModel('customer/address')->load($addressId);
		//print_r($retAddresss->getData());
		return "<b>Address : </b><br>".$retAddresss = $retAddresss->getFirstname().' '.$retAddresss->getLastname().'<br>'.implode(',',$retAddresss->getStreet()).'<br>'.$retAddresss->getPostcode().' '.$retAddresss->getCity().' '.$retAddresss->getRegion().', '.$retAddresss->getCountry()."</br></br>";
		
	}
 
} 
      