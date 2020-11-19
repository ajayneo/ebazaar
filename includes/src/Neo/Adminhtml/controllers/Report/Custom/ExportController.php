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
class Neo_Adminhtml_Report_Custom_ExportController extends Mage_Adminhtml_Controller_Report_Abstract
{   	
	public function orderAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Order Report'))
            ->_setActiveMenu('report/Neo_reports/export_order')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Order Report')
				->setButtonTitle('Export Orders')
				->setReturnUrl($this->getUrl('*/*/exportOrders'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 

	/**
     * function for Export Orders action
     *
     *  @return void()
     */
	public function exportOrdersAction() {
        $fileName   = 'Orders.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_order')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	 	
	public function invoiceAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Invoice Report'))
            ->_setActiveMenu('report/Neo_reports/export_invoice')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Invoice Report')
				->setButtonTitle('Export Invoices')
				->setReturnUrl($this->getUrl('*/*/exportInvoices'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 

	/**
     * function for Export Invoices action
     *
     *  @return void()
     */
	public function exportInvoicesAction() {
        $fileName   = 'Invoices.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_invoice')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	 	
	public function shipmentAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Shipment Report'))
            ->_setActiveMenu('report/Neo_reports/export_shipment')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Shipment Report')
				->setButtonTitle('Export Shipments')
				->setReturnUrl($this->getUrl('*/*/exportShipments'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 

	/**
     * function for Export Shipments action
     *
     *  @return void()
     */
	public function exportShipmentsAction() {
        $fileName   = 'Shipments.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_shipment')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	 	
	public function creditmemoAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Creditmemo Report'))
            ->_setActiveMenu('report/Neo_reports/export_creditmemo')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Creditmemo Report')
				->setButtonTitle('Export Creditmemos')
				->setReturnUrl($this->getUrl('*/*/exportCreditmemos'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 

	/**
     * function for Export Creditmemos action
     *
     *  @return void()
     */
	public function exportCreditmemosAction() {
        $fileName   = 'Creditmemos.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_creditmemo')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	 	
	public function itemsAction()
	{
		$this->loadLayout()
			->_title($this->__('Export LineItem Report'))
            ->_setActiveMenu('report/Neo_reports/export_lineitems')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export LineItem Report')
				->setButtonTitle('Export LineItems')
				->setReturnUrl($this->getUrl('*/*/exportLineitems'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 
	
	public function bluedartshipmentsAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Bluedart Shipment Report'))
            ->_setActiveMenu('report/Neo_reports/export_lineitems')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Bluedart Shipment Report')
				->setButtonTitle('Export Bluedart Shipments')
				->setReturnUrl($this->getUrl('*/*/exportBluedartshipments'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 

	/**
     * function for Export Creditmemos action
     *
     *  @return void()
     */
	public function exportLineitemsAction() {
        $fileName   = 'Items.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_items')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	
	public function exportBluedartshipmentsAction() {
        $fileName   = 'BluedartShipments.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_bluedartshipments')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	
	/**
     * function for Export BlueDart Shipment Ecom[Prepaid]
     *
     *  @return void()
     */
	 
	public function bluedartshipmentsprepaidAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Bluedart Shipment Ecom(PrePaid)'))
            ->_setActiveMenu('report/Neo_reports/export_lineitems')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Bluedart Shipment Ecom(PrePaid) Report')
				->setButtonTitle('Export Bluedart Shipments Ecom(PrePaid)')
				->setReturnUrl($this->getUrl('*/*/exportBluedartshipmentsprepaid'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 
	public function exportBluedartshipmentsprepaidAction() {
        $fileName   = 'BluedartShipmentsPrepaid.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_bluedartshipmentsprepaid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	/**
     * function for Export BlueDart Shipment COD
     *
     *  @return void()
     */
	 
	public function bluedartshipmentscodAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Bluedart Shipment COD'))
            ->_setActiveMenu('report/Neo_reports/export_lineitems')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Bluedart Shipment COD Report')
				->setButtonTitle('Export Bluedart Shipments COD')
				->setReturnUrl($this->getUrl('*/*/exportBluedartshipmentscod'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 
	public function exportBluedartshipmentscodAction() {
        $fileName   = 'BluedartShipmentsCod.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_bluedartshipmentscod')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	
	public function ecomexpressshipmentscodAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Shipment'))
            ->_setActiveMenu('report/Neo_reports/export_lineitems')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Shipment Report')
				->setButtonTitle('Export Shipments')
				->setReturnUrl($this->getUrl('*/*/exportEcomexpressshipmentscod'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 
	public function exportEcomexpressshipmentscodAction() {
        $fileName   = 'ExportShipment.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_ecomexpressshipmentscod')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
	
	public function ecomexpressshipmentsprepaidAction()
	{
		$this->loadLayout()
			->_title($this->__('Export Ecomexpress Shipment Prepaid'))
            ->_setActiveMenu('report/Neo_reports/export_lineitems')
            ->_addContent(
				$this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export')
				->setPageTitle('Export Ecomexpress Shipment Prepaid Report')
				->setButtonTitle('Export Ecomexpress Shipments Prepaid')
				->setReturnUrl($this->getUrl('*/*/exportEcomexpressshipmentsprepaid'))
				);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
	} 
	public function exportEcomexpressshipmentsprepaidAction() {
        $fileName   = 'EcomexpressShipmentsCod.csv';
        $content = $this->getLayout()->createBlock('Neo_adminhtml/report_custom_sales_export_ecomexpressshipmentsprepaid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
	}
}
