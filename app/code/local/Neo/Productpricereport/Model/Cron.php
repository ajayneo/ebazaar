<?php
class Neo_Productpricereport_Model_Cron{	
	

    public function toptenpartnersperweek(){

        error_reporting(0);
        exit;


        $last_order_id = Mage::getStoreConfig('sidebanner_section/dashboard/weeklysale');      

        $collection = Mage::getResourceModel('sales/order_grid_collection');
        $collection->addFieldToFilter('increment_id', array('gt'=>$last_order_id))->getData();          
     
        foreach($collection as $order)
        {   
            $totalSalesAffiliate[$order['shipping_name']] = $totalSalesAffiliate[$order['shipping_name']] + $order['grand_total'];
            $totalSalesAffiliate1[$order['shipping_name']] = $totalSalesAffiliate1[$order['shipping_name']]  + 1;       
        }

        arsort($totalSalesAffiliate);
        $showRecords = array_slice($totalSalesAffiliate, 0, 10, true);

        $mailBody = '';


        $mailBody .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
            $mailBody .= '<tr>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Customer</th>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Number of Orders</th>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total Sale (Incl Tax) Rs</th>';
            $mailBody .= '</tr>';

            if (!$fp = fopen('php://temp', 'w+')) return FALSE;
            fputcsv($fp, array('Customer', 'Number of Orders', 'Total Sale (Incl Tax) Rs'));


             foreach ($showRecords as $key => $value){

                $amount = $value;
                setlocale(LC_MONETARY, 'en_IN');
                $amount = money_format('%!i', $amount);
                $line[0] = ucwords($key); 
                $line[1] = $totalSalesAffiliate1[$key];
                $line[2] = 'Rs. '.$amount;
                //$line[2] = Mage::helper('core')->currency($value, true, false);

            fputcsv($fp, $line); 

                $mailBody .= '<tr>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .=     ucwords($key); 
                    $mailBody .= '</td>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .= $totalSalesAffiliate1[$key];
                        
                    $mailBody .= '</td>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .= 'Rs. '.$amount; 
                    //$mailBody .= Mage::helper('core')->currency($value, true, false); 
                    $mailBody .= '</td>';
                $mailBody .= '</tr>';
            }

        $mailBody .= '</table>';


        rewind($fp);


        //$emailTo = 'sandeepmukherjee0488@gmail.com';
        $emailTo = 'sharad@compuindia.com,rajeev@electronicsbazaar.com,rekha@compuindia.com,amritpalsingh.d@electronicsbazaar.com,rajkumar@electronicsbazaar.com,akhilesh@electronicsbazaar.com,kushlesh@electronicsbazaar.com,vivek.c@kaykay.co.in,sandeepmukherjee0488@gmail.com';

        $emailSubject = 'Top Ten Partners Every Week';
        $multipartSep = '-----'.md5(time()).'-----';
        $strem = stream_get_contents($fp);
        $attachment = chunk_split(base64_encode($strem));
        $boundary = md5(time());
        $header = "From: Electronics Bazaar <support@electronicsbazaar.com>\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed;boundary=\"" . $boundary . "\"\r\n";
        $output = "--".$boundary."\r\n";
        $output .= "Content-Type: text/csv; name=\"top-ten-partners-every-day.csv\";\r\n";
        $output .= "Content-Disposition: attachment;\r\n\r\n";
        $output .= $strem."\r\n\r\n";
        $output .= "--".$boundary."\r\n";
        $output .= "Content-type: text/html; charset=\"utf-8\"\r\n";
        $output .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $output .= $mailBody."\r\n\r\n";
        $output .= "--".$boundary."--\r\n\r\n"; 

        mail($emailTo, $emailSubject, $output, $header);

    }

