<?php
class Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("suvidha_form", array("legend"=>Mage::helper("suvidha")->__("Item information")));

				
						$fieldset->addField("email_id", "text", array(
						"label" => Mage::helper("suvidha")->__("Email Id"),
						"name" => "email_id",
						));
					
						$fieldset->addField("company_name", "text", array(
						"label" => Mage::helper("suvidha")->__("Company Name"),
						"name" => "company_name",
						));
					
						$fieldset->addField("gst_no", "text", array(
						"label" => Mage::helper("suvidha")->__("GST No."),
						"name" => "gst_no",
						));
					
						$fieldset->addField("partner_details", "text", array(
						"label" => Mage::helper("suvidha")->__("Partenr Details"),
						"name" => "partner_details",
						));
									
						 $fieldset->addField('partnership_type', 'select', array(
						'label'     => Mage::helper('suvidha')->__('Partnership Type'),
						'values'   => Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid::getValueArray4(),
						'name' => 'partnership_type',
						));
						$fieldset->addField("billing_add", "text", array(
						"label" => Mage::helper("suvidha")->__("Billing Address"),
						"name" => "billing_add",
						));
					
						$fieldset->addField("billing_city", "text", array(
						"label" => Mage::helper("suvidha")->__("City"),
						"name" => "billing_city",
						));
					
						$fieldset->addField("billing_state", "text", array(
						"label" => Mage::helper("suvidha")->__("State"),
						"name" => "billing_state",
						));
					
						$fieldset->addField("billing_zip_code", "text", array(
						"label" => Mage::helper("suvidha")->__("Zip Code"),
						"name" => "billing_zip_code",
						));
					
						$fieldset->addField("company_add", "text", array(
						"label" => Mage::helper("suvidha")->__("Company Address"),
						"name" => "company_add",
						));
					
						$fieldset->addField("company_city", "text", array(
						"label" => Mage::helper("suvidha")->__("Company City"),
						"name" => "company_city",
						));
					
						$fieldset->addField("company_state", "text", array(
						"label" => Mage::helper("suvidha")->__("Company State"),
						"name" => "company_state",
						));
					
						$fieldset->addField("company_zip_code", "text", array(
						"label" => Mage::helper("suvidha")->__("Company Zip Code"),
						"name" => "company_zip_code",
						));
					
						$fieldset->addField("credit_requested", "text", array(
						"label" => Mage::helper("suvidha")->__("Credit Requested"),
						"name" => "credit_requested",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('business_commenced', 'date', array(
						'label'        => Mage::helper('suvidha')->__('Date business commenced'),
						'name'         => 'business_commenced',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));
						$fieldset->addField("nature_of_business", "text", array(
						"label" => Mage::helper("suvidha")->__("Nature Of Business"),
						"name" => "nature_of_business",
						));
					
						$fieldset->addField("bank_acc_name", "text", array(
						"label" => Mage::helper("suvidha")->__("Bank Account Name"),
						"name" => "bank_acc_name",
						));
					
						$fieldset->addField("bank_acc_no", "text", array(
						"label" => Mage::helper("suvidha")->__("Bank Account Number"),
						"name" => "bank_acc_no",
						));
					
						$fieldset->addField("bank_name", "text", array(
						"label" => Mage::helper("suvidha")->__("Bank Name"),
						"name" => "bank_name",
						));
					
						$fieldset->addField("bank_branch", "text", array(
						"label" => Mage::helper("suvidha")->__("Bank Branch"),
						"name" => "bank_branch",
						));
									
						 $fieldset->addField('bank_acc_type', 'select', array(
						'label'     => Mage::helper('suvidha')->__('Bank Account Type'),
						'values'   => Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid::getValueArray20(),
						'name' => 'bank_acc_type',
						));
						$fieldset->addField("bank_ifsc", "text", array(
						"label" => Mage::helper("suvidha")->__("Bank Ifsc"),
						"name" => "bank_ifsc",
						));
					
						$fieldset->addField("mobile", "text", array(
						"label" => Mage::helper("suvidha")->__("Mobile No."),
						"name" => "mobile",
						));
					
						$fieldset->addField("ref_company1", "text", array(
						"label" => Mage::helper("suvidha")->__("Reference Company Name 1"),
						"name" => "ref_company1",
						));
					
						$fieldset->addField("ref_address1", "text", array(
						"label" => Mage::helper("suvidha")->__("Reference Company Address 1"),
						"name" => "ref_address1",
						));
					
						$fieldset->addField("ref_nature_of_business1", "text", array(
						"label" => Mage::helper("suvidha")->__("Reference Company Nature Of Business 1"),
						"name" => "ref_nature_of_business1",
						));
					
						$fieldset->addField("ref_phone1", "text", array(
						"label" => Mage::helper("suvidha")->__("Reference Company Phone No."),
						"name" => "ref_phone1",
						));
					
						$fieldset->addField("ref_company2", "text", array(
						"label" => Mage::helper("suvidha")->__("Reference Company Name 2"),
						"name" => "ref_company2",
						));
					
						$fieldset->addField("ref_address2", "text", array(
						"label" => Mage::helper("suvidha")->__("Reference Company Address 2"),
						"name" => "ref_address2",
						));
					
						$fieldset->addField("ref_nature_of_business2", "text", array(
						"label" => Mage::helper("suvidha")->__("Reference Company Nature Of Business 2"),
						"name" => "ref_nature_of_business2",
						));
					
						$fieldset->addField("ref_phone2", "text", array(
						"label" => Mage::helper("suvidha")->__("Reference Company Phone No."),
						"name" => "ref_phone2",
						));
									
						$fieldset->addField('sign_file1', 'image', array(
						'label' => Mage::helper('suvidha')->__('Signature 1'),
						'name' => 'sign_file1',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("sign_name1", "text", array(
						"label" => Mage::helper("suvidha")->__("Signature Name 1 "),
						"name" => "sign_name1",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('sign_date1', 'date', array(
						'label'        => Mage::helper('suvidha')->__('Signature Date 1'),
						'name'         => 'sign_date1',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));				
						$fieldset->addField('sign_file2', 'image', array(
						'label' => Mage::helper('suvidha')->__('Signature 2'),
						'name' => 'sign_file2',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("sign_name2", "text", array(
						"label" => Mage::helper("suvidha")->__("Signature Name 2"),
						"name" => "sign_name2",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('sign_date2', 'date', array(
						'label'        => Mage::helper('suvidha')->__('Signature Date 2'),
						'name'         => 'sign_date2',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));				
						$fieldset->addField('sign_file3', 'image', array(
						'label' => Mage::helper('suvidha')->__('Signature  3'),
						'name' => 'sign_file3',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("sign_name3", "text", array(
						"label" => Mage::helper("suvidha")->__("Signature Name 2"),
						"name" => "sign_name3",
						));
					
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('sign_date', 'date', array(
						'label'        => Mage::helper('suvidha')->__('Signature Date 3'),
						'name'         => 'sign_date',
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));				
						$fieldset->addField('aadhar', 'image', array(
						'label' => Mage::helper('suvidha')->__('Aadhar Card'),
						'name' => 'aadhar',
						'note' => '(*.jpg, *.png, *.gif)',
						));				
						$fieldset->addField('pancard', 'image', array(
						'label' => Mage::helper('suvidha')->__('Pan Card'),
						'name' => 'pancard',
						'note' => '(*.jpg, *.png, *.gif)',
						));				
						$fieldset->addField('postcheque', 'image', array(
						'label' => Mage::helper('suvidha')->__('Post Cheque'),
						'name' => 'postcheque',
						'note' => '(*.jpg, *.png, *.gif)',
						));				
						$fieldset->addField('bankst', 'image', array(
						'label' => Mage::helper('suvidha')->__('Bank Statement'),
						'name' => 'bankst',
						'note' => '(*.jpg, *.png, *.gif)',
						));

						$fieldset->addField("assigned_credit_limit", "text", array(
						"label" => Mage::helper("suvidha")->__("Assigned Credit limit"),
						"name" => "assigned_credit_limit",
						));

						$id = $this->getRequest()->getParam('id');
						$fieldset->addField('approve', 'button', array(
				            'label' => 'Approve Credit',
				            'value' => 'Approve',
				            'name'  => 'approve',
				            'class' => 'form-button',
				            // 'onclick' => "setLocation('{$this->getUrl('*/*/approvecredit/id/'.$id)}')",
				            'onclick' => "suvidhaapprove($id)",
				        ));

				        $fieldset->addField('reject', 'button', array(
				            'label' => 'Reject Credit',
				            'value' => 'Reject',
				            'name'  => 'reject',
				            'class' => 'form-button',
				            'onclick' => "suvidhareject($id)",
				        ));

				        $fieldset->addField('status', 'select', array(
						'label'     => 'Request Status',
						'values'   => Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid::getValueArrayStatus(),
						'name' => 'status',
						'readonly' => true,
						'disabled' => true,
						));

						$fieldset->addField("credit_approved_by", "text", array(
						"label" => "Credit Approved by",
						"name" => "credit_approved_by",
						));

						$fieldset->addField('credit_approval_date', 'date', array(
						'label'        => Mage::helper('suvidha')->__('Credit Approval Date'),
						'name'         => 'credit_approval_date',
						'time' 		   => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));

						$fieldset->addField('mapped_status', 'select', array(
						'label'     => 'Mapped Status',
						'values'   => Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid::getValueArrayMapped(),
						'name' => 'mapped_status',
						));

						$fieldset->addField('mapped_details', 'textarea', array(
				          'label'     => 'Mapped Details',
				          'name'      => 'mapped_details',
				          'value'  => '<b><b/>',
				        ));

				if (Mage::getSingleton("adminhtml/session")->getCreditsuvidhaData())
				{
					$form->addValues(Mage::getSingleton("adminhtml/session")->getCreditsuvidhaData());
					Mage::getSingleton("adminhtml/session")->setCreditsuvidhaData(null);
				} 
				elseif(Mage::registry("creditsuvidha_data")) {
				    $form->addValues(Mage::registry("creditsuvidha_data")->getData());
				}
				return parent::_prepareForm();
		}
}

?>
<script type="text/javascript">
	//admin html ajax request
	//Mahesh Gurav
	//09 March 2018
	function suvidhaapprove(id) {
		var credit = jQuery('#assigned_credit_limit').val();
		var form_key = '<?php echo Mage::getSingleton('core/session')->getFormKey(); ?>';
		var url = '<?php echo Mage::helper("adminhtml")->getUrl('/adminhtml_creditsuvidha/approvecredit') ?>';
		var param = '&credit='+credit+'&id='+id;
        new Ajax.Request(url, {
            method: 'post',
            dataType: 'json',
            parameters: param,
            onSuccess: function(transport) {
                var response_test = transport.responseText;
                	alert(response_test);
                	location.reload();
            }
        });
	}
	function suvidhareject(id) {
		// var credit = jQuery('#assigned_credit_limit').val();
		var form_key = '<?php echo Mage::getSingleton('core/session')->getFormKey(); ?>';
		var url = '<?php echo Mage::helper("adminhtml")->getUrl('/adminhtml_creditsuvidha/rejectcredit') ?>';
		var param = '&id='+ id;
        new Ajax.Request(url, {
            method: 'post',
            dataType: 'json',
            parameters: param,
            onSuccess: function(transport) {
                var response_test = transport.responseText;
                alert(response_test);
                location.reload();
            }
        });
	}
</script>