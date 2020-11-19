<?php
/*
* Copyright (c) 2016 www.magebuzz.com 
*/
class Magebuzz_Featuredproducts_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_featuredproducts';
		$this->_blockGroup = 'featuredproducts';
		$this->_headerText = 'Featured Products Management';
		parent::__construct();
		$this->removeButton('add');
	}
}