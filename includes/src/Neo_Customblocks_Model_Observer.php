<?php
class Neo_Customblock_Model_Observer{
    
    public function load_outofstock_later($observer){
        echo 'exit'; exit;
        //catalog_product_collection_load_before    
    }
    
    public function export_new_order($observer){
        echo 'exit'; exit;
    $str = 'TESTME|UATTXN0001|NA|10|HDF|NA|NA|INR|NA|R|NA|NA|NA|F|Andheri|Mumbai|02240920005|support@billdesk.com|NA|NA|NA|https://www.billdesk.com';

    $checksum = hash_hmac('sha256',$str,'ABCDEF1234567890', false); 
    $checksum = strtoupper($checksum);
    
    $dataWithCheckSumValue = $str."|".$checksum;
 
    $msg = $dataWithCheckSumValue;
    
    Mage::getSingleton('core/session')->setMyValue($msg);
    
    $getSession =Mage::getSingleton('core/session')->getMyValue();
    
    Mage::log($getSession,null,'session.log');
    
    }
}
?>