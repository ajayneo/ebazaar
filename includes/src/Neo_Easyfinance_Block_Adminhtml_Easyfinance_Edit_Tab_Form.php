<?php
class Neo_Easyfinance_Block_Adminhtml_Easyfinance_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("easyfinance_form", array("legend"=>Mage::helper("easyfinance")->__("Item information")));

				
						$fieldset->addField("first_name", "text", array(
						"label" => Mage::helper("easyfinance")->__("First Name"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "first_name",
						));
					
						$fieldset->addField("last_name", "text", array(
						"label" => Mage::helper("easyfinance")->__("Last Name"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "last_name",
						));
					
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("easyfinance")->__("Email Address"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "email",
						));
					
						$fieldset->addField("phone", "text", array(
						"label" => Mage::helper("easyfinance")->__("Phone"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "phone",
						));
					
						$fieldset->addField("city", "text", array(
						"label" => Mage::helper("easyfinance")->__("City"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "city",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getEasyfinanceData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getEasyfinanceData());
					Mage::getSingleton("adminhtml/session")->setEasyfinanceData(null);
				} 
				elseif(Mage::registry("easyfinance_data")) {
				    $form->setValues(Mage::registry("easyfinance_data")->getData());
				}
				return parent::_prepareForm();
		}
}
