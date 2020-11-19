<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales report admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Neo_Adminhtml_Report_CustomController extends Mage_Adminhtml_Controller_Report_Abstract
{    
	public function salesAction()
	{
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_initAction()
            ->_setActiveMenu('report/Neo_reports/sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('report_custom_sales_sales.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
	}   

    /**
     * Export sales report grid to CSV format
     */
    public function exportSalesCsvAction()
    {
		set_time_limit(0);
		ini_set('memory_limit','-1');
		ini_set('max_execution_time','0');
        $fileName   = 'dsr.csv';
        $grid       = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_sales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'dsr.xml';
        $grid       = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_sales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
	public function invoiceAction()
	{
        $this->_title($this->__('Reports'))->_title($this->__('Invoices'))->_title($this->__('Invoices'));

        $this->_initAction()
            ->_setActiveMenu('report/Neo_reports/invoice')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Invoices Report'), Mage::helper('adminhtml')->__('Invoices Report'));

        $gridBlock = $this->getLayout()->getBlock('report_custom_sales_invoice.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
	} 
	

    /**
     * Export sales invoice report grid to CSV format
     */
    public function exportInvoiceCsvAction()
    {
        $fileName   = 'warrentyupsell.csv';
        $grid       = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_invoice_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export sales invoice report grid to Excel XML format
     */
    public function exportInvoiceExcelAction()
    {
        $fileName   = 'warrentyupsell.xml';
        $grid       = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_invoice_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
	
	public function stockAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Stock Report'))
            ->_setActiveMenu('report/Neo_reports/stock')
            ->_addContent($this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_stock'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 

	/**
     * function for Product quntity export action
     *
     *  @return void()
     */
	public function exportStockAction() {
        $fileName   = 'stock.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_stock_export')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	public function nocouponAction()
	{
       $this->loadLayout()
			->_title($this->__('Export No Coupon Report'))
            ->_setActiveMenu('report/Neo_reports/nocoupon')
            ->_addContent($this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_nocoupon'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	}  
	public function exportNocouponAction() {
        $fileName   = 'nocoupon.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_nocoupon_export')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}	
	
	public function servicetagmailerAction()
	{
       $this->loadLayout()
			->_title($this->__('Export Service Tag Transfer Report'))
            ->_setActiveMenu('report/Neo_reports/servicetagmailer')
            ->_addContent($this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_servicetagmailer'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 
	public function exportServicetagAction(){
		$fileName   = 'servicetagtransfer.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_servicetagmailer_export')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	
	
}
