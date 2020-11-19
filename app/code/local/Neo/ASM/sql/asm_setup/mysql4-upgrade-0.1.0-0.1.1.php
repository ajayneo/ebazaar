<?php
/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$installer->startSetup();

// $installer->addAttribute('order', 'asm_armassisting', array(
//     'label' => 'ARM Assisting',
//     'type' => 'varchar',
//     'length'=> 10,
//     'input' => 'text',
//     'required' => false,
// ));

$installer->addAttribute('order', 'assisted_by_arm', array(
    'label' => 'Assisted By ARM',
    'type' => 'int',
    'source' => 'eav/entity_attribute_source_boolean',
    'input' => 'select',
    'required' => false,
));

$installer->endSetup();