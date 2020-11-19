<?php
class Neo_Affiliatecommision_Block_Adminhtml_Commision_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("affiliatecommision_form", array("legend"=>Mage::helper("affiliatecommision")->__("Item information")));

				

				if (Mage::getSingleton("adminhtml/session")->getCommisionData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCommisionData());
					Mage::getSingleton("adminhtml/session")->setCommisionData(null);
				} 
				elseif(Mage::registry("commision_data")) {
				    $form->setValues(Mage::registry("commision_data")->getData());
				}
				return parent::_prepareForm();
		}
}
