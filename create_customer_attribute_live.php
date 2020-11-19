<?php
require_once('app/Mage.php');
Mage::app('default');

$installer = new Mage_Customer_Model_Entity_Setup('core_setup');
$installer->startSetup();
$entityTypeId     = (int)$installer->getEntityTypeId('customer');
$attributeSetId   = (int)$installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = (int)$installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('customer', 'zone', array(
    'type'               => 'text',
    'label'              => 'Zone',
    'input'              => 'select',
    'forms'              => array('customer_account_edit','customer_account_create','adminhtml_customer','checkout_register'),
    'source'             => 'eav/entity_attribute_source_table',
    'required'           => false,
    'visible'            => 1,
    'user_defined'       => true, /* To display in frontend */
    'position'           => 110,
    'option'             => array('values' => array('East', 'West', 'North', 'South', 'Central', 'Mysuru'))
));

$installer->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'zone', 100);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'zone');
$oAttribute->setData('used_in_forms', array('customer_account_edit','customer_account_create','adminhtml_customer','checkout_register'));
$oAttribute->save();

$installer->endSetup();
?>