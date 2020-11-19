<?php
class Neo_Bannericon_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/bannericon?id=15 
    	 *  or
    	 * http://site.com/bannericon/id/15 	
    	 */
    	/* 
		$bannericon_id = $this->getRequest()->getParam('id');

  		if($bannericon_id != null && $bannericon_id != '')	{
			$bannericon = Mage::getModel('bannericon/bannericon')->load($bannericon_id)->getData();
		} else {
			$bannericon = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	
    	if($bannericon == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$bannericonTable = $resource->getTableName('bannericon');
			
			$select = $read->select()
			   ->from($bannericonTable,array('bannericon_id','title','content','status','filename'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$bannericon = $read->fetchRow($select);
		}
		print_r($bannericon);exit;
		//Mage::register('bannericon', $bannericon);
		

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}