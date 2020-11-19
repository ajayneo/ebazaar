<?php
class Neo_Affiliatelimit_Block_Adminhtml_Limit_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("affiliatelimit_form", array("legend"=>Mage::helper("affiliatelimit")->__("Item information")));

								
				 $fieldset->addField('email', 'select', array(
				'label'     => Mage::helper('affiliatelimit')->__('Email'),
				'values'   => Neo_Affiliatelimit_Block_Adminhtml_Limit_Grid::getValueArray0(),
				'name' => 'email',
				));

				$fieldset->addField("credit", "text", array(
				"label" => Mage::helper("affiliatelimit")->__("Credit"),
				"name" => "credit",
				));
					

				if (Mage::getSingleton("adminhtml/session")->getLimitData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getLimitData());
					Mage::getSingleton("adminhtml/session")->setLimitData(null);
				} 
				elseif(Mage::registry("limit_data")) {
				    $form->setValues(Mage::registry("limit_data")->getData());
				}
				return parent::_prepareForm();
		}
}
