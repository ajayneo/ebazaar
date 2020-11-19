<?php
class Neo_Gadget_Block_Adminhtml_Gadget_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("gadget_form", array("legend"=>Mage::helper("gadget")->__("Item information")));

				$fieldset->addField("name", "text", array(
					"label" => Mage::helper("gadget")->__("Name"),
					'after_element_html' => '<small>Product Name To Be Shown In Frontend.</small>',
					'required'  => true,
					"name" => "name",
				));

				$brands = Neo_Gadget_Model_Brands::getBrands();

				$fieldset->addField("brand", "select", array(
					"label" => Mage::helper("gadget")->__("Brand"), 
					'after_element_html' => '<small>Brand To Which Product Belongs.</small>',
					'required'  => true,
					'values' => $brands,
					"name" => "brand",
				));

				$fieldset->addField("working_price", "text", array(
					"label" => Mage::helper("gadget")->__("Working Price"),
					'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					"name" => "working_price",
				));

				$fieldset->addField("non_working_price", "text", array(
					"label" => Mage::helper("gadget")->__("Non Working Price"),
					'after_element_html' => '<small>Product Price If In Non Working Condition.</small>',
					'required'  => true,
					"name" => "non_working_price", 
				));

				$fieldset->addField('image', 'image', array(
					'label' => Mage::helper('gadget')->__('Image'),
					'name' => 'image',
					'note' => '(*.jpg, *.png, *.gif)',
				));
					

				if (Mage::getSingleton("adminhtml/session")->getGadgetData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getGadgetData());
					Mage::getSingleton("adminhtml/session")->setGadgetData(null);
				} 
				elseif(Mage::registry("gadget_data")) {
				    $form->setValues(Mage::registry("gadget_data")->getData());
				}
				return parent::_prepareForm();
		}
}
