<?php
require_once('app/Mage.php');
Mage::app('default');

$installer = new Mage_Customer_Model_Entity_Setup('core_setup');
$installer->startSetup();
$entityTypeId     = (int)$installer->getEntityTypeId('customer');
$attributeSetId   = (int)$installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = (int)$installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->removeAttribute($entityTypeId, 'nav_map_status');
$installer->addAttribute('customer', 'nav_map_status', array(
    'type'               => 'int',
    'label'              => 'Nav Mapped Status',
    'input'              => 'select',
    'forms'              => array('customer_account_edit','customer_account_create','adminhtml_customer'),
   // 'source'             => 'eav/entity_attribute_source_table',
    'required'           => false,
    'visible'            => 1,
    'user_defined'       => false, /* To display in frontend */
    'position'           => 110,
    'source'     => 'eav/entity_attribute_source_boolean',
    'option'             => array('values' => array('1'=>'Yes','0'=>'No')),
    'value'            => '0',
    'default' => '0'
));

$installer->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'nav_map_status', 200);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'nav_map_status');
$oAttribute->setData('used_in_forms', array('customer_account_edit','customer_account_create','adminhtml_customer'));
$oAttribute->save();

$installer->endSetup();