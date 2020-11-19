<?php
class Neo_Asmdetail_Block_Adminhtml_Asmdetail_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("asmdetail_form", array("legend"=>Mage::helper("asmdetail")->__("Item information")));

								
				$fieldset->addField('name', 'select', array(
					'label'     => Mage::helper('asmdetail')->__('Asm Name'),
					'values'   => Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getValueArray0(),
					'name' => 'name',					
					"class" => "required-entry",
					"required" => true,
				));				
				 
				$fieldset->addField('state', 'select', array(
					'label'     => Mage::helper('asmdetail')->__('Asm State'),
					'values'   => Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getValueArray1(),
					'name' => 'state',					
					"class" => "required-entry",
					"required" => true,
				));
					
				$fieldset->addField("email", "text", array(
					"label" => Mage::helper("asmdetail")->__("Asm Email"),					
					"class" => "required-entry",
					"required" => true,
					"name" => "email",
				));

				$fieldset->addField('rsmname', 'select', array(
					'label'     => Mage::helper('asmdetail')->__('Rsm Name'),
					'values'   => Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getValueArray2(),
					'name' => 'rsmname',	 				
					"class" => "required-entry",
					"required" => true,
				));				
				 
				$fieldset->addField('rsmstate', 'select', array(
					'label'     => Mage::helper('asmdetail')->__('Rsm Region'),
					'values'   => Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getValueArray11(),
					'name' => 'rsmstate',					
					"class" => "required-entry",
					"required" => true,
				));
					
				$fieldset->addField("rsmemail", "text", array(
					"label" => Mage::helper("asmdetail")->__("Rsm Email"),					
					"class" => "required-entry",
					"required" => true,
					"name" => "rsmemail",
				));
					

				if (Mage::getSingleton("adminhtml/session")->getAsmdetailData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getAsmdetailData());
					Mage::getSingleton("adminhtml/session")->setAsmdetailData(null);
				} 
				elseif(Mage::registry("asmdetail_data")) {
				    $form->setValues(Mage::registry("asmdetail_data")->getData());
				}
				return parent::_prepareForm();
		}
}
