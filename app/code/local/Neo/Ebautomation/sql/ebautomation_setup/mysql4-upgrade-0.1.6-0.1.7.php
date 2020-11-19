<?php
require_once('app/Mage.php');
Mage::app('default');

$installer = new Mage_Customer_Model_Entity_Setup('core_setup');
$installer->startSetup();
$entityTypeId     = (int)$installer->getEntityTypeId('customer');
$attributeSetId   = (int)$installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = (int)$installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->removeAttribute($entityTypeId, 'nav_map_details');
$installer->addAttribute('customer', 'nav_map_details', array(
    'type'               => 'text',
    'label'              => 'Nav Mapped Details',
    'input'              => 'textarea',
    'forms'              => array('customer_account_edit','customer_account_create','adminhtml_customer'),
    'required'           => false,
    'visible'            => 1,
    'user_defined'       => false, /* To display in frontend */
    'position'           => 120
    
));

$installer->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'nav_map_details', 200);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'nav_map_details');
$oAttribute->setData('used_in_forms', array('customer_account_edit','customer_account_create','adminhtml_customer'));
$oAttribute->save();

$installer->endSetup();