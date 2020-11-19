<?php // India GST
class Neo_Indiagst_Model_Indiagst extends Mage_Core_Model_Abstract{
	protected function _construct(){

       $this->_init("indiagst/gstdetails");

    }
	public function indiaCities(){
		try{
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$query_cities = 'SELECT `city` FROM `city_pincodes` GROUP BY city';
		$result = $connection->fetchAll($query_cities);
		$column = array();
		foreach ($result as $key => $value) {
			# code...
			$column[] = $value['city'];
		}
		}catch(Exception $e){
			Mage::log($e->getMessage());
		}
		return $column;
	}

	public function indiaStates(){
		try{
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$query = "SELECT `default_name` FROM `directory_country_region` WHERE `country_id` LIKE 'IN' AND `code` LIKE 'IN%'";
		$result = $connection->fetchAll($query);
		$column = array();
		foreach ($result as $key => $value) {
			$column[] = $value['default_name'];
		}
		}catch(Exception $e){
			Mage::log($e->getMessage());
		}
		return $column;
	}
}


?>