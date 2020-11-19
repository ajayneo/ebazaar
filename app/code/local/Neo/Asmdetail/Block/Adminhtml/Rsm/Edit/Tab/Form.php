<?php
class Neo_Asmdetail_Block_Adminhtml_Rsm_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("asmdetail_form", array("legend"=>Mage::helper("asmdetail")->__("Item information")));

				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("asmdetail")->__("CRM Name"),
						"name" => "name",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getRsmData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getRsmData());
					Mage::getSingleton("adminhtml/session")->setRsmData(null);
				} 
				elseif(Mage::registry("rsm_data")) {
				    $form->setValues(Mage::registry("rsm_data")->getData());
				}
				return parent::_prepareForm();
		}
}
