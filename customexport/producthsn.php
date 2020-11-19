<?php
require_once('../app/Mage.php');
umask(0);
Mage::app();
$_productModel = Mage::getModel('Catalog/Product');
$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection') ->load();
$hsncodes = array();
$hsnCollection = Mage::helper('indiagst')->getHsnCodes();
foreach ($attributeSetCollection as $id=>$attributeSet) {
    $name = $attributeSet->getAttributeSetName();

    // echo $name.'<br/>';
    if($hsnCollection[$name] !== ''){
        $hsncodes[$id]['hsncode'] = $hsnCollection[$name];
        $hsncodes[$id]['name'] = $name;
    }
}
$collection = $_productModel->getCollection();
Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
$filename = "product_hsn_codes.csv";
$file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;

$filepath = fopen($file,"w");
$header = array('ITEM CODE','HSN CODE','PRODUCT CATEGORY'); 
fputcsv($filepath, $header);
foreach ($collection as $product) {
    # code...
    $_productModel->load($product->getEntityId());
    $_productModel->getSku().','.$product->getAttributeSetId();

    if(!empty($hsncodes[$product->getAttributeSetId()]['hsncode'])){
        fputcsv($filepath, array($_productModel->getSku(),$hsncodes[$product->getAttributeSetId()]['hsncode'],$hsncodes[$product->getAttributeSetId()]['name']));
    }
}
fclose($filepath);
echo $file;
exit();