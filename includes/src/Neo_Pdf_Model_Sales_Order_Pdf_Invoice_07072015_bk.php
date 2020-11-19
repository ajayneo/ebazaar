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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
 
class Neo_Pdf_Model_Sales_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontArial($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 35,
			'font'  => 'bold',
			'align' => 'left'
        );

        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Serial No.'),
            'feed' => 145,
			'font'  => 'bold',
			'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 230,
			'font'  => 'bold',
            'align' => 'left'
        );		

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 400,
			'font'  => 'bold',
            'align' => 'left'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Price'),
            'feed'  => 320,
			'font'  => 'bold',
            'align' => 'left'
        );

       /* $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Tax'),
            'feed'  => 380,
			'font'  => 'bold',
            'align' => 'left'
        );
		
		 $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Tax %'),
            'feed'  => 450,
			'font'  => 'bold',
            'align' => 'left'
        );*/

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Subtotal (Incl. Tax)'),
            'feed'  => 450,
			'font'  => 'bold',
            'align' => 'left'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page  = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());

            // added by pradeep sanku on the 30 oct 2014
            $page->drawText("TIN NO # 27370085832V, Service Tax #: AACFK0693DST002", 323, $this->y, 'UTF-8');

			$this->y -= 15;
            /* Add head */
			
			$displayText = "Invoice";
			$incrementId = $invoice->getIncrementId();
			$FirstKey = $incrementId[0];			
			$LikeKey = Mage::getStoreConfig('sales/receiptkey/receiptkey_data');   
			if($LikeKey  == $FirstKey){
				$displayText = "Receipt";
			}
			
			
            $this->insertOrder($page,$order,Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()),$displayText);
            /* Add document text and number */			
			
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
			$font = $this->_setFontArial($page, 9);
			$docHeader = $this->getDocHeaderCoordinates(); 
		
			$invoiceIdText = Mage::helper('sales')->__($displayText.' # ') . $invoice->getIncrementId();
			$invoiceDateText = Mage::helper('sales')->__($displayText.' Date : ') . Mage::helper('core')->formatDate($invoice->getCreatedAtStoreDate(), 'medium', false
			
            );
            $page->drawText("Subsidiary of Kay Kay Overseas Corporation", 35, $docHeader[1] + 10, 'UTF-8');
			$page->drawText($invoiceIdText .', '. $invoiceDateText, 35, $docHeader[1], 'UTF-8');
				
			/* $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
			$font = $this->_setFontArial($page, 10);
			$docHeader = $this->getDocHeaderCoordinates();
			$invoiceDateText = Mage::helper('sales')->__('Invoice Date # ') . Mage::helper('core')->formatDate($invoice->getCreatedAtStoreDate(), 'medium', false
            );
			$page->drawText($invoiceDateText, $this->getAlignRight($invoiceDateText, 130, 440, $font, 10,0), $docHeader[1] - 10, 'UTF-8');
			*/
			
			
			
            /* Add table */
            $this->_drawHeader($page);
			
			// added for item box bof
			$this->y = $this->y ;
			$this->y += 20;
			// added for item box eof
			
			
            /* Add body */
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }	
							
				// added for item box bof
				$option = $item->getOrderItem()->getProductOptionByCode();
				$productCount = 1;	
				
				 if(count($option['bundle_options']) != 0) {
				 
					// invoice_display
					$childItems = $this->getChilds($item);				
					foreach ($childItems as $chldItem) {
						$sku = $chldItem->getSku();
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku); 		
						if($product['custom_invoice_display'] != '') { 
						 
						$customeInvoice =  $product['custom_invoice_display']; 
						$customeInvoiceArray = str_split($customeInvoice, 30);
						$custome_invoice_display = count($customeInvoiceArray);	
						}
						else {
							$custome_invoice_display = 0;
						}

						//for product list display
						$$custom_productlist_display = 0;
						if($product['custom_product_list1'] != '') {
							$custom_product_list1 = "- ".$product->getCustomProductList1();
							$customProductlist1Array = str_split($custom_product_list1, 50);
							$custom_productlist_display = count($customProductlist1Array) + $custom_productlist_display;	
						}
						if($product['custom_product_list2'] != ''){
							$custom_product_list2 = "- ".$product->getCustomProductList2();
							$customProductlist2Array = str_split($custom_product_list2, 50);
							$custom_productlist_display = count($customProductlist2Array) + $custom_productlist_display;
						}
						if($product['custom_product_list3'] != ''){
							$custom_product_list3 = "- ".$product->getCustomProductList3();
							$customProductlist3Array = str_split($custom_product_list3, 50);
							$custom_productlist_display = count($customProductlist3Array) + $custom_productlist_display;
						}
						if($product['custom_product_list4'] != ''){
							$custom_product_list4 = "- ".$product->getCustomProductList4();
							$customProductlist4Array = str_split($custom_product_list4, 50);
							$custom_productlist_display = count($customProductlist4Array) + $custom_productlist_display;
						}
						if($product['custom_product_list5'] != ''){
							$custom_product_list5 = "- ".$product->getCustomProductList5();
							$customProductlist5Array = str_split($custom_product_list5, 50);
							$custom_productlist_display = count($customProductlist5Array) + $custom_productlist_display;
						}
						if($product['custom_product_list6'] != ''){
							$custom_product_list6 = "- ".$product->getCustomProductList6();
							$customProductlist6Array = str_split($custom_product_list6, 50);
							$custom_productlist_display = count($customProductlist6Array) + $custom_productlist_display;
						}
						if($product['custom_product_list7'] != ''){
							$custom_product_list7 = "- ".$product->getCustomProductList7();
							$customProductlist7Array = str_split($custom_product_list7, 50);
							$custom_productlist_display = count($customProductlist7Array) + $custom_productlist_display;
						}
							
						
					}
				 
									
					// parent name
					$parentName =  str_split($item->getOrderItem()->getName(), 30);
					$parentNameArrayCount = count($parentName);
					// parent sku
					$productData = Mage::getModel('catalog/product')->load($item->getOrderItem()->getProductId());
					$parentSku = $productData['sku'];
					$parentSku =  str_split($parentSku, 10);
					$parentSkuArrayCount = count($parentSku);
					
					$finalUpperRow = array($parentNameArrayCount,$parentSkuArrayCount);
					$finalUpperRowCount = max($finalUpperRow);
					
					$finalCOunt = 1;
					// sku string
					//$skuString = $item->getOrderItem()->getSku();
					//$skuArray = str_split($skuString, 10);
					//$skuArrayCount = count($skuArray);
					
					// title				
					
					$countOfChildernTitle = count($option['bundle_options']);
					foreach($option['bundle_options'] as $optionsArray) {
						foreach($optionsArray['value'] as $optionsTitle) {
							
							$titleArray = str_split($optionsTitle['title'], 30);
							$titleCount +=  count($titleArray);							
						}
						$countOfChilderproduct += count($optionsArray['value']);	
					}
					
					// row total string
					$rowTotal =  $item->getOrderItem()->getRowTotal();
					$rowTotalArray = str_split($rowTotal, 8);
					$rowTotalArrayCount = count($rowTotalArray);
					
					// price string
					/*$priceTotal =  $item->getOrderItem()->getPrice();
					$priceTotalArray = str_split($priceTotal, 8);
					$priceTotalArrayCount = count($priceTotalArray);
					
					// tax string
					$taxTotal =  $item->getOrderItem()->getTaxAmount();
					$taxTotalArray = str_split($taxTotal, 8);
					$taxTotalArrayCount = count($taxTotalArray);
					*/
					
					$finalTitleTotalLower = $titleCount+$custome_invoice_display + $custom_productlist_display;
					
					/* $findLMax = array($titleCount,$rowTotalArrayCount,$priceTotalArrayCount,$taxTotalArrayCount);
					$maxLValu = max($findLMax); 
					$finalLowerCOunt = $finalUpperRowCount+$maxLValu;
					*/
					
					//$finalCOunt = $finalUpperRowCount+$titleCount+$countOfChilderproduct+$rowTotalArrayCount+1;
					
					// new
					//$finalCOunt = $finalUpperRowCount+$countOfChilderproduct+$finalLowerCOunt+$countOfChilderproduct;					
					//$multippleAmount = 17;
					
					// old
					
					$finalCOunt = $finalUpperRowCount+$finalTitleTotalLower+$countOfChilderproduct+$skuArrayCount+$rowTotalArrayCount+$countOfChilderproduct;
					$multippleAmount = 17;
				}
				else if(count($option['attributes_info']) != 0) {
					$finalCOunt =1;					
					
					$skuString = $option['sku'];
					$skuArray = str_split($skuString, 10);
					$skuArrayCount = count($skuArray);
					
					$optionsTitle =  $option['simple_name'];
					$titleArray = str_split($optionsTitle, 30);
					$titleCount =  count($titleArray);
					
					// row total string
					$rowTotal =  $item->getOrderItem()->getRowTotal();
					$rowTotalArray = str_split($rowTotal, 8);
					$rowTotalArrayCount = count($rowTotalArray);
					
					// price string
					/*$priceTotal =  $item->getOrderItem()->getPrice();
					$priceTotalArray = str_split($priceTotal, 8);
					$priceTotalArrayCount = count($priceTotalArray);
					
					// tax string
					$taxTotal =  $item->getOrderItem()->getTaxAmount();
					$taxTotalArray = str_split($taxTotal, 8);
					$taxTotalArrayCount = count($taxTotalArray);
					*/
					$countOfChildernTitle = count($option['attributes_info']);					
					$countOfChilderproduct = $countOfChildernTitle*2;	
					
					$finalCOunt = $titleCount+$countOfChilderproduct+$skuArrayCount+$rowTotalArrayCount;
					
					//$maxValu = max(array($skuArrayCount,$titleCount,$rowTotalArrayCount,$priceTotalArrayCount,$taxTotalArrayCount));
					//$finalCOunt = $maxValu;
					
					$multippleAmount = 15;
				}
				else {
					
					$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$item->getOrderItem()->getSku()); 
					if($product['custom_invoice_display'] != '') {
						$customeInvoice =  $product['custom_invoice_display'];
						$customeInvoiceArray = str_split($customeInvoice, 30);
						$custome_invoice_display = count($customeInvoiceArray);				
						
					}
					else {
						$custome_invoice_display = 0;
					}
					//echo $custome_invoice_display;exit;
					//for product list display
						
						$custom_productlist_display = 0;
						if($product['custom_product_list1'] != '') { 
							$custom_product_list1 = "- ".$product->getCustomProductList1();
							$customProductlist1Array = str_split($custom_product_list1, 50);
							$custom_productlist_display = count($customProductlist1Array) + $custom_productlist_display;
						}
						if($product['custom_product_list2'] != ''){
							$custom_product_list2 = "- ".$product->getCustomProductList2();
							$customProductlist2Array = str_split($custom_product_list2, 50);
							$custom_productlist_display = count($customProductlist2Array) + $custom_productlist_display;
						}
						if($product['custom_product_list3'] != ''){
							$custom_product_list3 = "- ".$product->getCustomProductList3();
							$customProductlist3Array = str_split($custom_product_list3, 50);
							$custom_productlist_display = count($customProductlist3Array) + $custom_productlist_display;
						}
						if($product['custom_product_list4'] != ''){
							$custom_product_list4 = "- ".$product->getCustomProductList4();
							$customProductlist4Array = str_split($custom_product_list4, 50);
							$custom_productlist_display = count($customProductlist4Array) + $custom_productlist_display;
						}
						if($product['custom_product_list5'] != ''){
							$custom_product_list5 = "- ".$product->getCustomProductList5();
							$customProductlist5Array = str_split($custom_product_list5, 50);
							$custom_productlist_display = count($customProductlist5Array) + $custom_productlist_display;
						}
						if($product['custom_product_list6'] != ''){
							$custom_product_list6 = "- ".$product->getCustomProductList6();
							$customProductlist6Array = str_split($custom_product_list6, 50);
							$custom_productlist_display = count($customProductlist6Array) + $custom_productlist_display;
						}
						if($product['custom_product_list7'] != ''){
							$custom_product_list7 = "- ".$product->getCustomProductList7();
							$customProductlist7Array = str_split($custom_product_list7, 50);
							$custom_productlist_display = count($customProductlist7Array) + $custom_productlist_display;
						}
						//checking the esn
						if($item->getEsnNumber() != ''){
							$esnnumber[] = 'MEID :: ';
							$esnnumber1 = explode(",", $item->getEsnNumber());
							$meid = array_merge($esnnumber,$esnnumber1);
							$custom_productlist_display = count($meid ) + $custom_productlist_display;
						
						}
						//end checking the esn
						//echo "endh";exit;
					// sku string
					$skuString = $item->getOrderItem()->getSku();
					
					$skuArray = str_split($skuString, 10);
					$skuArrayCount = count($skuArray);
					// name string
					$optionsTitle =  $item->getOrderItem()->getName();
					$titleArray = str_split($optionsTitle, 30);
					$titleCount =  count($titleArray);	
					$totalTitleCount = $titleCount+$custome_invoice_display + $custom_productlist_display;						
					
					// row total string
					$rowTotal =  $item->getOrderItem()->getRowTotal();
					$rowTotalArray = str_split($rowTotal, 8);
					$rowTotalArrayCount = count($rowTotalArray);
					
					// price string
					/*$priceTotal =  $item->getOrderItem()->getPrice();
					$priceTotalArray = str_split($priceTotal, 8);
					$priceTotalArrayCount = count($priceTotalArray);
					
					// tax string
					$taxTotal =  $item->getOrderItem()->getTaxAmount();
					$taxTotalArray = str_split($taxTotal, 8);
					$taxTotalArrayCount = count($taxTotalArray);
					*/
					//$findMax = array($skuArrayCount,$totalTitleCount,$rowTotalArrayCount,$priceTotalArrayCount,$taxTotalArrayCount);
					//$maxValu = max($findMax); 
					
					//$finalCOunt = $skuArrayCount+$titleCount+$custome_invoice_display+$rowTotalArrayCount;
					//$finalCOunt = $maxValu;
					
					$finalCOunt = $skuArrayCount+$titleCount+$custome_invoice_display+$custom_productlist_display+$rowTotalArrayCount;
					
					$multippleAmount = 15;
				}
				
				//589.4
				
				/* if($this->y > 589) {
					//$page = $this->newPage(array('table_header' => true,'fcount'=>$finalCOunt,'moption'=>$multippleAmount));
					$page = $this->newPage(array('table_header' => true),$finalCOunt,$multippleAmount);
				}
				*/
				
				$page->setFillColor(new Zend_Pdf_Color_RGB(1, 1, 1));
				$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
				$page->setLineWidth(0.5);	
				
				$rectY = $this->y -($finalCOunt*$multippleAmount);
				//echo $rectY;exit;
				if($rectY > 570) $rectY = 570;
				//$page->drawRectangle(25, $this->y,570, $rectY);
				
				$page->drawRectangle(25,$this->y,570,$rectY);
				//$page->drawLine(25, $this->y, 570, $this->y);
				$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
				$this->y -= 20;
				// added for item box eof			
				
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
				
            }
			//exit;
			if ($this->y < 55) {
$page = $this->newPage(array(�table_header� => true));
} 
			//echo $this->y; exit;
			
			//added grand total in words
			$gprice = str_replace(",","",($invoice->getBaseGrandTotal()+$invoice->getCodFee()));
			$grandTotalInWords = "Grand Total in words: Rs. ".ucwords($this->convertNumber($gprice)).Mage::helper('sales')->__(' only');
			//$grandTotalInWords = "Total Payable: Rs. ".$gprice;
			
			// price grandtotal string					
			$grandTotalArray = Mage::helper('core/string')->str_split($grandTotalInWords, 110);
			$grandTotalArrayCount = count($grandTotalArray);		
			$y = $this->y + 1;
			$this->_setFontBold($page , 10);
			
			// added for total box bof	
			$this->y -= -5;
			$page->setFillColor(new Zend_Pdf_Color_RGB(1, 1, 1));
			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
			$page->setLineWidth(0.5);			
			$page->drawRectangle(25, $this->y,570, $this->y-(80+($grandTotalArrayCount*70)));
			$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));	
			// added for total box eof		
			//$this->y -= -20;
			
			 /* Add totals */		 
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
			$this->y = $this->y;
			foreach($grandTotalArray as $grandTotalVal) {
			$i = 0;			
			$y = $this->y+$i;
			//$page->drawText($grandTotalVal,310 ,$y, 'UTF-8');
			
			$font = $this->_setFontBold($page, 10);						
			$page->drawText($grandTotalVal, $this->getAlignRight($grandTotalVal, 300, 265, $font, 10,0), $y, 'UTF-8');      
			$i = $i+15;
			$this->y = $y - $i;			
			}			
			
			if($displayText == "Invoice"){
				$displayMsg = "invoice";
			}
			
			if($displayText == "Receipt"){
				$displayMsg = "receipt";
			}
			
			//exit;			
			// added For Footer bof	
			$this->y = $this->y-5;
			$page->setFillColor(new Zend_Pdf_Color_RGB(1, 1, 1));
			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
			$page->setLineWidth(0.5);			
			$page->drawRectangle(25, $this->y,570, $this->y-40);
			$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));	
			$page->drawText('Computer Generated '.$displayMsg.' no signature or seal required.', $this->getAlignCenter('This is a computer generated '.$displayMsg.'.', 1, 470, $font, 9,0), $this->y-20, 'UTF-8'); 
			$this->y -= -20;  
			// added for Footer eof		
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array(),$fcount,$moption)
    {
		
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;		
	
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
			$page->setFillColor(new Zend_Pdf_Color_RGB(1, 1, 1));
				$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
				$page->setLineWidth(0.5);	
				$this->y +=20;
				$page->drawRectangle(25, $this->y,570, 650);
				//$page->drawLine(25, $this->y, 570, $this->y);
				$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
				$this->y -= 20;
        }
		
        return $page;
    }	
	
	
	/**
     * Insert address to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
   
	/*protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $font = $this->_setFontArial($page, 9);
        $page->setLineWidth(2);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
		foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $key=>$value){
			if ($value !== '') {
			    if($key == 0){
					$font = $this->_setFontArial($page, 12);
					$font = $this->_setFontBold($page,12);
				}
				else{
					$font = $this->_setFontArial($page, 9);
				}
				
				$value = preg_replace('/<br[^>]*>/i', "\n", $value);
				foreach (Mage::helper('core/string')->str_split($value, 60, true, true) as $_value) {
					if($key == 0){
						$page->drawText(trim(strip_tags($_value)),
						$this->getAlignRight($_value, 130, 446, $font, 9,5),
						$top,
						'UTF-8');						
					}
					else{
						$page->drawText(trim(strip_tags($_value)),
							$this->getAlignRight($_value, 130, 445, $font, 9,5),
							$top,
							'UTF-8');
					}
					$top -= 15;
				}
            }
        }
        $this->y = ($this->y > $top) ? $top : $this->y;
    }*/
	
	/**
     * Insert order to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $obj
     * @param bool $putOrderId
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true,$displayText)
    {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
   
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
        $font = $this->_setFontArial($page, 9);
		$this->_setFontBold($page, 11);
		
		
		$orderIdText = Mage::helper('sales')->__('Order # ') . $order->getRealOrderId();
		$orderDateText = Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false);
        if ($putOrderId) {
			$page->drawText($orderIdText .', '. $orderDateText, 35, ($top -= 15), 'UTF-8');
        }
		
		if($displayText == "Invoice"){
			$displayHeader = "TAX INVOICE";
		}
		
		if($displayText == "Receipt"){
			$displayHeader = "TAX RECEIPT";
		}
		
		
			$font = $this->_setFontBold($page, 9);
			$this->y -= 15;
			$page->drawText($displayHeader, $this->getAlignCenter($displayHeader, 50, 445, $font, 9,0), $this->y-10, 'UTF-8'); 
			$this->y -= 20;
		
       // $page->drawText($orderDateText,$this->getAlignRight($orderDateText, 130, 440, $font, 10,0),($top -= 10),'UTF-8');

        $top -= 20;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, ($top - 25));
        $page->drawRectangle(275, $top, 570, ($top - 25));

        /* Calculate blocks info */
	
		
        /* Billing Address */
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
       
        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            $shippingMethod  = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(Mage::helper('sales')->__('Billed to:'), 35, ($top - 15), 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('Ship to:'), 285, ($top - 15), 'UTF-8');
        } 

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontArial($page, 9);
        $this->y = $top - 40;
        $addressesStartY = $this->y;	
		
		
        foreach ($billingAddress as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 40, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $text = array();
                    foreach (Mage::helper('core/string')->str_split($value, 40, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;           

           

            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontArial($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments   = $this->y - 15;
        }
        else {
            $yPayments   = $addressesStartY;
            $paymentLeft = 285;
        }

      
    }
	
	/**
     * Draw lines
     *
     * draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 9 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontArial($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontArial($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }
                        $page->drawText($part, $feed, $this->y-$top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }
	
	/**
     * Set font as Arial
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontArial($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/arial.ttf');
        $object->setFont($font, $size);
        return $font;
    }
	 /**
     * Insert logo to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 825; //top border of the page
                $widthLimit  = 175; //half of the page width
                $heightLimit = 270; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 25;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }
	
	/**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/arialbd.ttf');
        $object->setFont($font, $size);
        return $font;
    }
	
	
	 /**
     * Format address
     *
     * @param  string $address
     * @return array
     */
    protected function _formatAddress($address)
    {
        $return = array();
        foreach (explode('|', $address) as $str) {
            foreach (Mage::helper('core/string')->str_split($str, 40, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }
	
	/**
     * Calculate address height
     *
     * @param  array $address
     * @return int Height
     */
    protected function _calcAddressHeight($address)
    {
        $y = 0;
        foreach ($address as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 40, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $y += 15;
                }
            }
        }
        return $y;
    }

	/**
     * Getting all available childs for Invoice, Shipmen or Creditmemo item
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getChilds($item)
    {
        $_itemsArray = array();

        if ($item instanceof Mage_Sales_Model_Order_Invoice_Item) {
            $_items = $item->getInvoice()->getAllItems();
        } else if ($item instanceof Mage_Sales_Model_Order_Shipment_Item) {
            $_items = $item->getShipment()->getAllItems();
        } else if ($item instanceof Mage_Sales_Model_Order_Creditmemo_Item) {
            $_items = $item->getCreditmemo()->getAllItems();
        }

        if ($_items) {
            foreach ($_items as $_item) {
                $parentItem = $_item->getOrderItem()->getParentItem();
                if ($parentItem) {
                    $_itemsArray[$parentItem->getId()][$_item->getOrderItemId()] = $_item;
                } else {
                    $_itemsArray[$_item->getOrderItem()->getId()][$_item->getOrderItemId()] = $_item;
                }
            }
        }

        if (isset($_itemsArray[$item->getOrderItem()->getId()])) {
            return $_itemsArray[$item->getOrderItem()->getId()];
        } else {
            return null;
        }
    }
	public function convertNumber($num)
	{
	   list($num, $dec) = explode(".", $num);

	   $output = "";

	   if($num{0} == "-")
	   {
		  $output = "negative ";
		  $num = ltrim($num, "-");
	   }
	   else if($num{0} == "+")
	   {
		  $output = "positive ";
		  $num = ltrim($num, "+");
	   }
	   
	   if($num{0} == "0")
	   {
		  $output .= "zero";
	   }
	   else
	   {
		  $num = str_pad($num, 36, "0", STR_PAD_LEFT);
		  $group = rtrim(chunk_split($num, 3, " "), " ");
		  $groups = explode(" ", $group);

		  $groups2 = array();
		  foreach($groups as $g) $groups2[] = $this->convertThreeDigit($g{0}, $g{1}, $g{2});

		  for($z = 0; $z < count($groups2); $z++)
		  {
			 if($groups2[$z] != "")
			 {
				$output .= $groups2[$z].$this->convertGroup(11 - $z).($z < 11 && !array_search('', array_slice($groups2, $z + 1, -1))
				 && $groups2[11] != '' && $groups[11]{0} == '0' ? " and " : ", ");
			 }
		  }

		  $output = rtrim($output, ", ");
	   }

	   if($dec > 0)
	   {
		  $output .= " and paise ";
		  for($i = 0; $i < strlen($dec); $i++) $output .= " ".$this->convertDigit($dec{$i});
	   }
	    if($dec == 0)
	   {
		  $output .= " and paise ";
		  $output .= "Zero";
	   }

	   return $output;
	}
	public function convertGroup($index)
	{
	   switch($index)
	   {
		  case 11: return " decillion";
		  case 10: return " nonillion";
		  case 9: return " octillion";
		  case 8: return " septillion";
		  case 7: return " sextillion";
		  case 6: return " quintrillion";
		  case 5: return " quadrillion";
		  case 4: return " trillion";
		  case 3: return " billion";
		  case 2: return " million";
		  case 1: return " thousand";
		  case 0: return "";
	   }
	}
	public function convertThreeDigit($dig1, $dig2, $dig3)
	{
	   $output = "";

	   if($dig1 == "0" && $dig2 == "0" && $dig3 == "0") return "";

	   if($dig1 != "0")
	   {
		  $output .= $this->convertDigit($dig1)." hundred";
		  if($dig2 != "0" || $dig3 != "0") $output .= " and ";
	   }

	   if($dig2 != "0") $output .= $this->convertTwoDigit($dig2, $dig3);
	   else if($dig3 != "0") $output .= $this->convertDigit($dig3);

	   return $output;
	}

	public function convertTwoDigit($dig1, $dig2)
	{
	   if($dig2 == "0")
	   {
		  switch($dig1)
		  {
			 case "1": return "ten";
			 case "2": return "twenty";
			 case "3": return "thirty";
			 case "4": return "forty";
			 case "5": return "fifty";
			 case "6": return "sixty";
			 case "7": return "seventy";
			 case "8": return "eighty";
			 case "9": return "ninety";
		  }
	   }
	   else if($dig1 == "1")
	   {
		  switch($dig2)
		  {
			 case "1": return "eleven";
			 case "2": return "twelve";
			 case "3": return "thirteen";
			 case "4": return "fourteen";
			 case "5": return "fifteen";
			 case "6": return "sixteen";
			 case "7": return "seventeen";
			 case "8": return "eighteen";
			 case "9": return "nineteen";
		  }
	   }
	   else
	   {
		  $temp = $this->convertDigit($dig2);
		  switch($dig1)
		  {
			 case "2": return "twenty-$temp";
			 case "3": return "thirty-$temp";
			 case "4": return "forty-$temp";
			 case "5": return "fifty-$temp";
			 case "6": return "sixty-$temp";
			 case "7": return "seventy-$temp";
			 case "8": return "eighty-$temp";
			 case "9": return "ninety-$temp";
		  }
	   }
	}

	public function convertDigit($digit)
	{
	   switch($digit)
	   {
		  case "0": return "zero";
		  case "1": return "one";
		  case "2": return "two";
		  case "3": return "three";
		  case "4": return "four";
		  case "5": return "five";
		  case "6": return "six";
		  case "7": return "seven";
		  case "8": return "eight";
		  case "9": return "nine";
	   }
	}

	/*protected function convert_number($number) 
	{ 
		if (($number < 0) || ($number > 999999999)) 
		{ 
		throw new Exception("Number is out of range");
		} 

		$Gn = floor($number / 1000000);  // Millions (giga) 
		$number -= $Gn * 1000000; 
		$kn = floor($number / 1000);     // Thousands (kilo) 
		$number -= $kn * 1000; 
		$Hn = floor($number / 100);      // Hundreds (hecto) 
		$number -= $Hn * 100; 
		$Dn = floor($number / 10);       // Tens (deca) 
		$n = $number % 10;               // Ones  

		$res = ""; 

		if ($Gn) 
		{ 
			$res .= $this->convert_number($Gn) . " Million"; 
		} 

		if ($kn) 
		{ 
			$res .= (empty($res) ? "" : " ") . 
				$this->convert_number($kn) . " Thousand"; 
		} 

		if ($Hn) 
		{ 
			$res .= (empty($res) ? "" : " ") . 
				$this->convert_number($Hn) . " Hundred"; 
		} 

		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
			"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
			"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
			"Nineteen"); 
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
			"Seventy", "Eigthy", "Ninety"); 

		if ($Dn || $n) 
		{ 
			if (!empty($res)) 
			{ 
				$res .= " and "; 
			} 

			if ($Dn < 2) 
			{ 
				$res .= $ones[$Dn * 10 + $n]; 
			} 
			else 
			{ 
				$res .= $tens[$Dn]; 

				if ($n) 
				{ 
					$res .= "-" . $ones[$n]; 
				} 
			} 
		} 

		if (empty($res)) 
		{ 
			$res = "zero"; 
		} 

		return $res; 
	} */
	
	
	 protected function insertTotals($page, $source){
        $order = $source->getOrder();
		$invoicePrefix = substr( $source->getIncrementId(), 0, 1);
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
		if($source->getCodFee()){
			$codFee = $source->getCodFee();
		}
		if($order->getCodFee()){
			$codFee = $order->getCodFee();
		}
		
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
					if($totalData['label'] == "Subtotal:"){ 
						$totalData['label'] = "Subtotal (Excl. Tax):";
					}
					if($codFee != '' && $codFee !=NULL && $totalData['label'] == "Grand Total:"){
						if($order->getCod()){
						$totalData['label'] = "Balance Amount:";
						$totalData['amount'] = $order->formatPriceTxt($source->getBaseGrandTotal());						
						$Modified_GT = $source->getBaseGrandTotal()  + $codFee ;
						}
					}
					if($totalData['label'] == "Tax:"){ continue; }

					$pos = strpos($totalData['label'], 'Service');
					if (($pos !== false) && ($invoicePrefix != 'W')) { continue; }

					$pos = strpos($totalData['label'], 'CST');
					if (($pos !== false) && ($invoicePrefix == 'W')) { continue; }

					$pos = strpos($totalData['label'], 'VAT');
					if (($pos !== false) && ($invoicePrefix == 'W')) { continue; }
					
					$shipping_address = $order->getShippingAddress();
					
					if($order->getPayment()->getMethodInstance()->getCode() == "cashondelivery"){
						if($totalData['label'] == "Pending Amount:"){
							$totalData['label'] = "Advance Amount:";
						}
					
						if($totalData['label'] == "Total Payable:"){
							$totalData['label'] = "Total Due:";
						}	
					}
					
					if($shipping_address->getRegion() != "Maharashtra"){
						if($totalData['label'] == 'Vat:'){
							$totalData['label'] = 'CST:';
						}
					}
					                 
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $totalData['label'],
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                    );
                }
            }
        }
        
		if($order->getPayment()->getMethodInstance()->getCode() == "cashondelivery"){
			$gprice = str_replace(",","",($source->getBaseGrandTotal()+$source->getCodFee()));
			$lineBlock['lines'][] = array(
				array(
					'text'      => 'Total Payable:',
					'feed'      => 475,
                    'align'     => 'right',
                    'font_size' => 10,
                    'font'      => 'bold'
				),
                array(
                	'text' => Mage::helper('core')->currency($gprice,true,false),
					//'text'      => 'Rs.'.$gprice,
                    'feed'      => 565,
                    'align'     => 'right',
                    'font_size' => 10,
                    'font'      => 'bold'
                    ),
				);	
		}
		
		if($order->getCod()){
		if($codFee != '' && $codFee !=NULL){
			$lineBlock['lines'][] = array(
                        array(
                            'text'      => 'Grand Total:',
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => 10,
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $order->formatPriceTxt($Modified_GT),
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => 10,
                            'font'      => 'bold'
                        )
			);
		}
		}

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
}
