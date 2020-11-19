<?php
class Neo_Ticker_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/ticker?id=15 
    	 *  or
    	 * http://site.com/ticker/id/15 	
    	 */
    	/* 
		$ticker_id = $this->getRequest()->getParam('id');

  		if($ticker_id != null && $ticker_id != '')	{
			$ticker = Mage::getModel('ticker/ticker')->load($ticker_id)->getData();
		} else {
			$ticker = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($ticker == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$tickerTable = $resource->getTableName('ticker');
			
			$select = $read->select()
			   ->from($tickerTable,array('ticker_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$ticker = $read->fetchRow($select);
		}
		Mage::register('ticker', $ticker);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}