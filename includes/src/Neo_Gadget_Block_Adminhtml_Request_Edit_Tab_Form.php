<?php
class Neo_Gadget_Block_Adminhtml_Request_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("gadget_form", array("legend"=>Mage::helper("gadget")->__("Item information")));

				
		
					
				$fieldset->addField("name", "text", array(
					"label" => Mage::helper("gadget")->__("name"),
					"name" => "name",
				));
					

				if (Mage::getSingleton("adminhtml/session")->getRequestData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getRequestData());
					Mage::getSingleton("adminhtml/session")->setRequestData(null);
				} 
				elseif(Mage::registry("request_data")) {
				    $form->setValues(Mage::registry("request_data")->getData());
				}
				return parent::_prepareForm();
		}
}
