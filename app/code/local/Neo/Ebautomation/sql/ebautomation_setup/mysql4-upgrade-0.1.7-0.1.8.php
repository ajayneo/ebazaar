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
$model->removeAttribute('catalog_product','mapped_details');
$model->removeAttribute('catalog_product','mapped_status');
 
// adding attribute group
 
// the attribute added will be displayed under the group/tab Special Attributes in product edit page
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'mapped_status', array(
    'type'       => 'int',
    'input'      => 'select',
    'label'      => 'Navision Mapped',
    'sort_order' => 1000,
    'required'   => false,
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    //'backend'    => 'eav/entity_attribute_backend_array',
    'frontend_class' => 'disabled',
    'source'     => 'eav/entity_attribute_source_boolean',
    'option'             => array('values' => array('1'=>'Yes','0'=>'No')),
    'value'            => '0',
    'default'   =>'0'

));
$setup->addAttribute('catalog_product', 'mapped_details', array(
    'group'         => 'General',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Mapped Details',
    'backend'       => '',
    'default'       => '',
    'visible'       => 1,
    'required'        => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 1,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'frontend_class' => 'disabled',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


$groupName = 'Automation Information';
$entityTypeId = $setup->getEntityTypeId('catalog_product');
$attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);


// Add existing attribute to group

$collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
->setEntityTypeFilter($entityTypeId);

foreach ($collection as $attributeSet) {
    $attributeSetId = $attributeSet->getId();
   /* $attributeGroupId = $installer->getDefaultAttributeGroupId('catalog_product',     $attributeSet->getId());
    $installer->addAttributeToSet('catalog_product', $attributeSet->getId(), $attributeGroupId, $attributeId);*/

    $setup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 100);
    $attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

    $attributeId = $setup->getAttributeId($entityTypeId, 'mapped_status');
    $setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, $attributeId, null);
     
    $attributeId = $setup->getAttributeId($entityTypeId, 'mapped_details');
    $setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, $attributeId, null);

}




$setup->endSetup();