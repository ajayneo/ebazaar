<?php
	class Neo_Adminhtml_Block_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
	{
		protected function _prepareCollection()
	    {
	        $collection = Mage::getResourceModel('customer/customer_collection')
	            ->addNameToSelect()
	            ->addAttributeToSelect('email')
	            ->addAttributeToSelect('created_at')
	            ->addAttributeToSelect('group_id')
	            ->addAttributeToSelect('cus_city')
	            ->addAttributeToSelect('landmark')
	            ->addAttributeToSelect('asm_map')
	            ->addAttributeToSelect('affiliate_store')
	            ->addAttributeToSelect('cus_country')
	            ->addAttributeToSelect('cus_state')
	            ->addAttributeToSelect('mobile')
	            ->addAttributeToSelect('repcode')
	            ->addAttributeToSelect('pincode')
	            ->addAttributeToSelect('nav_map_status')
	            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
	            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
	            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
	            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
	            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
	
	        $this->setCollection($collection);			return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	        //return parent::_prepareCollection();
	    }

		protected function _prepareColumns()
	    {
	        $data_array = $asm_reffred = array(); 
			$statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter('IN')->load()->toOptionArray();
	        foreach($statearray as $states){
				$data_array[$states['value']] = $states['label'];
			}

			$asm_reffred = Mage::getModel('neoaffiliate/eav_entity_attribute_Source_affiliatescontacts')->getOptionArray();

   	        $this->addColumn('entity_id', array(
	            'header'    => Mage::helper('customer')->__('ID'),
	            'width'     => '50px',
	            'index'     => 'entity_id',
	            'type'  => 'number',
	        ));
	        $this->addColumn('name', array(
	            'header'    => Mage::helper('customer')->__('Name'),
	            'index'     => 'name'
	        ));
	        $this->addColumn('email', array(
	            'header'    => Mage::helper('customer')->__('Email'),
	            'width'     => '150',
	            'index'     => 'email'
	        ));


	        $this->addColumn('repcode', array(
	            'header'    => Mage::helper('customer')->__('Repcode'),
	            'index'     => 'repcode'
	        ));
	
	        $this->addColumn('customer_since', array(
	            'header'    => Mage::helper('customer')->__('Customer Since'),
	            'type'      => 'datetime',
	            'align'     => 'center',
	            'index'     => 'created_at',
	            'gmtoffset' => true
	        ));

			$this->addColumn('updatedon', array(
	            'header'    => Mage::helper('customer')->__('Updated On'),
	            'type'      => 'datetime',
	            'align'     => 'center',
	            'index'     => 'updated_at',
	            'gmtoffset' => true
	        ));
	
	        $groups = Mage::getResourceModel('customer/group_collection')
	            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
	            ->load()
	            ->toOptionHash();
	
	        $this->addColumn('group', array(
	            'header'    =>  Mage::helper('customer')->__('Group'),
	            'width'     =>  '100',
	            'index'     =>  'group_id',
	            'type'      =>  'options',
	            'options'   =>  $groups,
	        ));
	
	        // $this->addColumn('Telephone', array(
	        //     'header'    => Mage::helper('customer')->__('Telephone'),
	        //     'width'     => '100',
	        //     'index'     => 'billing_telephone'
	        // ));

	        $this->addColumn('mobile', array(
	            'header'    => Mage::helper('customer')->__('Mobile'),
	            'width'     => '100',
	            'index'     => 'mobile'
	        ));

	        $this->addColumn('asm_map', array(
	            'header'    => Mage::helper('customer')->__('Reffered By ASM'),
	            'width'     => '100',
	            'type'   => 'options',
	            'index'     => 'asm_map',
	            'options' => $asm_reffred,
	        ));

	        $this->addColumn('affiliate_store', array(
	            'header'    => Mage::helper('customer')->__('Affiliate Store Name'),
	            'width'     => '100',
	            'index'     => 'affiliate_store'
	        ));

	        $this->addColumn('gstin', array(
	            'header'    => Mage::helper('customer')->__('GSTIN'),
	            'width'     => '100',
	            'index'     => 'gstin'
	        ));
			

	        $this->addColumn('pincode', array(
	            'header'    => Mage::helper('customer')->__('PINCODE'),
	            'width'     => '90',
	            'index'     => 'pincode',
	        ));

	        $this->addColumn('cus_city', array(
	            'header'    => Mage::helper('customer')->__('City'),
	            'width'     => '100',
	            'index'     => 'cus_city'
	        ));
	
	        // $this->addColumn('cus_country', array(
	        //     'header'    => Mage::helper('customer')->__('Country'),
	        //     'width'     => '100',
	        //     'index'     => 'cus_country'
	        // ));

	        // $this->addColumn('landmark', array(
	        //     'header'    => Mage::helper('customer')->__('Address'),
	        //     'width'     => '100',
	        //     'index'     => 'landmark'
	        // ));
	
	        $this->addColumn('cus_state', array(
	            'header'    => Mage::helper('customer')->__('State'),
	            'width'     => '100',
	            'type'   => 'options',
	            'index'     => 'cus_state',
	            'options' => $data_array,
	        ));

	        // $this->addColumn('billing_postcode', array(
	        //     'header'    => Mage::helper('customer')->__('ZIP'),
	        //     'width'     => '90',
	        //     'index'     => 'billing_postcode',
	        // ));

	
	        // $this->addColumn('billing_country_id', array(
	        //     'header'    => Mage::helper('customer')->__('Billing Country'),
	        //     'width'     => '100',
	        //     'type'      => 'country',
	        //     'index'     => 'billing_country_id',
	        // ));
	
	        // $this->addColumn('billing_region', array(
	        //     'header'    => Mage::helper('customer')->__('Billing State/Province'),
	        //     'width'     => '100',
	        //     'index'     => 'billing_region',
	        // ));

	
	        if (!Mage::app()->isSingleStoreMode()) {
	            $this->addColumn('website_id', array(
	                'header'    => Mage::helper('customer')->__('Website'),
	                'align'     => 'center',
	                'width'     => '80px',
	                'type'      => 'options',
	                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
	                'index'     => 'website_id',
	            ));
	        }

	        $this->addColumn('nav_map_status',
	            array(
	                'header'=> Mage::helper('catalog')->__('Nav Mapped Status'),
	                'width' => '70px',
	                'index' => 'nav_map_status',
	                'type'  => 'options',
	                'options' => array('1'=>'Yes','0'=>'No'),
	        ));
	
	        $this->addColumn('action',
	            array(
	                'header'    =>  Mage::helper('customer')->__('Action'),
	                'width'     => '100',
	                'type'      => 'action',
	                'getter'    => 'getId',
	                'actions'   => array(
	                    array(
	                        'caption'   => Mage::helper('customer')->__('Edit'),
	                        'url'       => array('base'=> '*/*/edit'),
	                        'field'     => 'id'
	                    )
	                ),
	                'filter'    => false,
	                'sortable'  => false,
	                'index'     => 'stores',
	                'is_system' => true,
	        ));
	
	        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
	        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
	        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	        //return parent::_prepareColumns();
	    }
	}
?>