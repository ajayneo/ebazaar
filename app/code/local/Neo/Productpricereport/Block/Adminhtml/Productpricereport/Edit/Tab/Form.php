<?php
class Neo_Productpricereport_Block_Adminhtml_Productpricereport_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("productpricereport_form", array("legend"=>Mage::helper("productpricereport")->__("Item information")));

				
						$fieldset->addField("id", "text", array(
						"label" => Mage::helper("productpricereport")->__("ID"),
						"name" => "id",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getProductpricereportData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getProductpricereportData());
					Mage::getSingleton("adminhtml/session")->setProductpricereportData(null);
				} 
				elseif(Mage::registry("productpricereport_data")) {
				    $form->setValues(Mage::registry("productpricereport_data")->getData());
				}
				return parent::_prepareForm();
		}
}
