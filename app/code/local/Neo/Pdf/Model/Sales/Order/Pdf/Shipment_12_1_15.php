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
 * Sales Order Shipment PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Neo_Pdf_Model_Sales_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{

	protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y-15);
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
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 270,
			'font'  => 'bold',
           	'align' => 'left'
        );		

		   $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Price'),
            'feed'  => 320,
			'font'  => 'bold',
            'align' => 'left'
        );		
		
        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 370,
			'font'  => 'bold',
            'align' => 'left'
        );
   
		
		$lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Subtotal (Excl. Tax)'),
            'feed'  => 450,
			'font'  => 'bold',
            'align' => 'left'
        );


        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
	
    /**
     * Insert order to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $obj
     * @param bool $putOrderId
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }
		
		$tracks = array();
		if ($shipment) {
			$tracks = $shipment->getAllTracks();
		}

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
        $this->_setFontRegular($page, 10);

        if ($putOrderId) {
            $page->drawText(
                Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(), 35, ($top -= 30), 'UTF-8'
            );
        }
        $page->drawText(
            Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
                $order->getCreatedAtStoreDate(), 'medium', false
            ),
            35,
            ($top -= 15),
            'UTF-8'
        );
		
		/* START -- Cod Implementation-- Sanchi*/
		$DueAmount = $order->formatPriceTxt(0);

		$payment_code =  $order->getPayment()->getMethod();		
		$paymentmethod = Mage::getStoreConfig('payment/'.$payment_code.'/title');
		//if($order->getCod()){
		if($order->getPayment()->getMethodInstance()->getCode() == "cashondelivery"){
			$invoiceObj = Mage::getModel("sales/order_invoice")->load($order->getId(),"order_id");
			//if($order->getStatus() != "complete"){				
                // commented by pradeep sanku on 7th April 2015 below line as their was wrong value in the due amount for order total above 50000
				//$DueAmount = $order->formatPriceTxt($order->getGrandTotal());
                // commented by pradeep sanku on 7th April 2015 above line as their was wrong value in the due amount for order total above 50000

                // added by pradeep sanku on 7th April 2015 as their is wrong due value in the due amount for the order total above 50000
                if($order->getCodFee()){
                    $DueAmount = $order->formatPriceTxt($order->getGrandTotal() - $order->getCodFee());
                }else{
                    $DueAmount = $order->formatPriceTxt($order->getGrandTotal());
                }
                // added by pradeep sanku on 7th April 2015 as their is wrong due value in the due amount for the order total above 50000
			//}		
			$page->drawText('Invoice #', 250, ($top + 30), 'UTF-8');
			$page->drawText($invoiceObj->getIncrementId(), 290, ($top + 30), 'UTF-8');
			$page->drawText('Invoice Date: ', 360, ($top + 30), 'UTF-8');			
			$page->drawText($invoiceObj->getCreatedAt(), 420, ($top + 30), 'UTF-8');
			$page->drawText('Collect Cash on Delivery', 250, ($top + 15), 'UTF-8');
			$page->drawText('Due Amount: ', 250, ($top), 'UTF-8');		
			$page->drawText($DueAmount, 310, ($top), 'UTF-8');
			$page->drawText('Payment Method:  ', 360, ($top + 15), 'UTF-8');
			$page->drawText($paymentmethod, 440, ($top + 15), 'UTF-8');
		}else{
			//$invoiceObj = Mage::getModel("sales/order_invoice")->load($shipment->getInvoiceId());
            $invoiceObj = Mage::getModel("sales/order_invoice")->load($order->getId(),"order_id");
			$page->drawText('Invoice #', 250, ($top + 30), 'UTF-8');
			$page->drawText($invoiceObj->getIncrementId(), 290, ($top + 30), 'UTF-8');			
			$page->drawText('Invoice Date: ', 250, ($top + 15), 'UTF-8');		
			$page->drawText($invoiceObj->getCreatedAt(), 310, ($top + 15), 'UTF-8');
			$page->drawText('PREPAID', 450, ($top + 30), 'UTF-8');
			$page->drawText('Payment Method:  ', 250, ($top), 'UTF-8');
			$page->drawText($paymentmethod, 330, ($top), 'UTF-8');
		
		}
		/* END */
		
		
        $top -= 10;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, ($top - 25));
        $page->drawRectangle(275, $top, 570, ($top - 25));

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

        /* Payment */
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            $shippingMethod  = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(Mage::helper('sales')->__('Ship to:'), 35, ($top - 15), 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, ($top - 15) , 'UTF-8');
        /*if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('Ship to:'), 285, ($top - 15), 'UTF-8');
        } else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, ($top - 15), 'UTF-8');
        }*/

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
			$addressesHeight = $addressesHeight;
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight-25);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        /*foreach ($billingAddress as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }*/

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $text = array();
                    foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }
			foreach($tracks as $track){
				$CarrierCode = $track->getCarrierCode();
				if($CarrierCode == 'bluedart_apex' || $CarrierCode == 'bluedart_surface'){					
					$read = Mage::getSingleton('core/resource')->getConnection('core_read');
					$sql  = "SELECT carea,cscrcd FROM bluedart_pincodes WHERE pincode = ".$order->getShippingAddress()->getPostcode(); 
					$details = $read->fetchRow($sql);
					$this->_setFontBold($page, 16);
					$page->drawText('-'.$details['carea'].'/'.$details['cscrcd'], 400, $this->y+15, 'UTF-8');
				}
			}

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            /*$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y-25);
            $page->drawinsertAddressRectangle(275, $this->y, 570, $this->y-25);

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            //$page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
            //$page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');
			*/
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments   = $this->y - 15;
        }
        else {
            $yPayments   = $addressesStartY;
            $paymentLeft = 285;
        }
		
		$addressesEndY = $this->y;
		
		// 1st sm
		if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25,  ($top - 25), 25,  $yPayments);
            $page->drawLine(570, ($top - 25), 570, $yPayments);
            $page->drawLine(25,  $yPayments,  570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
        	$this->y = $addressesStartY;
            $topMargin = 15;
            $methodStartY = $this->y;
            //$this->y     -= 15;

            foreach (Mage::helper('core/string')->str_split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " "
                . $order->formatPriceTxt($order->getShippingAmount()) . ")";

            $page->drawText($totalShippingChargesText, 285, $this->y, 'UTF-8');
            $yShipments -= $topMargin + 10;

            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('AWB'), 410, $yShipments - 7, 'UTF-8');
				  
                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {

                    $CarrierCode = $track->getCarrierCode();
                    //$CarrierTitle = $track->getTitle();
                    if ($CarrierCode != 'custom') {
                        //$carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                        $carrierTitle = $track->getTitle();						
                    } else {
                        $carrierTitle = Mage::helper('sales')->__('Custom Value');
                    }

                    //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
					$truncatedTitle = strtoupper($truncatedTitle);
					$this->insertBarCode($page, $order->getIncrementId(), 285, $yShipments-30,"Order ");
                    // if($CarrierTitle == "Speed Post Manual"){
                    //     $page->drawText("BNPL A/C MR/DA/NE/SP/1064 /2015-16 KURLA BPC MUMBAI-704000", 285, $yShipments-50 , 'UTF-8');
                    // }
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
					$this->_setFontBold($page, 12);
                    $page->drawText($truncatedTitle, 292, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments , 'UTF-8');
					$this->_setFontRegular($page, 8);
                    $yShipments -= $topMargin - 5;
                    /**
                     * Commented the coondition for printing Awb
                     * @author Deepak M
                     */
                    //if($CarrierCode == 'bluedart_apex' || $CarrierCode == 'bluedart_surface' || $CarrierCode == 'bluedart_dpcourier' || $CarrierCode == 'bluedart_ecomprepaid' || $CarrierCode == 'bluedart_cod' || $CarrierCode == 'ecomexpress_prepaid' || $CarrierCode == 'ecomexpress_cod'){
                    $this->insertBarCode($page, $track->getNumber(), 418, $yShipments+50,"AWB ");
					//}
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            //$page->drawLine(25,  $methodStartY, 25,  $currentY); //left
            //$page->drawLine(25,  $currentY,     570, $currentY); //bottom
            //$page->drawLine(570, $currentY,     570, $methodStartY); //right

            $this->y = $currentY;
            $this->y -= 0;
        }
    }
	

    /**
     * insert the barcode into the pdf
     * 
     * */
    protected function insertBarCode($page, $AwbNo, $x, $y,$order="")
    {
        if ($AwbNo) {
            $this->_setFontBarcode($page, 30);
            $page->drawText('*'.$AwbNo.'*', $x, $y);       
             
            $this->_setFontRegular($page, 16);
            $page->drawText($order.$AwbNo, $x+30, $y-15);            
        }
    }
	

    /**
     * font for the barcode
     * 
     * */
    protected function _setFontBarcode($object, $size = 10)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/FRE3OF9X.TTF');
        $object->setFont($font, $size);
        return $font;
    }
	
	public function getPdf($shipments = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');
        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());

            // added by pradeep sanku on the 38 Aug 2015 as a part of speed post account
            $tracks = $shipment->getAllTracks();
            foreach ($tracks as $track) {
                $CarrierTitle = $track->getTitle();
                if($CarrierTitle == "Speed Post Manual"){
                    $page->drawText("Speed Post", 250, $this->y+50, 'UTF-8');
                    $page->drawText("BNPL A/C", 250, $this->y+40, 'UTF-8');
                    $page->drawText("MR/DA/NE/SP/1064 /2015-16", 250, $this->y+30, 'UTF-8');
                    $page->drawText("KURLA BPC MUMBAI-400070", 250, $this->y+20, 'UTF-8');       
                }
            }

            // added by pradeep sanku on the 30 oct 2014
            //$page->drawText("TIN NO # 27370085832V, Service Tax #: AACFK0693DST002", 323, $this->y, 'UTF-8');
            $page->drawText("VAT TIN NO # 27530926734V,CST NO # 27530926734C,Service Tax #: AAKC A6979MSD003", 200, $this->y, 'UTF-8');
            $this->y -= 15;
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId()
            );
            /* Add table */
            $this->_drawHeader($page);
			
			
            /* Add body */
