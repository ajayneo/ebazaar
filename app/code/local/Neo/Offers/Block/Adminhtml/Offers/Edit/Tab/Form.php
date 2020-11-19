<?php
class Neo_Offers_Block_Adminhtml_Offers_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("offers_form", array("legend"=>Mage::helper("offers")->__("Item information")));

				
						$fieldset->addField("offer", "text", array(
						"label" => Mage::helper("offers")->__("Offer"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "offer",
						));
					
						$fieldset->addField("offer_desc", "textarea", array(
						"label" => Mage::helper("offers")->__("Offer Description"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "offer_desc",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getOffersData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getOffersData());
					Mage::getSingleton("adminhtml/session")->setOffersData(null);
				} 
				elseif(Mage::registry("offers_data")) {
				    $form->setValues(Mage::registry("offers_data")->getData());
				}
				return parent::_prepareForm();
		}
}
