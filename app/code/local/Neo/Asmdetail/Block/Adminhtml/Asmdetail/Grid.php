<?php

class Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("asmdetailGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("asmdetail/asmdetail")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
					"header" => Mage::helper("asmdetail")->__("ID"),
					"align" =>"right",
					"width" => "50px",
				    "type" => "number",
					"index" => "id",
				));
                
				$this->addColumn('name', array(
					'header' => Mage::helper('asmdetail')->__('Asm Name'),
					'index' => 'name',
					'type' => 'options',
					'options'=>Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getOptionArray0(),				
				));
				
				$this->addColumn('state', array(
					'header' => Mage::helper('asmdetail')->__('Asm State'),
					'index' => 'state',
					'type' => 'options',
					'options'=>Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getOptionArray1(),				
				));
						
				$this->addColumn("email", array(
					"header" => Mage::helper("asmdetail")->__("Asm Email"),
					"index" => "email",
				));

						
				$this->addColumn('rsmname', array(
					'header' => Mage::helper('asmdetail')->__('Rsm Name'),
					'index' => 'rsmname',
					'type' => 'options',
					'options'=>Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getOptionArray2(),			 	
				));
				
				$this->addColumn('rsmstate', array(
					'header' => Mage::helper('asmdetail')->__('Rsm Region'),
					'index' => 'rsmstate',
					'type' => 'options',
					'options'=>Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getOptionArray11(),				
				));  

				$this->addColumnAfter('category_id',array(
				       "header" => Mage::helper("asmdetail")->__("Rsm Email"),
					   "index" => "rsmemail"
				    ),
				    'rsmstate'
				);     


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
			$this->getMassactionBlock()->addItem('remove_asmdetail', array(
					 'label'=> Mage::helper('asmdetail')->__('Remove ASM Detail'),
					 'url'  => $this->getUrl('*/adminhtml_asmdetail/massRemove'),
					 'confirm' => Mage::helper('asmdetail')->__('Are you sure?')
				));
			return $this;
		}

		static public function getOptionArray2() 
		{
            $data_array=array(); 
			
			$obj = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts();
            $groups = $obj->getOptionArray2(); 

            foreach($groups as $group){
            	if($group['value'] > 0){
            		$data_array[$group['value']] = $group['label'];
            	}   
            }

            return($data_array);
		}
			
		static public function getOptionArray0()
		{
            $data_array=array(); 
			
			$obj = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts();
            $groups = $obj->getAllOptions(); 

            foreach($groups as $group){
            	if($group['value'] > 0){
            		$data_array[$group['value']] = $group['label'];
            	}   
            }

            return($data_array);
		}

		static public function getValueArray2()
		{
            $data_array=array();
            $data_array[]=array('value'=>'','label'=>''); 
			foreach(Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}

		static public function getValueArray0()
		{
            $data_array=array();
            $data_array[]=array('value'=>'','label'=>''); 
			foreach(Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getOptionArray0() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}

		static public function getOptionArray11()
		{
            $data_array=array(); 
            $data_array[520] = 'North 1';
            $data_array[485] = 'North 2';
            $data_array[486] = 'South';
            $data_array[487] = 'East';
            $data_array[488] = 'West';
             return($data_array);
        }
		
		static public function getOptionArray1()
		{
            $data_array=array(); 
            $data_array[520] = 'Andaman Nicobar';
            $data_array[485] = 'Andra Pradesh';
            $data_array[486] = 'Arunachal Pradesh';
            $data_array[487] = 'Assam';
            $data_array[488] = 'Bihar';
            $data_array[489] = 'Chandigarh';
            $data_array[490] = 'Chhattisgarh';
            $data_array[491] = 'Dadra and Nagar Haveli';
            $data_array[492] = 'Daman and Diu';
            $data_array[493] = 'Delhi';
            $data_array[494] = 'Goa';
            $data_array[495] = 'Gujarat';
            $data_array[496] = 'Haryana';
            $data_array[497] = 'Himachal Pradesh';
            $data_array[498] = 'Jammu and Kashmir';
            $data_array[499] = 'Jharkhand';
            $data_array[500] = 'Karnataka';
            $data_array[501] = 'Kerala';
            $data_array[502] = 'Lakshadeep';
            $data_array[503] = 'Madya Pradesh';
            $data_array[504] = 'Maharashtra';
            $data_array[505] = 'Manipur';
            $data_array[506] = 'Meghalaya';
            $data_array[507] = 'Mizoram';
            $data_array[508] = 'Nagaland';
            $data_array[510] = 'Orissa';
            $data_array[511] = 'Pondicherry';
            $data_array[512] = 'Punjab';
            $data_array[513] = 'Rajasthan';
            $data_array[514] = 'Sikkim';
            $data_array[515] = 'Tamil Nadu';
            $data_array[555] = 'Telangana';
            $data_array[516] = 'Tripura';
            $data_array[517] = 'Uttar Pradesh';
            $data_array[518] = 'Uttaranchal';
            $data_array[519] = 'West Bengal';
			
            return($data_array);
		}

		static public function getValueArray1()
		{
            $data_array=array();
            $data_array[]=array('value'=>'','label'=>''); 
			foreach(Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getOptionArray1() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}

		static public function getValueArray11()
		{
            $data_array=array();
            $data_array[]=array('value'=>'','label'=>''); 
			foreach(Neo_Asmdetail_Block_Adminhtml_Asmdetail_Grid::getOptionArray11() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}