/*
			$invoice = Mage::getModel("sales/order_invoice")->load($shipment->getInvoiceId());
			foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
*/
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
			}
            if ($this->y < 55) {
			$page = $this->newPage(array('table_header' => true));
			} 
						//echo $this->y; exit;			
			$y = $this->y + 1;
			$this->_setFontBold($page , 10);
			$this->y -= -5;
			$page->setFillColor(new Zend_Pdf_Color_RGB(1, 1, 1));
			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
			$page->setLineWidth(0.5);
			$page->drawRectangle(25, $this->y,570, $this->y-100);
			$page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

			
			 /* Add totals */		 
            //$this->insertTotals($page, $shipment,$shipment->getIncrementId());
            $this->insertTotals($page, $order,$shipment->getIncrementId());
            //$this->insertTotals($page, $invoice,$shipment->getIncrementId());
			$this->y = $this->y;
			$page->drawText("If undelivered return to Origin.", $x+30, $y-120);
        }
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }
	
	protected function insertTotals($page, $source ,$ship_IncrementId){
		$order = $source;
        //Mage::log($order->getData(),null,'helll.log');
        //$order = $source->getOrder();
		$invoicePrefix = substr($source->getIncrementId(), 0, 1);
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
				//Mage::log(get_class($total),null,'aaaa.log');
                foreach ($total->getTotalsForDisplay() as $totalData) {
					$dbconn = Mage::getSingleton('core/resource')->getConnection('core_write');
					$sql1 = "SELECT order_id FROM sales_flat_shipment WHERE increment_id = '".$ship_IncrementId."'";				
					$result1 = $dbconn->fetchRow($sql1);
					$oId = $result1['order_id'];
					
					$sql2 = "SELECT increment_id,base_grand_total FROM sales_flat_invoice WHERE order_id = '".$oId."'";
					$result2 = $dbconn->fetchAll($sql2);
					
					if($totalData['label'] == "Grand Total:"){					
						foreach($result2  as $key=>$invoiceno){	
								$warranty_amount = '';
								if($invoiceno['increment_id'][0] == 'W'){	
									$warranty_amount =  Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().number_format($invoiceno['base_grand_total'],2);
									
									$totalData['amount']  = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().number_format($source->getBaseGrandTotal() + $invoiceno['base_grand_total'],2);
																		
									
									$lineBlock['lines'][] = array(
									array( 
										'text'      => 'Warranty Amount:',
										'feed'      => 475,
										'align'     => 'right',
										'font_size' => $totalData['font_size'],
										'font'      => 'bold'
									),
									array(
										'text'      => $warranty_amount,
										'feed'      => 565,
										'align'     => 'right',
										'font_size' => $totalData['font_size'],
										'font'      => 'bold'
									),
									);
								} // W condition			
						} // foreach
						
									
					}// Grand total	 
					
					if($totalData['label'] == "Subtotal:"){ 
						$totalData['label'] = "Subtotal (Excl. Tax):";
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
						if($totalData['label'] == 'Total Payable:'){
							$totalData['label'] = 'Total Due:';
                            if($order->getCodFee()){
                                $totalData['amount'] = Mage::helper('core')->currency(str_replace(",","",($source->getBaseGrandTotal())),true,false);
                            }
						}
						
						if($totalData['label'] == "Pending Amount:"){
							$totalData['label'] = "Advance Amount:";
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

		//Mage::log($lineBlock,null,'aa.log');
        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
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
	}public function convertGroup($index)
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
	
	/**
     * Insert title and number for concrete document type
     *
     * @param  Zend_Pdf_Page $page
     * @param  string $text
     * @return void
     */
    public function insertDocumentNumber(Zend_Pdf_Page $page, $text)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, 'UTF-8');
    }
}
