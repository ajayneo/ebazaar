<?php

class Neo_Ticker_Block_Adminhtml_Ticker_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('tickerGrid');
      $this->setDefaultSort('ticker_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('ticker/ticker')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('ticker_id', array(
          'header'    => Mage::helper('ticker')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'ticker_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('ticker')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('category', array(
          'header'    => Mage::helper('ticker')->__('Category'),
          'align'     =>'left',
          'index'     => 'category',
          'width'     => '150px',
      ));

      $this->addColumn('content', array(
			'header'    => Mage::helper('ticker')->__('Item Content'),
			'width'     => '350px',
			'index'     => 'content',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('ticker')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('ticker')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('ticker')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('ticker')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('ticker')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ticker_id');
        $this->getMassactionBlock()->setFormFieldName('ticker');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('ticker')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('ticker')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('ticker/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('ticker')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('ticker')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}