<?php

class Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("creditsuvidhaGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("suvidha/creditsuvidha")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("suvidha")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("email_id", array(
				"header" => Mage::helper("suvidha")->__("Email Id"),
				"index" => "email_id",
				));
				$this->addColumn("company_name", array(
				"header" => Mage::helper("suvidha")->__("Company Name"),
				"index" => "company_name",
				));
				$this->addColumn("gst_no", array(
				"header" => Mage::helper("suvidha")->__("GST No."),
				"index" => "gst_no",
				));
				$this->addColumn("partner_details", array(
				"header" => Mage::helper("suvidha")->__("Partenr Details"),
				"index" => "partner_details",
				));
						$this->addColumn('partnership_type', array(
						'header' => Mage::helper('suvidha')->__('Partnership Type'),
						'index' => 'partnership_type',
						'type' => 'options',
						'options'=>Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid::getOptionArray4(),				
						));
						
				$this->addColumn("billing_add", array(
				"header" => Mage::helper("suvidha")->__("Billing Address"),
				"index" => "billing_add",
				));
				$this->addColumn("billing_city", array(
				"header" => Mage::helper("suvidha")->__("City"),
				"index" => "billing_city",
				));
				$this->addColumn("billing_state", array(
				"header" => Mage::helper("suvidha")->__("State"),
				"index" => "billing_state",
				));
				$this->addColumn("billing_zip_code", array(
				"header" => Mage::helper("suvidha")->__("Zip Code"),
				"index" => "billing_zip_code",
				));
				$this->addColumn("company_add", array(
				"header" => Mage::helper("suvidha")->__("Company Address"),
				"index" => "company_add",
				));
				$this->addColumn("company_city", array(
				"header" => Mage::helper("suvidha")->__("Company City"),
				"index" => "company_city",
				));
				$this->addColumn("company_state", array(
				"header" => Mage::helper("suvidha")->__("Company State"),
				"index" => "company_state",
				));
				$this->addColumn("company_zip_code", array(
				"header" => Mage::helper("suvidha")->__("Company Zip Code"),
				"index" => "company_zip_code",
				));
				$this->addColumn("credit_requested", array(
				"header" => Mage::helper("suvidha")->__("Credit Requested"),
				"index" => "credit_requested",
				));
					$this->addColumn('business_commenced', array(
						'header'    => Mage::helper('suvidha')->__('Date business commenced'),
						'index'     => 'business_commenced',
						'type'      => 'datetime',
					));
				$this->addColumn("nature_of_business", array(
				"header" => Mage::helper("suvidha")->__("Nature Of Business"),
				"index" => "nature_of_business",
				));
				$this->addColumn("bank_acc_name", array(
				"header" => Mage::helper("suvidha")->__("Bank Account Name"),
				"index" => "bank_acc_name",
				));
				$this->addColumn("bank_acc_no", array(
				"header" => Mage::helper("suvidha")->__("Bank Account Number"),
				"index" => "bank_acc_no",
				));
				$this->addColumn("bank_name", array(
				"header" => Mage::helper("suvidha")->__("Bank Name"),
				"index" => "bank_name",
				));
				$this->addColumn("bank_branch", array(
				"header" => Mage::helper("suvidha")->__("Bank Branch"),
				"index" => "bank_branch",
				));
						$this->addColumn('bank_acc_type', array(
						'header' => Mage::helper('suvidha')->__('Bank Account Type'),
						'index' => 'bank_acc_type',
						'type' => 'options',
						'options'=>Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid::getOptionArray20(),				
						));
						
				$this->addColumn("bank_ifsc", array(
				"header" => Mage::helper("suvidha")->__("Bank Ifsc"),
				"index" => "bank_ifsc",
				));
				$this->addColumn("mobile", array(
				"header" => Mage::helper("suvidha")->__("Mobile No."),
				"index" => "mobile",
				));
				$this->addColumn("ref_company1", array(
				"header" => Mage::helper("suvidha")->__("Reference Company Name 1"),
				"index" => "ref_company1",
				));
				$this->addColumn("ref_address1", array(
				"header" => Mage::helper("suvidha")->__("Reference Company Address 1"),
				"index" => "ref_address1",
				));
				$this->addColumn("ref_nature_of_business1", array(
				"header" => Mage::helper("suvidha")->__("Reference Company Nature Of Business 1"),
				"index" => "ref_nature_of_business1",
				));
				$this->addColumn("ref_phone1", array(
				"header" => Mage::helper("suvidha")->__("Reference Company Phone No."),
				"index" => "ref_phone1",
				));
				$this->addColumn("ref_company2", array(
				"header" => Mage::helper("suvidha")->__("Reference Company Name 2"),
				"index" => "ref_company2",
				));
				$this->addColumn("ref_address2", array(
				"header" => Mage::helper("suvidha")->__("Reference Company Address 2"),
				"index" => "ref_address2",
				));
				$this->addColumn("ref_nature_of_business2", array(
				"header" => Mage::helper("suvidha")->__("Reference Company Nature Of Business 2"),
				"index" => "ref_nature_of_business2",
				));
				$this->addColumn("ref_phone2", array(
				"header" => Mage::helper("suvidha")->__("Reference Company Phone No."),
				"index" => "ref_phone2",
				));
				$this->addColumn("sign_name1", array(
				"header" => Mage::helper("suvidha")->__("Signature Name 1 "),
				"index" => "sign_name1",
				));
					$this->addColumn('sign_date1', array(
						'header'    => Mage::helper('suvidha')->__('Signature Date 1'),
						'index'     => 'sign_date1',
						'type'      => 'datetime',
					));
				$this->addColumn("sign_name2", array(
				"header" => Mage::helper("suvidha")->__("Signature Name 2"),
				"index" => "sign_name2",
				));
					$this->addColumn('sign_date2', array(
						'header'    => Mage::helper('suvidha')->__('Signature Date 2'),
						'index'     => 'sign_date2',
						'type'      => 'datetime',
					));
				$this->addColumn("sign_name3", array(
				"header" => Mage::helper("suvidha")->__("Signature Name 2"),
				"index" => "sign_name3",
				));
					$this->addColumn('sign_date', array(
						'header'    => Mage::helper('suvidha')->__('Signature Date 3'),
						'index'     => 'sign_date',
						'type'      => 'datetime',
					));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_creditsuvidha', array(
					 'label'=> Mage::helper('suvidha')->__('Remove Creditsuvidha'),
					 'url'  => $this->getUrl('*/adminhtml_creditsuvidha/massRemove'),
					 'confirm' => Mage::helper('suvidha')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray4()
		{
            $data_array=array(); 
			$data_array[0]='Sole proprietorship';
			$data_array[1]='Partnership';
			$data_array[2]='Corporation';
			$data_array[3]='Other';
            return($data_array);
		}
		static public function getValueArray4()
		{
            $data_array=array();
			foreach(Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid::getOptionArray4() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray20()
		{
            $data_array=array(); 
			$data_array[0]='Please Select';
			$data_array[1]='Current';
			$data_array[2]='Cash Credit';
			$data_array[3]='Other';
            return($data_array);
		}
		static public function getValueArray20()
		{
            $data_array=array();
			foreach(Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Grid::getOptionArray20() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}