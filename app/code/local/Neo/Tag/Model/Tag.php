<?php
    class Neo_Tag_Model_Tag extends Mage_Tag_Model_Tag{
    		
    	//change the url from 'tag/product/list/tagId/23' to 'tag/camera'
		public function getTaggedProductsUrl(){
			return Mage::getUrl('',array('_direct' => 'tag/'.strtolower(str_replace(" ", "-", $this->getName()))));
		}
    }
?>