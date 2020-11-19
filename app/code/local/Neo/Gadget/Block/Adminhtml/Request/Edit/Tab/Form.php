<?php
class Neo_Gadget_Block_Adminhtml_Request_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);

				$fieldset = $form->addFieldset("gadget_form_data", array("legend"=>Mage::helper("gadget")->__("Item information")));

				$fieldset->addField("id", "text", array(
					"label" => Mage::helper("gadget")->__("ID"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "id", 
				));

				$fieldset->addField("sku", "text", array(
					"label" => Mage::helper("gadget")->__("Product SKU"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "sku", 
				));

				$fieldset->addField("proname", "text", array(
					"label" => Mage::helper("gadget")->__("Product Name"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "proname",
				));

				$fieldset->addField("price", "text", array(
					"label" => Mage::helper("gadget")->__("Product Price"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "price",
				));

				$fieldset->addField("brand", "text", array(
					"label" => Mage::helper("gadget")->__("Product Brand"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "brand",
				));


				$fieldsetss = $form->addFieldset("gadget_form_customer", array("legend"=>Mage::helper("gadget")->__("Customer Information")));

				
				$fieldsetss->addField("address", "text", array(
					"label" => Mage::helper("gadget")->__("Address"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "address",
				));


				$fieldsetss->addField("pincode", "text", array(
					"label" => Mage::helper("gadget")->__("Customer Pincode"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "pincode",
				));

				/*$fieldsetss->addField("city", "text", array(
					"label" => Mage::helper("gadget")->__("Customer City"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "city",
				));*/

				$fieldsetss->addField("email", "text", array(
					"label" => Mage::helper("gadget")->__("Customer Email"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					'disabled' => true,
		  			'readonly' => true,
					"name" => "email",  
				));

				$fieldsetss->addField("mobile", "text", array(
					"label" => Mage::helper("gadget")->__("Customer Mobile"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					"name" => "mobile",
					'disabled' => true,
		  			'readonly' => true,
				));

				if(Mage::registry("request_data")->getData('is_order_confirmed') == 'Yes'){

					//Retailers Data
					$fieldretAddress = $form->addFieldset("gadget_form_retailer_info", array("legend"=>Mage::helper("gadget")->__("Retailer Information")));
					
					
					$script = $fieldretAddress->addField("confrm_by_retailer_name_in_bank", "text", array(
						"label" => Mage::helper("gadget")->__("Name in Bank"),
						"name" => "confrm_by_retailer_name_in_bank",
						'readonly' => true,
					));

					$script = $fieldretAddress->addField("confrm_by_retailer_bank_name", "text", array(
						"label" => Mage::helper("gadget")->__("Bank Name"),
						"name" => "confrm_by_retailer_bank_name",
						'readonly' => true,
					));

					$script = $fieldretAddress->addField("confrm_by_retailer_acct_number", "text", array(
						"label" => Mage::helper("gadget")->__("Account Number"),
						"name" => "confrm_by_retailer_acct_number",
						'readonly' => true,
					));
					
					$script = $fieldretAddress->addField("confrm_by_retailer_ifsc_code", "text", array(
						"label" => Mage::helper("gadget")->__("IFSC Code"),
						"name" => "confrm_by_retailer_ifsc_code",
						'readonly' => true,
					));
					

					//$fieldretAddress->addType('textarea', 'Neo_Gadget_Block_Adminhtml_Request_Renderer_Address');
					$retAddress = $fieldretAddress->addField("confrm_by_retailer_address_id", "textarea", array(
						"label" => Mage::helper("gadget")->__("Address"),
						"name" => "confrm_by_retailer_address_id",
						'index'=>"confrm_by_retailer_address_id",
					));


					$renderer = new Neo_Gadget_Block_Adminhtml_Request_Renderer_Address();
					$retAddress->setRenderer($renderer);
				}

				//pickup field set
				$fieldpickup = $form->addFieldset("gadget_form_order_pickup", array("legend"=>Mage::helper("gadget")->__("Pickup Information")));
				
				$button = $this->getLayout()->createBlock('adminhtml/widget_button')
				    ->setData(array(
				        'label'   => 'Reverse Pickup',
				        'onclick' => 'reversePickup()',
				        'class'   => 'ecom_qc_pickup',
				    ));
				$button->setName('generate_reverse_pickup');

				$fieldpickup->setHeaderBar($button->toHtml());

				$renderer = new Neo_Gadget_Block_Adminhtml_Request_Renderer_Reversepickup();
				$button->setRenderer($renderer);
				
				$script = $fieldpickup->addField("awb_number", "text", array(
					"label" => Mage::helper("gadget")->__("ECOM QC RVP AWB Number"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					//'required'  => true,
					"name" => "awb_number",
					//'disabled' => true,
		  			'readonly' => true,
				));

				$script->setAfterElementHtml("<div id='message'></div><script>
				jQuery(document).ready(function(){
					var awb_number = jQuery('#awb_number').val();
					
					if(awb_number != ''){
						jQuery('.ecom_qc_pickup').css('display','none');
						jQuery('#status').css('pointer-events','none');
					}
				});
				function reversePickup(){

					if(jQuery('#status').val() != 'approved'){
						alert('Please approve request!');
						return false;
					}

					var id = jQuery('#id').val();
					
					jQuery.ajax({
					    'url': '".$this->getUrl('*/*/sendForPickup')."', 
                        'data': {productId: id},
                        success: function (data) {
                        	var obj = jQuery.parseJSON(data);
							jQuery('#awb_number').val(obj.awb_number);
                        	jQuery('#status').val(obj.status);
                        	jQuery('#message').html(obj.message);
                        	if(obj.success == 0){
                        		jQuery('#message').addClass('error');
                        	}
							return false;
                        }

					});
				}
				</script>");

				//Request Info
				$fieldsetsso = $form->addFieldset("gadget_form_order_status", array("legend"=>Mage::helper("gadget")->__("Order Information")));
				
				$statusOptionsArray = Mage::helper('gadget')->getGadgetStatusOptions();

				$fieldsetsso->addField("status", "select", array(
					"label" => Mage::helper("gadget")->__("Status"),
					//'after_element_html' => '<small>Product Price If In Working Condition.</small>',
					'required'  => true,
					"name" => "status",
					'options' => $statusOptionsArray,  
				));


				$fieldsets = $form->addFieldset("gadget_form", array("legend"=>Mage::helper("gadget")->__("Item options")));
				
				$nextdate = $fieldsets->addField('option', 'note',
					array(
					 'name'      => 'option', 
					 'label'     => 'Close Date',
					 'class'     => 'required-entry validate-date validate-date-range date-range-custom_theme-from',
					 'required'  => true,
					 //'image'     => $this->getSkinUrl('images/grid-cal.gif'),
					 //'format'    => 'YYYY-MM-dd',
					 //'value'   => $array['next_date']
				));


				//Set your renderer file path here
				$renderer = new Neo_Gadget_Block_Adminhtml_Request_Renderer_Optionform();
				$nextdate->setRenderer($renderer);
					

				if (Mage::getSingleton("adminhtml/session")->getRequestData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getRequestData());
					Mage::getSingleton("adminhtml/session")->setRequestData(null);
				} 
				elseif(Mage::registry("request_data")) {
					
				    $form->setValues(Mage::registry("request_data")->getData());
				   // $form->setValue(array('confrm_by_retailer_address_id'=>$retAddresss));
				}
				return parent::_prepareForm();
		}
}
