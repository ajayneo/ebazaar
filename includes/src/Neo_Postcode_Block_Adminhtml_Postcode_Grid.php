<?php
class Neo_Postcode_Block_Adminhtml_Postcode_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('postcodeGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('postcode/postcode')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('entity_id', array(
			'header' => 'Id',
			'index'  => 'entity_id',
			'type'   => 'number'
		));
		$this->addColumn('area', array(
			'header' => 'Area Name',
			'index'  => 'area'
		));
		$this->addColumn('city', array(
			'header' => 'City',
			'index'  => 'city'
		));
		$this->addColumn('state', array(
			'header' => 'State',
			'index'  => 'state'
		));
		$this->addColumn('postcode', array(
			'header' => 'Postcode',
			'index'  => 'postcode',
			'type'	 => 'number'
		));
		$this->addColumn('status', array(
            'header'    => Mage::helper('postcode')->__('Status'),
            'index'        => 'status',
            'type'        => 'options',
            'options'    => array(
                '1' => Mage::helper('postcode')->__('Enabled'),
                '0' => Mage::helper('postcode')->__('Disabled'),
            )
        ));
	}
	
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
	public function getGridUrl() {
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}
?>
