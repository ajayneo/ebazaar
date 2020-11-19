<?php
class Neo_Notification_Model_Observer
{
    public function trigger_notification_cron_eb()
    {
      // do something
        /*Mage::log(now().' Cron executed /n', null, 'deepak.log');
        $notifiction_collection = Mage::getModel('neo_notification/notification')->getCollection();
        $notifiction_collection->addFieldToFilter('status', array('eq' => 3));
        $notification_array = $notifiction_collection->getData();
        $notification_array_check_empty = array_filter($notification_array);
        //Mage::log(now().print_r($notification_array_check_empty).' /n', null, 'deepak.log');
        if (!empty($notification_array_check_empty)) {
            foreach($notification_array as $key => $value){
                //Mage::log(now().'<pre>'.print_r($value).'</pre> test /n', null, 'deepak.log');
                Mage::helper('neo_notification')->divideRequestWithinAndroidAndIphone($value);
                // after sending the notification change the status of notification to notification sent
                $notifiction_model_eb = Mage::getModel('neo_notification/notification')->load($value["entity_id"]);
                $notifiction_model_eb->setStatus(4)->save();
            }
        } else {
            //Mage::log(now().'<pre>'.print_r($notifiction_collection->getData()).'</pre> test /n', null, 'deepak.log');
            //Mage::log(now().'empty test /n', null, 'deepak.log');
        }*/


    }
}

?>