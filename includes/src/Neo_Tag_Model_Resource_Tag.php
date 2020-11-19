<?php
    class Neo_Tag_Model_Resource_Tag extends Mage_Tag_Model_Resource_Tag {
    	
		//by default, when loading a tag by name magento does not load the store ids it is allowed in
    	//this method loads also the store ids
    	public function loadByName($model,$name){
    		parent::loadByName($model,$name);
			if($model->getId()){
				$this->_afterLoad($model);
			}else{
				return false;
			}
    	}
    	
    }
?>