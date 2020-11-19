<?php
class Neo_Vowdelight_Block_Adminhtml_Vowdelight_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("vowdelight_form", array("legend"=>Mage::helper("vowdelight")->__("Item information")));

				
						$fieldset->addField("sku", "text", array(
						"label" => Mage::helper("vowdelight")->__("Sku"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "sku",
						));
					
						$fieldset->addField("old_order_no", "text", array(
						"label" => Mage::helper("vowdelight")->__("Old Order No"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "old_order_no",
						));
					
						$fieldset->addField("old_imei_no", "text", array(
						"label" => Mage::helper("vowdelight")->__("Old IMEI No"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "old_imei_no",
						));
					
						$fieldset->addField("rvp_awb_no", "text", array(
						"label" => Mage::helper("vowdelight")->__("RVP AWB No"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "rvp_awb_no",
						));
					
						$fieldset->addField("new_order_no", "text", array(
						"label" => Mage::helper("vowdelight")->__("New Order No"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "new_order_no",
						));
					
						$fieldset->addField("new_imei_no", "text", array(
						"label" => Mage::helper("vowdelight")->__("New IMEI No"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "new_imei_no",
						));
					
						$fieldset->addField("new_awb_no", "text", array(
						"label" => Mage::helper("vowdelight")->__("New AWB No"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "new_awb_no",
						));
					
						$fieldset->addField("customer_id", "text", array(
						"label" => Mage::helper("vowdelight")->__("Customer ID"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "customer_id",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getVowdelightData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getVowdelightData());
					Mage::getSingleton("adminhtml/session")->setVowdelightData(null);
				} 
				elseif(Mage::registry("vowdelight_data")) {
				    $form->setValues(Mage::registry("vowdelight_data")->getData());
				}
				return parent::_prepareForm();
		}
}
