<?php
class Neo_Vexpressawb_Block_Adminhtml_Vexpress_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

			$form = new Varien_Data_Form();
			$this->setForm($form);
			$fieldset = $form->addFieldset("vexpressawb_form", array("legend"=>Mage::helper("vexpressawb")->__("Item information")));


			$fieldset->addField("awb", "text", array(
				"label" => Mage::helper("vexpressawb")->__("AWB"),
				"name" => "awb",
			));

			$fieldset->addField("status", "text", array(
				"label" => Mage::helper("vexpressawb")->__("Status"),
				"name" => "status",
			));
				

			if (Mage::getSingleton("adminhtml/session")->getVexpressData())
			{
				$form->setValues(Mage::getSingleton("adminhtml/session")->getVexpressData());
				Mage::getSingleton("adminhtml/session")->setVexpressData(null);
			} 
			elseif(Mage::registry("vexpress_data")) {
			    $form->setValues(Mage::registry("vexpress_data")->getData());
			}
			return parent::_prepareForm();
		}
}
