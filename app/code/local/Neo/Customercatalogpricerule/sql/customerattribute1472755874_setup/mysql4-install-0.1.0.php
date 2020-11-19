<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute('catalog_product', 'product_type', array(
        'group'             => 'Catalog Price Rule',
        'label'             => 'Select Customer For Price Rule',
        'note'              => '',
        'type'              => 'int',   //backend_type
        'input'             => 'multiselect',    //frontend_input
        'frontend_class'    => '',
        'source'            => 'customercatalogpricerule/attribute_source_type',
        'backend'           => '',//imp eav/entity_attribute_backend_array
        'frontend'          => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'required'          => false,
        'visible_on_front'  => false,
        'apply_to'          => 'simple',
        'is_configurable'   => false,
        'used_in_product_listing'   => false, 
        'sort_order'        => 5,
    ));
 
$installer->endSetup(); 
     