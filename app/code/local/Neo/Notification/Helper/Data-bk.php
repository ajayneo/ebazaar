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
    * @desc divide the data within android and iphone and accordingly call apis
    * @author deepakd
    */
    public function divideRequestWithinAndroidAndIphone($form_data)
    { 
        ini_set('max_execution_time','0'); 
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
                    'image_url' => $form_data['image_name'],
                    'url_type' => 'category',
                    'category_id' => $form_data['category_id'],
                    'category_name' => $_category->getName(),
                );
            } elseif ($form_data['image_link_type'] == 2) {
                $notification_data = array(
                'title' => $form_data['title'],
                'type' => 'text',
                'product_id' => $form_data['product_id']); 
                $notification_data = array(
                    'title' => $form_data['title'],
                    'type' => 'image',
                    'image_url' => $form_data['image_name'],
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

        if($form_data['status'] == 5){
             
           $andoridDataArray1 = Mage::getModel('neo_notification/pushnotificationtest')
                                ->getCollection()->addFieldToSelect('device_Id')                                
                                ->addFieldToFilter('device_type','android')
                                ->getColumnValues('device_Id');

            $andoridDataArray = array_unique($andoridDataArray1);

            //mangesh
            // $andoridDataArray = array('APA91bFESO1tSb7AY9LxY4EdSPoSd9PC88kFNGnTt5GKJJFEus9AV80uxuyx6aj10K-1CbVhZ0y5MqC8kx8bRo3tln6t7fraYzRsZML0dYYxuBE3JFZhYvbC4WpN-rBIxX3PmYlJOAoMvbzqTivlR0DjWRuFnp8Gjg','APA91bEA8JA3qiZHdyFB5jIM5YhxFBLoXO6EiV6cJJhGpgEcC4Gc43yWvl2l2TTLqPqgfP6buD2j6sfmAekDtExCVNhVQ1cs8xk4ljsRwFufzl_dp0BgQV_y-E6VdBAbWaxOCkPb9k39Rl1n9jroKLVbt-l5qtTgaQ');

            //322 mangesh
            // $andoridDataArray = array('APA91bG-XWuGxskIKk5f7XujC0Gq0RSlxf2sAKEQRpwNnGCc_xaf1sXzHZAjYt1zEocF3rjtldh-NnSBey5A_Xox1OC9PnPQiiAYjRzP7E9xbc7C-bhMMzwmQIEHKyh1AjPRUyp90twLrYCRhbeOpYfZ4-mxXTD4EA');
            
            // $andoridDataArray = array('APA91bG-XWuGxskIKk5f7XujC0Gq0RSlxf2sAKEQRpwNnGCc_xaf1sXzHZAjYt1zEocF3rjtldh-NnSBey5A_Xox1OC9PnPQiiAYjRzP7E9xbc7C-bhMMzwmQIEHKyh1AjPRUyp90twLrYCRhbeOpYfZ4-mxXTD4EA');
            
            // $andoridDataArray = array('APA91bG-XWuGxskIKk5f7XujC0Gq0RSlxf2sAKEQRpwNnGCc_xaf1sXzHZAjYt1zEocF3rjtldh-NnSBey5A_Xox1OC9PnPQiiAYjRzP7E9xbc7C-bhMMzwmQIEHKyh1AjPRUyp90twLrYCRhbeOpYfZ4-mxXTD4EA','APA91bHHNaQfUJFnxx5hyyZ7b7AZhS8BJ9Pozj0A4K9niILcHL52SnxsDvWRiNmeiTBtHsGj61fAfcRa7C5oBTVj4fp9_fuzrQtN5KiK8P-jkTMFXRgxFi4HeSWWIAjHl6EbeGqWsMrNsnpUWUjoxjoP2Bw-1fO8_A');

            //latest mahesh and mangesh 462 devices 253 harshad
            // $andoridDataArray = array('APA91bEUTSRKRAqrEkAy3GL86dqptTioebknx2cKdXWnz8cOA_rxHLVssaVd8ZwFsdBiplJ55Z8iFGUJWu4dAIP39EcgZ0LOeM2CrTwF6QHqJZcAIqLgaGp-xv2r0kKdvg8lZJV0-M73hwT3v4MFhLxd8E8XIPp4-Q','APA91bHHNaQfUJFnxx5hyyZ7b7AZhS8BJ9Pozj0A4K9niILcHL52SnxsDvWRiNmeiTBtHsGj61fAfcRa7C5oBTVj4fp9_fuzrQtN5KiK8P-jkTMFXRgxFi4HeSWWIAjHl6EbeGqWsMrNsnpUWUjoxjoP2Bw-1fO8_A','APA91bETkxrFi6PPuij7KdGwzN34tOgL_9MZ07LyCjxLP7eAUxONdDe2AhFULXeZIGobcZEVUWuWVtpB85c--jvl2-LarkMPjR_7shBgqQHqlhOs94og2FNGt8MCWCzPO-VsIOiTtEBShgan8a2uSPEeBTIbn9CFrw');


            // $andoridDataArray = array('APA91bE_VYTaBmJcS4CK82vyjMAoouK-wj690yAlRZ0ofQhEglpVqGKQmhilIvbk8Aj2RdxGiDqoPW6hUoybLKQyHdXEvV9_9rZHvDbUsdud157MyUCUohOT47LKoIi4XG7HoKBofASu-by5bkOW09Rxam1JUlh63Q','APA91bG-5A9CX6DOxusVX-OJXSbpeDqOv_e-xwW3Pu2x5Bd3afF4srDfv0ZQrYB1AFJKwWy3j34mr7bjRPXZnrVJytw7JmzvZRdZzYuBmkpMi5QL9boRkhI_Cc81qpq2MkURd9hD661R6vskbmTaZX9ZqO4-CtXNWw');
            
            //mahesh 364
            // $andoridDataArray = array('APA91bELldrsnNbgPWNz2qidGDqW6ErVYpPqWQn7mW7MwIt8zvfLQxqXUwkuKUQIj23Z4ghk3Lor0h-Sl_lzv1IyfLm8QygnHI1FqUAE1ejWYO33QYmeSm4wafrbZUK-RKUg9uvUiez9zA0v0F358nXJR84CmNdkgg');
            // $andoridDataArray = array('APA91bHHNaQfUJFnxx5hyyZ7b7AZhS8BJ9Pozj0A4K9niILcHL52SnxsDvWRiNmeiTBtHsGj61fAfcRa7C5oBTVj4fp9_fuzrQtN5KiK8P-jkTMFXRgxFi4HeSWWIAjHl6EbeGqWsMrNsnpUWUjoxjoP2Bw-1fO8_A');

            // echo "<pre>"; print_r($andoridDataArray); 
            //exit;

            // $andoridDataArray2 = Mage::getModel('neo_notification/device')
            //                     ->getCollection()->addFieldToSelect('device_Id')                                
            //                     ->addFieldToFilter('device_type','android')
            //                     ->getColumnValues('device_Id');
            // $andoridDataArray2 = array_unique($andoridDataArray2);



            $iphoneDataArray1 = Mage::getModel('neo_notification/pushnotificationtest')
                                ->getCollection()->addFieldToSelect('device_Id') 
                                ->addFieldToFilter('device_type','iphone')
                                ->getColumnValues('device_Id'); 
            $iphoneDataArray = array_unique($iphoneDataArray1);
            //mangesh 374
            // $iphoneDataArray = array('08a8ce0692aab8efe7c3f940cb3c64caa0b560dc1528d16761e01f9888988f27');
            // $iphoneDataArray = array();

        }else{
            /*$andoridDataArray1 = Mage::getModel('neo_notification/pushnotification')  
                                ->getCollection()->addFieldToSelect('device_Id')                                
                                ->addFieldToFilter('device_type','android')
                                ->getColumnValues('device_Id');
            $andoridDataArray = array_unique($andoridDataArray1);

            $iphoneDataArray1 = Mage::getModel('neo_notification/pushnotification')
                                ->getCollection()->addFieldToSelect('device_Id')
                                ->addFieldToFilter('device_type','iphone')
                                ->getColumnValues('device_Id'); 
            $iphoneDataArray = array_unique($iphoneDataArray1); */

            $andoridDataArray1 = Mage::getModel('neo_notification/pushnotification')
                                ->getCollection()->addFieldToSelect('device_Id')                                
                                ->addFieldToFilter('device_type','android')
                                ->getColumnValues('device_Id');

            $andoridDataArray1 = array_unique($andoridDataArray1);
            
            $andoridDataArray2 = Mage::getModel('neo_notification/device')
                                ->getCollection()->addFieldToSelect('device_Id')                                
                                ->addFieldToFilter('device_type','android')
                                ->getColumnValues('device_Id');
            $andoridDataArray2 = array_unique($andoridDataArray2);

            $andoridDataArray = array_merge($andoridDataArray1,$andoridDataArray2);

            // $andoridDataArray = array();

            $iphoneDataArray1 = Mage::getModel('neo_notification/pushnotification')
                                ->getCollection()->addFieldToSelect('device_Id') 
                                ->addFieldToFilter('device_type','iphone')
                                ->getColumnValues('device_Id');    
            $iphoneDataArray1 = array_unique($iphoneDataArray1);

            $iphoneDataArray2 = Mage::getModel('neo_notification/device')
                                ->getCollection()->addFieldToSelect('device_Id') 
                                ->addFieldToFilter('device_type','iphone')
                                ->getColumnValues('device_Id');
            $iphoneDataArray2 = array_unique($iphoneDataArray2);
            
            $iphoneDataArray = array_merge($iphoneDataArray1,$iphoneDataArray2);
            
            // $iphoneDataArray = array();
            //check iphones
            // echo '<pre>'; print_r($iphoneDataArray); echo "<pre>";

        }

        //echo "<pre>";print_r($andoridDataArray);print_r($iphoneDataArray);exit;


                  
 
        // call andorid push notification api
        //$android_response = Mage::helper('neo_notification')->sendAndroidNotification($andoridDataArray, $productData, $notification_data);
        $andoridDataArrayChunk = array_chunk($andoridDataArray,999,false);
    
	$android_response = "";
    $iphone_response = "";
    $android_success = "0";
    $android_failure = "0";
    $android_total = "0";
        $i = 0;
        foreach ($andoridDataArrayChunk as $key => $value) {
            // call android push notification api  
          $android_response = Mage::helper('neo_notification')->sendAndroidNotification($value, $productData, $notification_data);

          $android_success =  $android_success + $android_response['success'];
          $android_failure =  $android_failure + $android_response['failure'];
          $android_total   =  $android_success + $android_failure;
          $message = "Android Total notification sent : $android_total Total no of success : $android_success Total no of failed : $android_failure";
          // Mage::log($message, null, 'pushnotification_success.log', true);          
          Mage::getSingleton('adminhtml/session')->addSuccess($message);
          $i++;          

        }
        $iphone_response = Mage::helper('neo_notification')->sendIphoneNotification($iphoneDataArray, $productData, $notification_data);
        Mage::getSingleton('adminhtml/session')->addSuccess("iPhone Total no of success : $iphone_response");        
        //$total_android_users_count = sizeof($andoridDataArray);
        //$total_iphone_users_count = sizeof($iphoneDataArray);
        //message
        //Mage::getSingleton('adminhtml/session')->addSuccess("$android_response --Notification sent $iphone_response out of $total_iphone_users_count for iPhone");
        
        /*print_r($iphone_response);
        die;*/
        //Mage::helper('neo_notification')->divideRequestWithinAndroidAndIphone($data);
    } 
    /*
    * @desc trigger andorid api
    * @author deepakd
    */
    public function sendAndroidNotification($andoridDataArray, $productData, $notification_data)
    {
        ini_set('max_execution_time',0); 
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
        return json_decode($result,true);
    }
    /*
    * @desc trigger iphone api
    * @author deepakd
    */
    public function sendIphoneNotification($iphoneDataArray, $productData, $notification_data)
    {

        ini_set('max_execution_time',0);  


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
            stream_context_set_option($ctx, 'ssl', 'local_cert', Mage::getModuleDir('Helper', 'Neo_Notification')."/Helper/EBPushCert.pem");
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

        return $count_iphone_notifications;
    }

}

?>
