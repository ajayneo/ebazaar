<?php

class Neo_Orderreturn_Block_Adminhtml_Return_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("returnGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("orderreturn/return")->getCollection();
				//$gridCollection = Mage::helper('orderreturn')->getGridCollection();

				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("orderreturn")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("order_number", array(
				"header" => Mage::helper("orderreturn")->__("Order Number"),
				"index" => "order_number",
				));

				$this->addColumn("canceled_imei", array(
				"header" => Mage::helper("orderreturn")->__("IMEI"),
				"index" => "canceled_imei",
				//'frame_callback' => array( $this,'imeiTable' )
				));

				$this->addColumn("created_at", array(
				"header" => Mage::helper("orderreturn")->__("Requested Date"),
				"index" => "created_at",
				'align' =>'left',
        		'width' => '200px',
				'type' => 'datetime',
        		'filter_index' => 'created_at',
        		'frame_callback' => array( $this,'styleDate' )
				));

				$this->addColumn("updated_at", array(
				"header" => Mage::helper("orderreturn")->__("Updated Date"),
				"index" => "updated_at",
				'align' =>'left',
        		'width' => '200px',
				'type' => 'datetime',
        		'filter_index' => 'updated_at',
        		'frame_callback' => array( $this,'styleDate' )
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
			$this->getMassactionBlock()->addItem('remove_return', array(
					 'label'=> Mage::helper('orderreturn')->__('Remove Return'),
					 'url'  => $this->getUrl('*/adminhtml_return/massRemove'),
					 'confirm' => Mage::helper('orderreturn')->__('Are you sure?')
				));
			return $this;
		}
		
		public function styleDate( $value,$row,$column,$isExport )
		{
		  $locale = Mage::app()->getLocale();
		  if(!empty($value)){
			  $date = $locale->date( $value, $locale->getDateFormat(), $locale->getLocaleCode(), false )->toString( $locale->getDateFormat() ) ;
		  }
		  return $date;
		}	

		public function imeiTable($value){
			$imei = explode(",",$value);

			$str = '<table>';
			foreach ($imei as $key => $im) {
				# code...
				$str .= '<tr><td>'.$im.'</td></tr>';
			}
			$str .='</table>';
			return $str;
		}

}