    public function toptenpartnersperday(){

        error_reporting(0);
        exit;

        $last_order_id = Mage::getStoreConfig('sidebanner_section/dashboard/dailysales');

        $collection = Mage::getResourceModel('sales/order_grid_collection');
        $collection->addFieldToFilter('increment_id', array('gt'=>$last_order_id))->getData();          
     
        foreach($collection as $order)
        {   
            $totalSalesAffiliate[$order['shipping_name']] = $totalSalesAffiliate[$order['shipping_name']] + $order['grand_total'];
            $totalSalesAffiliate1[$order['shipping_name']] = $totalSalesAffiliate1[$order['shipping_name']]  + 1;       
        }

        arsort($totalSalesAffiliate);
        $showRecords = array_slice($totalSalesAffiliate, 0, 10, true);

        $mailBody = '';


        $mailBody .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
            $mailBody .= '<tr>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Customer</th>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Number of Orders</th>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total Sale (Incl Tax) Rs</th>';
            $mailBody .= '</tr>';

            if (!$fp = fopen('php://temp', 'w+')) return FALSE;
            fputcsv($fp, array('Customer', 'Number of Orders', 'Total Sale (Incl Tax) Rs'));


             foreach ($showRecords as $key => $value){

                $amount = $value;
                setlocale(LC_MONETARY, 'en_IN');
                $amount = money_format('%!i', $amount);
                $line[0] = ucwords($key); 
                $line[1] = $totalSalesAffiliate1[$key];
                $line[2] = 'Rs. '.$amount;
                //$line[2] = Mage::helper('core')->currency($value, true, false);

            fputcsv($fp, $line); 

                $mailBody .= '<tr>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .=     ucwords($key);   
                    $mailBody .= '</td>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .= $totalSalesAffiliate1[$key];
                        
                    $mailBody .= '</td>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .= 'Rs. '.$amount; 
                    //$mailBody .= Mage::helper('core')->currency($value, true, false); 
                    $mailBody .= '</td>';
                $mailBody .= '</tr>';
            }

        $mailBody .= '</table>';


        rewind($fp);


        //$emailTo = 'sandeepmukherjee0488@gmail.com';
        $emailTo = 'sharad@compuindia.com,rajeev@electronicsbazaar.com,rekha@compuindia.com,amritpalsingh.d@electronicsbazaar.com,rajkumar@electronicsbazaar.com,akhilesh@electronicsbazaar.com,kushlesh@electronicsbazaar.com,vivek.c@kaykay.co.in,sandeepmukherjee0488@gmail.com';

        $emailSubject = 'Top Ten Partners Every Day';
        $multipartSep = '-----'.md5(time()).'-----';
        $strem = stream_get_contents($fp);
        $attachment = chunk_split(base64_encode($strem));
        $boundary = md5(time());
        $header = "From: Electronics Bazaar <support@electronicsbazaar.com>\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed;boundary=\"" . $boundary . "\"\r\n";
        $output = "--".$boundary."\r\n";
        $output .= "Content-Type: text/csv; name=\"top-ten-partners-every-day.csv\";\r\n";
        $output .= "Content-Disposition: attachment;\r\n\r\n";
        $output .= $strem."\r\n\r\n";
        $output .= "--".$boundary."\r\n";
        $output .= "Content-type: text/html; charset=\"utf-8\"\r\n";
        $output .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $output .= $mailBody."\r\n\r\n";
        $output .= "--".$boundary."--\r\n\r\n"; 

        mail($emailTo, $emailSubject, $output, $header);

    }

    public function testsum(){

        error_reporting(0);


        $last_order_id = Mage::getStoreConfig('sidebanner_section/dashboard/dailysales');

        $collection = Mage::getResourceModel('sales/order_grid_collection');
        $collection->addFieldToFilter('increment_id', array('gt'=>$last_order_id))->getData();          
     
        foreach($collection as $order)
        {   
            $totalSalesAffiliate[$order['shipping_name']] = $totalSalesAffiliate[$order['shipping_name']] + $order['grand_total'];
            $totalSalesAffiliate1[$order['shipping_name']] = $totalSalesAffiliate1[$order['shipping_name']]  + 1;       
        }

        arsort($totalSalesAffiliate);
        $showRecords = array_slice($totalSalesAffiliate, 0, 10, true);

        $mailBody = '';


        $mailBody .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
            $mailBody .= '<tr>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Customer</th>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Number of Orders</th>';
                $mailBody .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total Sale (Incl Tax) Rs</th>';
            $mailBody .= '</tr>';

   	        if (!$fp = fopen('php://temp', 'w+')) return FALSE;
   	        fputcsv($fp, array('Customer', 'Number of Orders', 'Total Sale (Incl Tax) Rs'));


             foreach ($showRecords as $key => $value){

             	$amount = $value;
				setlocale(LC_MONETARY, 'en_IN');
				$amount = money_format('%!i', $amount);
             	$line[0] = ucwords($key); 
	            $line[1] = $totalSalesAffiliate1[$key];
	            $line[2] = 'Rs. '.$amount;
	            //$line[2] = Mage::helper('core')->currency($value, true, false);

            fputcsv($fp, $line); 

                $mailBody .= '<tr>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .=     ucwords($key); 
                    $mailBody .= '</td>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .= $totalSalesAffiliate1[$key];
                        
                    $mailBody .= '</td>';
                    $mailBody .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">';
                    $mailBody .= 'Rs. '.$amount; 
                    //$mailBody .= Mage::helper('core')->currency($value, true, false); 
                    $mailBody .= '</td>';
                $mailBody .= '</tr>';
            }

        $mailBody .= '</table>';


        rewind($fp);


        $emailTo = 'sandeepmukherjee0488@gmail.com';

        $emailSubject = 'Top Ten Partners Every Day';
        $multipartSep = '-----'.md5(time()).'-----';
        $strem = stream_get_contents($fp);
        $attachment = chunk_split(base64_encode($strem));
        $boundary = md5(time());
        $header = "From: Test Electronics Bazaar <support@electronicsbazaar.com>\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed;boundary=\"" . $boundary . "\"\r\n";
        $output = "--".$boundary."\r\n";
        $output .= "Content-Type: text/csv; name=\"top-ten-partners-every-day.csv\";\r\n";
        $output .= "Content-Disposition: attachment;\r\n\r\n";
        $output .= $strem."\r\n\r\n";
        $output .= "--".$boundary."\r\n";
        $output .= "Content-type: text/html; charset=\"utf-8\"\r\n";
        $output .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $output .= $mailBody."\r\n\r\n";
        $output .= "--".$boundary."--\r\n\r\n"; 

        mail($emailTo, $emailSubject, $output, $header);

    }



