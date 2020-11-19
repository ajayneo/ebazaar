<?php
    class Neo_Affiliate_Adminhtml_Manage_AffiliateController extends Mage_Adminhtml_Controller_Action{
    	
		/**
		 * Added this function by pradeep sanku on 14th August since patch SUPEE 6285 was having access to some 3rd party extension
		 */
		protected function _isAllowed()
		{
			return Mage::getSingleton('admin/session')->isAllowed('sales/manage_affiliate');
		}
    	
		public function indexAction()
		{
			$this->loadLayout()->_setActiveMenu('sales/manage_affiliate');
			$this->_addContent($this->getLayout()->createBlock('neo_affiliate/adminhtml_manage_affiliate'));
			$this->renderLayout();
		}
		
		public function exportCsvAction()
		{
			$fileName = 'affiliateorders.csv';
			$grid = $this->getLayout()->createBlock('neo_affiliate/adminhtml_manage_affiliate_grid');
			$this->_prepareDownloadResponse($fileName,$grid->getCsvFile());
		}
		
		public function exportExcelAction()
		{
			$fileName = 'affiliate_orders.xml';
			$grid = $this->getLayout()->createBlock('neo_affiliate/adminhtml_manage_affiliate_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
		
		public function viewAction(){
			$this->loadLayout();
			$this->renderLayout();
		}
    }
?>