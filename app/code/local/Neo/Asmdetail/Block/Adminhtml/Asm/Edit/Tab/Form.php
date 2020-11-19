<?php
class Neo_Asmdetail_Block_Adminhtml_Asm_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("asmdetail_form", array("legend"=>Mage::helper("asmdetail")->__("Item information")));

				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("asmdetail")->__("ARM Name"),
						"name" => "name",
						));

						$fieldset->addField('enabled', 'select', array(
				          'label'     => Mage::helper("asmdetail")->__('Is Active'),
				          'class'     => 'required-entry',
				          'required'  => true,
				          'name'      => 'enabled',
				          'onclick' => "",
				          'onchange' => "",
				          'value'  => '1',
				          'values' => array('-1'=>'Please Select..','0' => 'No','1' => 'Yes'),
				          'disabled' => false,
				          'readonly' => false,
				          'after_element_html' => '<br/><small>Set Is Active Yes/No</small>',
				          'tabindex' => 1
				        ));
					

				if (Mage::getSingleton("adminhtml/session")->getAsmData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getAsmData());
					Mage::getSingleton("adminhtml/session")->setAsmData(null);
				} 
				elseif(Mage::registry("asm_data")) {
				    $form->setValues(Mage::registry("asm_data")->getData());
				}
				return parent::_prepareForm();
		}
}
