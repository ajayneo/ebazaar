<?php
class Neo_Supersale_Block_Adminhtml_Super_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("supersale_form", array("legend"=>Mage::helper("supersale")->__("Item information")));

				
						$fieldset->addField("id", "text", array(
						"label" => Mage::helper("supersale")->__("ID"),
						"name" => "id",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getSuperData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getSuperData());
					Mage::getSingleton("adminhtml/session")->setSuperData(null);
				} 
				elseif(Mage::registry("super_data")) {
				    $form->setValues(Mage::registry("super_data")->getData());
				}
				return parent::_prepareForm();
		}
}
