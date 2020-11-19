<?php
class Neo_Cform_Block_Adminhtml_Cst_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("cform_form", array("legend"=>Mage::helper("cform")->__("Item information")));

								
						 $fieldset->addField('state', 'select', array(
						'label'     => Mage::helper('cform')->__('State'),
						'values'   => Neo_Cform_Block_Adminhtml_Cst_Grid::getValueArray0(),
						'name' => 'state',					
						"class" => "required-entry",
						"required" => true,
						));
						/*$fieldset->addField("amount", "text", array(
						"label" => Mage::helper("cform")->__("Amount"),
						"name" => "amount",
						));*/
						$fieldset->addField("min_amount", "text", array(
						"label" => Mage::helper("cform")->__("Min.Amount"),
						"name" => "min_amount",
						));
						$fieldset->addField("max_amount", "text", array(
						"label" => Mage::helper("cform")->__("Max.Amount"),
						"name" => "max_amount",
						));
									
						 $fieldset->addField('category', 'multiselect', array(
						'label'     => Mage::helper('cform')->__('Category'),
						'values'   => Neo_Cform_Block_Adminhtml_Cst_Grid::getValueArray3(),
						'name' => 'category',					
						"class" => "required-entry",
						"required" => true,
						));
						$fieldset->addField("percentage", "text", array(
						"label" => Mage::helper("cform")->__("Vat Percentage"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "percentage",
						));
									
						 $fieldset->addField('status', 'select', array(
						'label'     => Mage::helper('cform')->__('Status'),
						'values'   => Neo_Cform_Block_Adminhtml_Cst_Grid::getValueArray5(),
						'name' => 'status',
						));

				if (Mage::getSingleton("adminhtml/session")->getCstData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCstData());
					Mage::getSingleton("adminhtml/session")->setCstData(null);
				} 
				elseif(Mage::registry("cst_data")) {
				    $form->setValues(Mage::registry("cst_data")->getData());
				}
				return parent::_prepareForm();
		}
}
