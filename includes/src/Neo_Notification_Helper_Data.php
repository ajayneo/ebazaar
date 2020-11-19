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
        $product_id = $form_data['product_id'];
        $status = $form_data['status'];
        $productModel = Mage::getModel('catalog/product')->load($product_id);

/*        $productData = array('title' => $productModel->getName(),
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
        } elseif ($form_data['notfication_type'] == 3) {
           $notification_data = array(
                'title' => $form_data['title'],
                'type' => 'link',
                'link_url' => $form_data['link_url']
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
        $andoridDataArray = Mage::getModel('neo_notification/pushnotification')
                                ->getCollection()
                                ->addFieldToSelect('device_Id')
                                ->addFieldToFilter('device_type','android')
                                ->getColumnValues('device_Id');
        $iphoneDataArray = Mage::getModel('neo_notification/pushnotification')
                                ->getCollection()
                                ->addFieldToSelect('device_Id')
                                ->addFieldToFilter('device_type','iphone')
                                ->getColumnValues('device_Id');

        // call andorid push notification api
        //$android_response = Mage::helper('neo_notification')->sendAndroidNotification($andoridDataArray, $productData, $notification_data);
        $andoridDataArrayChunk = array_chunk($andoridDataArray,999,false);
	/*echo "<pre>";
	print_r($andoridDataArray);
	print_r($andoridDataArrayChunk);
	die;*/
	$android_response = "";
        foreach ($andoridDataArrayChunk as $key => $value) {
            // call android push notification api
            $android_response .= Mage::helper('neo_notification')->sendAndroidNotification($value, $productData, $notification_data);
        }

        $iphone_response = Mage::helper('neo_notification')->sendIphoneNotification($iphoneDataArray, $productData, $notification_data);
        $total_android_users_count = sizeof($andoridDataArray);
        $total_iphone_users_count = sizeof($iphoneDataArray);
        Mage::getSingleton('adminhtml/session')->addSuccess("$android_response --Notification sent $iphone_response out of $total_iphone_users_count for iPhone");
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

        if ($result === FALSE) {
            Mage::getSingleton('adminhtml/session')->addWarning('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        return $result;
    }
    /*
    * @desc trigger iphone api
    * @author deepakd
    */
    public function sendIphoneNotification($iphoneDataArray, $productData, $notification_data)
    {

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
            } else {
                $count_iphone_notifications++;
            }
            //echo 'Message successfully delivered'.$message. PHP_EOL;
            // Close the connection to the server
            fclose($fp);
        }
        return $count_iphone_notifications;
    }

}

?>