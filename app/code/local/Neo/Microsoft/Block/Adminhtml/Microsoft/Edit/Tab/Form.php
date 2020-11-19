<?php
class Neo_Microsoft_Block_Adminhtml_Microsoft_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("microsoft_form", array("legend"=>Mage::helper("microsoft")->__("Item information")));

				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("microsoft")->__("Name"),
						"name" => "name",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getMicrosoftData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getMicrosoftData());
					Mage::getSingleton("adminhtml/session")->setMicrosoftData(null);
				} 
				elseif(Mage::registry("microsoft_data")) {
				    $form->setValues(Mage::registry("microsoft_data")->getData());
				}
				return parent::_prepareForm();
		}
}
