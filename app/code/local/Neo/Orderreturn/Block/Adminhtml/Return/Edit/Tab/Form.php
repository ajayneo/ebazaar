<?php
class Neo_Orderreturn_Block_Adminhtml_Return_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{
			$form = new Varien_Data_Form();
			$this->setForm($form);
			$fieldset = $form->addFieldset("orderreturn_form", array("legend"=>Mage::helper("orderreturn")->__("Item information")));

	
			$order_info =  $fieldset->addField("order_number", "note", array(
			"label" => Mage::helper("orderreturn")->__("Order Details"),
			"name" => "order_number",
			"index" => "order_number"
			));

			//Set your renderer file path here
			$order_details = new Neo_Orderreturn_Block_Adminhtml_Return_Renderer_Order();
			$order_info->setRenderer($order_details);

			
			if (Mage::getSingleton("adminhtml/session")->getReturnData())
			{
				$form->setValues(Mage::getSingleton("adminhtml/session")->getReturnData());
				Mage::getSingleton("adminhtml/session")->setReturnData(null);
			} 
			elseif(Mage::registry("return_data")) {
			    $form->setValues(Mage::registry("return_data")->getData()); 
			}
			return parent::_prepareForm();
		}
		
}
