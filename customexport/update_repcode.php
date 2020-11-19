<?php require_once('../app/Mage.php');
umask(0);
Mage::app();

try{

    $_collection = Mage::getResourceModel('customer/customer_collection');
    $_collection->addFieldToFilter('repcode', array('null' => true));
    // echo $_collection->getSelect();
    $_customer = Mage::getModel('customer/customer');
    foreach ($_collection as $customer){
        $customer_id =  $customer->getEntityId();
        $_customer->load($customer_id);
        $firstname = $_customer->getFirstname();


        for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {  
            $randomNumber .= mt_rand(0, 9); 
        }
        $name = explode(' ', $firstname);
        $repcode = 'Aff_'.$name[0].'_'.$randomNumber;

        Mage::log("Saving email = ".$_customer->getEmail()."customer id = ".$customer_id." repcode = ".$repcode,null,'repcode.log',true);
        $_customer->setRepcode($repcode);
        $_customer->save();
        // exit;
    }

}catch(Exception $e){
    echo $e->getMessage();
}