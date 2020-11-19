<?php
class Neo_Deliveryvalidator_Block_Adminhtml_Deliveryvalidator_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("deliveryvalidator_form", array("legend"=>Mage::helper("deliveryvalidator")->__("Item information")));

				
						$fieldset->addField("pincodes", "textarea", array(
						"label" => Mage::helper("deliveryvalidator")->__("Pincode List"),
						"name" => "pincodes",
						));
					
						$fieldset->addField("rules", "text", array(
						"label" => Mage::helper("deliveryvalidator")->__("Delivery Days"),
						"name" => "rules",
						));
									
						 $fieldset->addField('status', 'select', array(
						'label'     => Mage::helper('deliveryvalidator')->__('Status'),
						'values'   => Neo_Deliveryvalidator_Block_Adminhtml_Deliveryvalidator_Grid::getValueArray2(),
						'name' => 'status',
						));

				if (Mage::getSingleton("adminhtml/session")->getDeliveryvalidatorData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getDeliveryvalidatorData());
					Mage::getSingleton("adminhtml/session")->setDeliveryvalidatorData(null);
				} 
				elseif(Mage::registry("deliveryvalidator_data")) {
				    $form->setValues(Mage::registry("deliveryvalidator_data")->getData());
				}
				return parent::_prepareForm();
		}
}