	public function testsum1(){

		//ini_set('max_execution_time', 800);


    function create_csv_string($data) {
       
        mysql_connect('180.149.246.49', 'electronics', 'hyju_PP$j7V^RR9');
        mysql_select_db('electron_electronics');
       
        $data = mysql_query('SELECT id, product_id, name, to_price, from_price, changed_by, date FROM neo_productpricereport ORDER BY id DESC' );

        // Open temp file pointer 
        if (!$fp = fopen('php://temp', 'w+')) return FALSE;
       
        fputcsv($fp, array('ID', 'Product Id', 'Name', 'OLD Price', 'New Price', 'Changed By', 'Date'));
       
        // Loop data and write to file pointer  
        while ($line = mysql_fetch_assoc($data)) fputcsv($fp, $line);
       
        // Place stream pointer at beginning
        rewind($fp);

        // Return the data
        return stream_get_contents($fp);
     
    }

    //  'sharad@compuindia.com,rajeev@electronicsbazaar.com,rekha@compuindia.com,amritpalsingh.d@electronicsbazaar.com,rajkumar@electronicsbazaar.com,akhilesh@electronicsbazaar.com,kushlesh@electronicsbazaar.com,sandeepmukherjee0488@gmail.com,vivek.c@kaykay.co.in'

    function send_csv_mail($csvData, $body, $to = 'sandeepmukherjee0488@gmail.com', $subject = 'Website Product Price Change Report', $from = 'ElectronicsBazaar@electronicsbazaar.com') { 

        // This will provide plenty adequate entropy
        $multipartSep = '-----'.md5(time()).'-----';

        // Arrays are much more readable
        $headers = array(    
            "From: $from",
            "Reply-To: $from",
            "Content-Type: multipart/mixed; boundary=\"$multipartSep\""
        ); 

        // Make the attachment
        $attachment = chunk_split(base64_encode(create_csv_string($csvData)));

        // Make the body of the message
        $body = "--$multipartSep\r\n"
            . "Content-Type: text/plain; charset=ISO-8859-1; format=flowed\r\n"
            . "Content-Transfer-Encoding: 7bit\r\n"
            . "\r\n"
            . "$body\r\n"
            . "--$multipartSep\r\n"
            . "Content-Type: text/csv\r\n"
            . "Content-Transfer-Encoding: base64\r\n"
            . "Content-Disposition: attachment; filename=\"Product-Price-Change-Website-Report-" . date("F-j-Y") . ".csv\"\r\n"
            . "\r\n"
            . "$attachment\r\n"
            . "--$multipartSep--";

        // Send the email, return the result
        return @mail($to, $subject, $body, implode("\r\n", $headers));

    }

    $array = array(array(1,2,3,4,5,6,7), array(1,2,3,4,5,6,7), array(1,2,3,4,5,6,7));

    send_csv_mail($array, "Website Product Price Change Report \r\n \r\n https://www.electronicsbazaar.com/");  

	} 
} 