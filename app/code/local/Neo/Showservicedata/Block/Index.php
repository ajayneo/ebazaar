<?php   
class Neo_Showservicedata_Block_Index extends Mage_Core_Block_Template{   


	protected function showservicedatalist(){
		try{

			//city filter
			$return = array();
			mysql_connect('180.149.246.49', 'electronics', 'hyju_PP$j7V^RR9');
	        mysql_select_db('electronic_test');
	        //ORDER BY id DESC
	        //$data = mysql_query('SELECT *  FROM neo_eb_service_center_list ' );
	        $data = mysql_query('SELECT *  FROM neo_eb_service_center_list_new_2' );

	        while ($line = mysql_fetch_assoc($data)){
	        	//echo '<pre>';print_r($line);
	        	$return[] = $line;
	        }

	        
		}catch(Exception $ex){
			return 0;
		}

		return $return;
	}


}