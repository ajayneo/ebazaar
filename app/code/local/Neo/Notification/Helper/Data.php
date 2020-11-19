<?php
/**
 * Neo_Notification extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Neo
 * @package        Neo_Notification
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Notification default helper
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }
    /*
    * @desc redesign this function for using FCM
    * @author Mahesh
    * @date 5th March 2018
    */
    public function divideRequestWithinAndroidAndIphone($form_data)
    { 
        ini_set('max_execution_time','0'); 
        Mage::log('Start Notification',null,'notification.log',true);
        $product_id = $form_data['product_id']; 
        $status = $form_data['status'];
        $productModel = Mage::getModel('catalog/product')->load($product_id);

        /*$productData = array('title' => $productModel->getName(),
            'price' => $productModel->getPrice(),
            'special_price' => $productModel->getSpecialPrice(),
            'form_data' => $form_data);*/
            
        $productData = array('title' => $productModel->getName(),
            'product_id' => $product_id);
        if($form_data['notfication_type'] == 1 ){
            if($form_data['image_link_type'] == 1 ){
                $_category = Mage::getModel('catalog/category')->load($form_data['category_id']);
                $notification_data = array(
                    'title' => $form_data['title'],
                    'type' => 'text',
                    'url_type' => 'category',
                    'category_id' => $form_data['category_id'],
                    'category_name' => $_category->getName(),
                );
            } elseif ($form_data['image_link_type'] == 2) {
                $notification_data = array(
                'title' => $form_data['title'],
                'type' => 'text',
                'url_type' => 'product',
                'product_id' => $form_data['product_id']);
            }

        } elseif ($form_data['notfication_type'] == 2) {
            if($form_data['image_link_type'] == 1 ){
                $_category = Mage::getModel('catalog/category')->load($form_data['category_id']);
                $notification_data = array(
                    'title' => $form_data['title'],
                    'type' => 'image',
                    // 'image_url' => $form_data['image_name'],
                    // 'image' => 'https://api.androidhive.info/images/minion.jpg',
                    'image' => Mage::getBaseUrl('media').$form_data['image_name'],
                    'url_type' => 'category',
                    'category_id' => $form_data['category_id'],
                    'category_name' => $_category->getName(),
                    'color' => 'transparent',
                );
            } elseif ($form_data['image_link_type'] == 2) {
                $notification_data = array(
                'title' => $form_data['title'],
                'type' => 'text',
                'product_id' => $form_data['product_id']); 
                $notification_data = array(
                    'title' => $form_data['title'],
                    'type' => 'image',
                    // 'image_url' => $form_data['image_name'],
                    'image' => Mage::getBaseUrl('media').$form_data['image_name'],
                    'url_type' => 'product',
                    'product_id' => $form_data['product_id']
                );
            }
        } elseif ($form_data['notfication_type'] == 3 || $form_data['notfication_type'] == 5) {
           $notification_data = array(
                'title' => $form_data['title'],
                'type' => 'link',
                'link_url' => $form_data['link_url']
                //'link_url' => 'deals' 
            );
        } elseif ($form_data['notfication_type'] == 4) {
           $notification_data = array(
                'title' => $form_data['title'],
                'type' => 'app_update',
                'link_url' => $form_data['link_url']
            );
        } else {
            $notification_data = array();
        }

        // $fcmDeviceIds = array();
        //FCM upgradation code adding to test condition in status 5
        $devices = array();
        $message = '';
        if($form_data['status'] == 5){
            
            //view new device table
            $fcmIphoneIds = Mage::getModel('neo_notification/pushnotificationtest')
                                ->getCollection()->addFieldToSelect('device_Id')                                
                                ->addFieldToFilter('status',1)
                                ->addFieldToFilter('device_type','iphone')
                                ->getColumnValues('device_Id');
                                // echo $fcmIphoneIds->getSelect();
            $devices['iphone'] = $fcmIphoneIds;

            $fcmAndroidIds = Mage::getModel('neo_notification/pushnotificationtest')
                                ->getCollection()->addFieldToSelect('device_Id')                                
                                ->addFieldToFilter('status',1)
                                ->addFieldToFilter('device_type','android')
                                ->getColumnValues('device_Id');
            $devices['android'] = $fcmAndroidIds;                    
            // print_r($devices); exit;
            $fcm_test_success = 0;
            $fcm_test_failure = 0;
            $fcm_test_total = 0;
            foreach ($devices as $key => $fcmDeviceIds) {
                $fcm_test_response = Mage::helper('neo_notification')->triggerFCMNotification($fcmDeviceIds, $productData, $notification_data, $key);
                $fcm_test_success =  $fcm_test_response['success'];
                $fcm_test_failure =  $fcm_test_response['failure'];
                $fcm_test_total   =  $fcm_test_success + $fcm_test_failure;
                $message .= "Test $key users notification sent : $fcm_test_total Total no of success : $fcm_test_success Total no of failed : $fcm_test_failure <br/>";

                $device_results = $fcm_test_response['results'];
                
                $failed_devices = array();
                foreach ($device_results as $key => $value) {
                    if(isset($value['error'])){
                        $failed_devices[] = $fcmDeviceIds[$key];
                    }
                }

                $new_devices = Mage::getResourceModel('neo_notification/pushnotificationtest_collection')
                ->addFieldToFilter('device_Id',array('in'=>$failed_devices));
                foreach ($new_devices as $device) {
                    $device->setStatus(2);
                    $device->save();
                }
            }
        }else{
            #--------------------------all fcm tables------------------------------------------------------------
            //send new device notifications
            $fcm_devices = array();
            $non_user_android_devices = Mage::getModel('neo_notification/fcmdevice')
                            ->getCollection()->addFieldToSelect('device_Id')                                
                            ->addFieldToFilter('status',1)
                            ->addFieldToFilter('device_type','android')
                            ->getColumnValues('device_Id');

            $live_users_android_devices = Mage::getModel('neo_notification/fcmpush')
                            ->getCollection()->addFieldToSelect('device_Id')                                
                            ->addFieldToFilter('status',1)
                            ->addFieldToFilter('device_type','android')
                            ->getColumnValues('device_Id');

            $total_live_android = array_merge($non_user_android_devices,$live_users_android_devices);

            $non_user_iphone_devices = Mage::getModel('neo_notification/fcmdevice')
                            ->getCollection()->addFieldToSelect('device_Id')                                
                            ->addFieldToFilter('status',1)
                            ->addFieldToFilter('device_type','iphone')
                            ->getColumnValues('device_Id');

            $live_users_iphone_devices = Mage::getModel('neo_notification/fcmpush')
                            ->getCollection()->addFieldToSelect('device_Id')                                
                            ->addFieldToFilter('status',1)
                            ->addFieldToFilter('device_type','iphone')
                            ->getColumnValues('device_Id');

            $total_live_iphone = array_merge($non_user_iphone_devices,$live_users_iphone_devices);


            $fcm_devices['android'] = array_unique($total_live_android);
            $fcm_devices['iphone'] = array_unique($total_live_iphone);

            foreach ($fcm_devices as $ios => $device_ids) {
                $fcm_response = Mage::helper('neo_notification')->triggerFCMNotification($device_ids, $productData, $notification_data, $ios);
                $fcm_success =  $fcm_response['success'];
                $fcm_failure =  $fcm_response['failure'];
                $fcm_total   =  $fcm_success + $fcm_failure;
                $message .= "Live FCM total $ios notification sent : $fcm_total Total no of success : $fcm_success Total no of failed : $fcm_failure <br/>";

                $device_results = $fcm_response['results'];
                
                $failed_devices = array();
                foreach ($device_results as $key => $value) {
                    if(isset($value['error'])){
                        $failed_devices[] = $device_ids[$key];
                    }
                }

                $new_devices = Mage::getResourceModel('neo_notification/fcmdevice_collection')
                ->addFieldToFilter('device_Id',array('in'=>$failed_devices));
                foreach ($new_devices as $device) {
                    $device->setStatus(2);
                    $device->save();
                }
            }
            


            #-------------- send notification to older devices--------------------------------------------------------

            $andoridDataArray1 = Mage::getModel('neo_notification/pushnotification')
                                ->getCollection()->addFieldToSelect('device_Id')                                
                                ->addFieldToFilter('device_type','android')
                                ->addFieldToFilter('status',1)
                                ->getColumnValues('device_Id');

            $andoridDataArray1 = array_unique($andoridDataArray1);
            
            $andoridDataArray2 = Mage::getModel('neo_notification/device')
                                ->getCollection()->addFieldToSelect('device_Id')                                
                                ->addFieldToFilter('device_type','android')
                                // ->addFieldToFilter('status',1)
                                ->getColumnValues('device_Id');
            $andoridDataArray2 = array_unique($andoridDataArray2);

            $andoridDataArray = array_merge($andoridDataArray1,$andoridDataArray2);

            // $andoridDataArray = array();

            $iphoneDataArray1 = Mage::getModel('neo_notification/pushnotification')
                                ->getCollection()->addFieldToSelect('device_Id') 
                                ->addFieldToFilter('device_type','iphone')
                                ->addFieldToFilter('status',1)
                                ->getColumnValues('device_Id');    
            $iphoneDataArray1 = array_unique($iphoneDataArray1);

            $iphoneDataArray2 = Mage::getModel('neo_notification/device')
                                ->getCollection()->addFieldToSelect('device_Id') 
                                ->addFieldToFilter('device_type','iphone')
                                // ->addFieldToFilter('status',1)
                                ->getColumnValues('device_Id');
            $iphoneDataArray2 = array_unique($iphoneDataArray2);
            
            $iphoneDataArray = array_merge($iphoneDataArray1,$iphoneDataArray2);

            $andoridDataArrayChunk = array_chunk($andoridDataArray,999,false);
    
            $android_response = "";
            $iphone_response = "";
            $android_success = "0";
            $android_failure = "0";
            $android_total = "0";
            $i = 0;
            foreach ($andoridDataArrayChunk as $key => $android_ids) {
                // call android push notification api  
              $android_response = Mage::helper('neo_notification')->sendAndroidNotification($android_ids, $productData, $notification_data);

              $android_success =  $android_success + $android_response['success'];
              $android_failure =  $android_failure + $android_response['failure'];
              $android_total   =  $android_success + $android_failure;
              $message .= "Android Older version Total notification sent : $android_total Total no of success : $android_success Total no of failed : $android_failure <br/>";
                $device_results = $android_response['results'];
                $failed_devices = array();
                foreach ($device_results as $key => $param) {
                    if(isset($param['error'])){
                        $failed_devices[] = $android_ids[$key];
                    }
                }

                $new_devices = Mage::getResourceModel('neo_notification/pushnotification_collection')
                ->addFieldToFilter('device_Id',array('in'=>$failed_devices));
                foreach ($new_devices as $device) {
                    $device->setStatus(2);
                    $device->save();
                }
              $i++;          

            }
            
            $iphone_response = Mage::helper('neo_notification')->sendIphoneNotification($iphoneDataArray, $productData, $notification_data);
            if(!empty($iphone_response)){
                $message .= "iPhone Older version total no of success : $iphone_response <br/>";
            }

            #----------------- old tables end -------------------------------------------------------------------------

        }//end of live notifications
       

        Mage::getSingleton('adminhtml/session')->addSuccess($message);
        Mage::log('End Notification',null,'notification.log',true);
    } 
    /*
    * @desc trigger andorid api
    * @author deepakd
    */
    public function sendAndroidNotification($andoridDataArray, $productData, $notification_data)
    {
        ini_set('max_execution_time',0); 
        Mage::log('Start GCM cnt '.count($andoridDataArray).' IOS Android',null,'notification.log',true);
        // get the registration id listing
        $registation_ids = $andoridDataArray;
        //$message = array("product" => $productData['form_data']);

        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registation_ids,
            'data' => $notification_data
        );

        $headers = array(
            //'Authorization: key=AIzaSyBez36IxZDguxTu8dmWGJPktyhjYbRRfQw',
            'Authorization: key=AIzaSyDsncspLbVYYZf0VZDDCMm5TmK4TkCX-Qk',
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        // echo "<pre>"; print_r($result); exit;
        if ($result === FALSE) {
            Mage::getSingleton('adminhtml/session')->addWarning('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        // Mage::log(json_decode($result,true),null,'android_'.date('dmyhis').'.log',true);
        Mage::log('Complete GCM cnt '.count($andoridDataArray).' IOS Android',null,'notification.log',true);
        return json_decode($result,true);
    }
    /*
    * @desc trigger iphone api
    * @author deepakd
    */
    public function sendIphoneNotification($iphoneDataArray, $productData, $notification_data)
    {

        ini_set('max_execution_time',0);  

        Mage::log('Start GCM cnt '.count($iphoneDataArray).' IOS iPhone',null,'notification.log',true);
        // get the registration id listing
        $registatoin_ids = $iphoneDataArray;
        //$message = array("product" => $productData);
        $count_iphone_notifications = 0;  
        foreach ($registatoin_ids as $key => $deviceToken) {
            //print_r(file_get_contents('EBPushCert.pem'));
            //echo Mage::getModuleDir('Helper', 'Neo_Notification')."/Helper/EBPushCert.pem";
            /*echo file_get_contents(Mage::getModuleDir('Helper', 'Neo_Notification')."/Helper/EBPushCert.pem");
            die;*/
            set_time_limit(0);
            require_once(Mage::getModuleDir('Helper', 'Neo_Notification')."/Helper/ApnsPHP/Autoload.php");
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', Mage::getModuleDir('Helper', 'Neo_Notification')."/Helper/EB_Distribution_Push_cer.pem");
            stream_context_set_option($ctx, 'ssl', 'passphrase', '');

//ssl://gateway.push.apple.com:2195
//ssl://gateway.sandbox.push.apple.com:2195 

                $fp = stream_socket_client('ssl://gateway.push.apple.com:2195',  
                $err,
                $errstr,   
                60,  
                STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,  
                $ctx);

            if (!$fp)
            Mage::getSingleton('adminhtml/session')->addWarning("Failed to connect: $err $errstr" . PHP_EOL);
            //exit("Failed to connect: $err $errstr" . PHP_EOL);

            //echo '<br>'.date("Y-m-d H:i:s").' Connected to APNS' . PHP_EOL;
            // Create the payload body
            $body['aps'] = array(
                'badge' => +1,
                'alert' => $notification_data["title"],
                'sound' => 'default'
            );
            //$body['product_id'] = $productData["product_id"];
            $body['data'] = $notification_data;

            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            



            if (!$result) {
                //echo 'Message not delivered' . PHP_EOL;
                // Mage::log($deviceToken,null,'iphone_success_'.date('dmyhis').'.log',true);
            } else {
                // Mage::log($deviceToken,null,'iphone_failure_'.date('dmyhis').'.log',true);
                $count_iphone_notifications++;
            }
            //echo 'Message successfully delivered'.$message. PHP_EOL;
            // echo $msg; echo "<br/>";
            // Close the connection to the server
            fclose($fp);
        }
        Mage::log('Complete GCM cnt '.count($iphoneDataArray).' IOS iPhone',null,'notification.log',true);
        return $count_iphone_notifications;
    }


    //FCM Function
    //27 Feb 2018
    //Mahesh Gurav
    public function triggerFCMNotification($fcmDeviceIds, $productData, $notification_data, $check_ios){
        Mage::log('Start FCM cnt '.count($fcmDeviceIds).' IOS '.$check_ios,null,'notification.log',true);
        // API access key from Google FCM App Console
        //Server Key
        define( 'API_ACCESS_KEY', 'AAAAgfZ51Y4:APA91bEQ6qxKu906Kq6sXw06pacqdrS-t8Q_fs5mn1b91UMvsOYBjZ2oJdNmWLTrVmYf6ZvZJcWvGzzzG66SHDUk1mms-VeBeXSEmnW4jmQjHnrmLxA3SnPIpe6v7PIymthNWjd8MJw2' );

        // generated via the cordova phonegap-plugin-push using "senderID" (found in FCM App Console)
        // this was generated from my phone and outputted via a console.log() in the function that calls the plugin
        // my phone, using my FCM senderID, to generate the following registrationId 
        // $singleID = '8b66bba2d36932d930626eb2c2eb05e6a7b9c74f807af3fb0f669c372062bd01'; //emulator device id  
        $singleID = 'e0eaYkUOtnQ:APA91bGtv69FBhl0Rb2NJ5G-yvJs7d5HhqnztW1M99ZTVf3_k_2TWEuX6uPLzdyKBR0hRkwYAKn81rWZv174rW5XRDhv5TUyZV_rm2KW3QSWAitSsq6kKQpJKFGOC3m60FrO2sMvucGw'; //hemant sable

        // $singleID='fvHl32qes0Q:APA91bES7ve8yJWuCSkZ8PYzSRpO4Wu0CXhRLvnHP3F-pz-o7Ngwh2y44uL-NlxIyrAfiMy1qiYCXmR6d26i7hfhoV2lIN7ziN_r2MiP4cqLiAx14wFNUx0Jww_yVwCVuLU_CR9Ihxg1';

        $registrationIDs = $fcmDeviceIds;

        // print_r($registrationIDs); exit;
        // prep the bundle
        // to see all the options for FCM to/notification payload: 
        // https://firebase.google.com/docs/cloud-messaging/http-server-ref#notification-payload-support 

        // 'vibrate' available in GCM, but not in FCM
        $fcmMsg = array(
            'body' => 'Awesome Offers!!! Buy 2 units and above and get 3% instant discount in bill** #Buy Brand New Mobiles** Moto E4 (16 GB) @ INR 8,499/- Lenovo Vibe B (8 GB) @ INR 5,100/- #Buy Open Box Mobile with 1 Year Warranty** Moto G4 Plus (32 GB) @ INR 9,200/-',
            'title' => 'expecting a single ID',
            'type' => "link",
            'link_url' => 'http://180.149.246.49/revamp/deals/',
            'color' => "#203E78" 
        );

        // $fcmMsg = $notification_data;

        
        // I haven't figured 'color' out yet.  
        // On one phone 'color' was the background color behind the actual app icon.  (ie Samsung Galaxy S5)
        // On another phone, it was the color of the app icon. (ie: LG K20 Plush)

        // 'to' => $singleID ;  // expecting a single ID
        // 'registration_ids' => $registrationIDs ;  // expects an array of ids
        // 'priority' => 'high' ; // options are normal and high, if not set, defaults to high.

        // done statically as per Rakshita

        // $merge = array('message'=>'','timestamp'=>'');
        // $notification_data = array_merge( $notification_data, $merge );
        // echo "<pre>"; print_r($notification_data); exit;
        $fcmFields = array(
            // 'to' => $singleID,
            'registration_ids' => $registrationIDs,
            'priority' => 'high',
            // 'notification' => $fcmMsg
            // 'notification' => $notification_data,
            // 'data' => $notification_data
        );

        if($check_ios == 'android'){
          $fcmFields['data'] = $notification_data;
        }else if($check_ios == 'iphone'){
            $fcmFields['content_available'] = true;
            $fcmFields['mutable_content'] = true;
            $notification_data['body'] = $notification_data['title'];
            $notification_data['title'] = 'Electronics Bazaar';
            if($notification_data['type'] == 'image'){
                $notification_data['category'] = 'newCategory';
                $part_mages = explode(".", $notification_data['image']);
                $notification_data['image_type'] = end($part_mages);
            }
            $fcmFields['notification'] = $notification_data;
        }

        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
         
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        // echo "<pre>";
        //echo $result . "\n\n";
        Mage::log('Complete FCM cnt '.count($fcmDeviceIds).' IOS '.$check_ios,null,'notification.log',true);
        return json_decode($result,true);
    }

    /*
    * @date : 16th Mar 2018
    * @author : Jitendra Patel
    * @purpose : To send notification to retailers. 
    */
    public function sygFCMNotification($fcmDeviceId, $productData, $notification_data, $check_ios){
        // API access key from Google FCM App Console
        //Server Key
        define( 'API_ACCESS_KEY', 'AAAAgfZ51Y4:APA91bEQ6qxKu906Kq6sXw06pacqdrS-t8Q_fs5mn1b91UMvsOYBjZ2oJdNmWLTrVmYf6ZvZJcWvGzzzG66SHDUk1mms-VeBeXSEmnW4jmQjHnrmLxA3SnPIpe6v7PIymthNWjd8MJw2' );

        // 'vibrate' available in GCM, but not in FCM
        $fcmMsg = array(
            'body' => 'Awesome Offers!!! Buy 2 units and above and get 3% instant discount in bill** #Buy Brand New Mobiles** Moto E4 (16 GB) @ INR 8,499/- Lenovo Vibe B (8 GB) @ INR 5,100/- #Buy Open Box Mobile with 1 Year Warranty** Moto G4 Plus (32 GB) @ INR 9,200/-',
            'title' => 'expecting a single ID',
            'type' => "link",
            'link_url' => 'http://180.149.246.49/revamp/deals/',
            'color' => "#203E78" 
        );

        $fcmFields = array(
            'to' => $fcmDeviceId,
            'priority' => 'high',
        );

        // echo "<pre>";
        //$fcmFields['categorynew'] = 'sygNotificationCategory';
        if($check_ios == 'android'){
          $fcmFields['data'] = $notification_data;
        }else if($check_ios == 'iphone'){
            $fcmFields['content_available'] = true;
            $fcmFields['mutable_content'] = true;
            $notification_data['body'] = $notification_data['title'];
            $notification_data['title'] = 'Electronics Bazaar';
            if($notification_data['type'] == 'syg_link'){
                $notification_data['click_action'] = 'sygNotificationCategory';
            }
            $fcmFields['notification'] = $notification_data;
        }
        // print_r($notification_data);
        // echo end($part_mages);
        // exit;
        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );        
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        // echo "<pre>";
        //echo $result . "\n\n";exit;
        //echo '<pre>', print_r($result);exit;
        // echo json_encode($fcmFields); exit;
        return json_decode($result,true);
    }    

}

?>
