<?php
/* ********************** EB Website(17-Feb-2018) *****************
************************* Code for IMEI update ********************
************************* Code by Jitendra Patel ****************** */
ini_set('memory_limit','-1');
set_time_limit(0);
require_once('../app/Mage.php');
error_reporting(E_ALL & ~E_NOTICE);
umask(0);
Mage::app();
echo "Check JP Order return";
$orderid = '200093014';//'200094165';//'200094165';
if($orderid == '200093014')
{
  $result = Mage::helper('orderreturn')->canReturnjp($orderid);
  echo '<pre>', print_r($result); 
  echo "End";

}

/* ********************** EB Website(17-Feb-2018) *****************
************************* Code for IMEI update ********************
************************* Code by Jitendra Patel ****************** */