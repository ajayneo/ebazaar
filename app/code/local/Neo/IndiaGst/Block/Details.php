<?php
class Neo_IndiaGst_Block_Details extends Mage_Core_Block_Template{

	public function getCities(){
		$cities = Mage::getModel('indiagst/indiagst')->indiaCities();
		
		return $cities;
	}
	public function getStates(){
		$cities = Mage::getModel('indiagst/indiagst')->indiaStates();
		
		return $cities;
	}
}