<?php
require_once('app/Mage.php');
Mage::app('default');

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
/**
 * Adding Different Attributes
 */



$model = Mage::getResourceModel('catalog/setup','catalog_setup');
$model->removeAttribute('catalog_product','qty_update_flag');
$model->removeAttribute('catalog_product','qty_update_reason');
$model->removeAttribute('catalog_product','product_category_type');

 
// adding attribute group
 
// the attribute added will be displayed under the group/tab Special Attributes in product edit page
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'qty_update_flag', array(
    'type'       => 'int',
    'input'      => 'select',
    'label'      => 'Qty Update Flag',
    'sort_order' => 1000,
    'required'   => false,
    'visible'       => 0,
    'visible_on_front' => 0,
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    //'backend'    => 'eav/entity_attribute_backend_array',
    'frontend_class' => 'disabled',
    'source'     => 'eav/entity_attribute_source_boolean',
    'option'             => array('values' => array('1'=>'Yes','0'=>'No')),
    'value'            => '0',
    'default'   =>'0'

));
$setup->addAttribute('catalog_product', 'qty_update_reason', array(
    'group'         => 'General',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Qty Update Reason',
    'backend'       => '',
    'default'       => '',
    'visible'       => 0,
    'required'        => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 1,
    'visible_on_front' => 0,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'product_category_type', array(
    'type'       => 'int',
    'group'         => 'General',
    'input'      => 'select',
    'label'      => 'Product Category Type',
    //'sort_order' => 700,
    'required'   => 1,
    'user_defined' => 1,
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'source'     => 'eav/entity_attribute_source_table',
    

));

$setup->endSetup();