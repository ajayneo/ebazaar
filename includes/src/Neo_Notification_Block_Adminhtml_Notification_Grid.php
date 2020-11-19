<?php
/**
 * Neo_Notification extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Neo
 * @package        Neo_Notification
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Notification admin grid block
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Block_Adminhtml_Notification_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('notificationGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Neo_Notification_Block_Adminhtml_Notification_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('neo_notification/notification')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Neo_Notification_Block_Adminhtml_Notification_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('neo_notification')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'    => Mage::helper('neo_notification')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('neo_notification')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '4' => Mage::helper('neo_notification')->__('Notification Sent'),
                    '3' => Mage::helper('neo_notification')->__('Add Notification to Cron'),
                    '2' => Mage::helper('neo_notification')->__('Send Notification Immediately'),
                    '1' => Mage::helper('neo_notification')->__('Enabled'),
                    '0' => Mage::helper('neo_notification')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'product_id',
            array(
                'header' => Mage::helper('neo_notification')->__('Product'),
                'index'  => 'product_id',
                'type'  => 'options',
                'options' => Mage::helper('neo_notification')->convertOptions(
                    Mage::getModel('neo_notification/notification_attribute_source_productid')->getAllOptions(false)
                )

            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('neo_notification')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('neo_notification')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        /*$this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('neo_notification')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('neo_notification')->__('Edit'),
                        'url'     => array('base'=> '//edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );*/
        $this->addExportType('*/*/exportCsv', Mage::helper('neo_notification')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('neo_notification')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('neo_notification')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Neo_Notification_Block_Adminhtml_Notification_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('notification');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('neo_notification')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('neo_notification')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('neo_notification')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('neo_notification')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('neo_notification')->__('Enabled'),
                            '0' => Mage::helper('neo_notification')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'product_id',
            array(
                'label'      => Mage::helper('neo_notification')->__('Change Product'),
                'url'        => $this->getUrl('*/*/massProductId', array('_current'=>true)),
                'additional' => array(
                    'flag_product_id' => array(
                        'name'   => 'flag_product_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('neo_notification')->__('Product'),
                        'values' => Mage::getModel('neo_notification/notification_attribute_source_productid')
                            ->getAllOptions(true),

                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Neo_Notification_Model_Notification
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return Neo_Notification_Block_Adminhtml_Notification_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
