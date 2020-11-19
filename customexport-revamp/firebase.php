<?php
#API access key from Google API's Console
    define( 'API_ACCESS_KEY', 'AIzaSyDsncspLbVYYZf0VZDDCMm5TmK4TkCX-Qk' );
    $registrationIds = $_GET['id'];

            //322 mangesh
            // $andoridDataArray = array('APA91bG-XWuGxskIKk5f7XujC0Gq0RSlxf2sAKEQRpwNnGCc_xaf1sXzHZAjYt1zEocF3rjtldh-NnSBey5A_Xox1OC9PnPQiiAYjRzP7E9xbc7C-bhMMzwmQIEHKyh1AjPRUyp90twLrYCRhbeOpYfZ4-mxXTD4EA');
            
            // $andoridDataArray = array('APA91bG-XWuGxskIKk5f7XujC0Gq0RSlxf2sAKEQRpwNnGCc_xaf1sXzHZAjYt1zEocF3rjtldh-NnSBey5A_Xox1OC9PnPQiiAYjRzP7E9xbc7C-bhMMzwmQIEHKyh1AjPRUyp90twLrYCRhbeOpYfZ4-mxXTD4EA');


            // $andoridDataArray = array('APA91bE_VYTaBmJcS4CK82vyjMAoouK-wj690yAlRZ0ofQhEglpVqGKQmhilIvbk8Aj2RdxGiDqoPW6hUoybLKQyHdXEvV9_9rZHvDbUsdud157MyUCUohOT47LKoIi4XG7HoKBofASu-by5bkOW09Rxam1JUlh63Q','APA91bG-5A9CX6DOxusVX-OJXSbpeDqOv_e-xwW3Pu2x5Bd3afF4srDfv0ZQrYB1AFJKwWy3j34mr7bjRPXZnrVJytw7JmzvZRdZzYuBmkpMi5QL9boRkhI_Cc81qpq2MkURd9hD661R6vskbmTaZX9ZqO4-CtXNWw');
            
            //mahesh 364
            $registrationIds = 'APA91bHHNaQfUJFnxx5hyyZ7b7AZhS8BJ9Pozj0A4K9niILcHL52SnxsDvWRiNmeiTBtHsGj61fAfcRa7C5oBTVj4fp9_fuzrQtN5KiK8P-jkTMFXRgxFi4HeSWWIAjHl6EbeGqWsMrNsnpUWUjoxjoP2Bw-1fO8_A';
#prep the bundle
     $msg = array
          (
		'body' 	=> 'Check Firebase Cloud Messaging',
		'title'	=> 'Mahesh - Testing FCM Notification',
             	'icon'	=> 'myicon',/*Default Icon*/
              	'sound' => 'mySound'/*Default sound*/
          );
        $notification_data = array(
                    'title' => 'Mahesh - Testing FCM Notification',
                    'type' => 'text',
                    'url_type' => 'category',
                    'category_id' => 3,
                    'category_name' =>'Laptops',
                );

	$fields = array
			(
				'to'		=> $registrationIds,
				'notification'	=> $notification_data
			);
	
	
	$headers = array
			(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
			);
#Send Reponse To FireBase Server	
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
#Echo Result Of FireBase Server
echo $result;