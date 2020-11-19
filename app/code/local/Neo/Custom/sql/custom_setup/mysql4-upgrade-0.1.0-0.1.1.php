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
$model->removeAttribute('catalog_product','new_launch');
 
// adding attribute group
 
// the attribute added will be displayed under the group/tab Special Attributes in product edit page

$data=array(
'type'=>'int',
'input'=>'boolean', //for Yes/No dropdown
'sort_order'=>50,
'label'=>'New Launch',
'global'=>Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
'required'=>'0',
'comparable'=>'0',
'searchable'=>'0',
'is_configurable'=>'1',
'user_defined'=>'1',

'required'=> 0,
//'apply_to' => 'configurable', //simple,configurable,bundled,grouped,virtual,downloadable
//'is_configurable' => false
);

$model->addAttribute('catalog_product','new_launch',$data);


$groupName = 'General';
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

    $attributeId = $setup->getAttributeId($entityTypeId, 'new_launch');
    $setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, $attributeId, null);
}




$setup->endSetup();