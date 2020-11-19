<?php
class Neo_Catalog_Model_Observer{

    public function catalog_product_import_finish($observer){
        $adapter = $observer->getEvent()->getAdapter();
        $affectedEntityIds = $adapter->getAffectedEntityIds();
        foreach($affectedEntityIds as $affectedEntityId){
            $_product = Mage::getModel('catalog/product')->load($affectedEntityId);
            $concated_columns = $_product->getConcatedDescription();
            $concated_columns_array = explode('/', $concated_columns);
            $final_val = "<ul>";
            $counter = 0;
            foreach($concated_columns_array as $concated_columns_array_values){
                if(!empty($concated_columns_array_values)){
                    $final_val .= "<li>".$concated_columns_array_values."</li>";
                    $counter++;
                }
                if($counter == 5){
                    break;
                }
            }
            $final_val .= "</ul>";
            $_product->setImpDesc($final_val);
            $_product->setDescription($final_val);
            $_product->setShortDescription($final_val);
            $_product->setDealsDesc($final_val);
            $_product->save();
        }
    }
}
?>