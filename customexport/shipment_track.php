<?php //Mahesh gurav revised on 16th Mar 2018
require_once('/home/electronicsbazaa/public_html/app/Mage.php');
umask(0);
Mage::app();
//tracking shipments ecom and bluedard within orders last 15 days
//update delivered status
//send out for delivery email and sms
Mage::getModel('orderreturn/trackstatus')->init();
exit();
