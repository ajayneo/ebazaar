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
 * Sales Order Invoice Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Neo_Pdf_Model_Sales_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    /**
     * Draw item line
     */
    public function draw()
    {  
        $order  = $this->getOrder();
        $item   = $this->getItem();
	    $itemtag = $item->getItemtag();
		$esn = $item->getEsnNumber();
		
		
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

		$sku =  $this->getSku($item);
		
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku); 
		/*if($product['custom_invoice_display'] != '') {
			$custome_invoice_display = $product->getCustomInvoiceDisplay();
		}
		*/
		$countName = count((Mage::helper('core/string')->str_split($item->getName(), 30, true, true)));
		
        $lines  = array();
		$skuarray = Mage::helper('core/string')->str_split($this->getSku($item), 15,true,true);
		
		/**Servicetag */
		if($itemtag){
			$arr_item[] = 'Service Tag#';
			$arr_item1 = explode(",", $itemtag);
			$servicetag = array_merge($arr_item,$arr_item1);				
			$skuarray = array_merge($skuarray,$servicetag);		
		}
		
		/**MEID number(Esn number)*/
		
		if($esn){
			$esnnumber[] = 'MEID :: ';
			$esnnumber1 = explode(",", $esn);
			$meid = array_merge($esnnumber,$esnnumber1);							
			$skuarray = array_merge($skuarray,$meid);			
			$nameArray = Mage::helper('core/string')->str_split($item->getName(), 50, true, true);
			/*$empty_array[] = 'kkkk ';			
			for($i=0;$i<=count($esnnumber1);$i++){
				$nameArray = array_merge($nameArray,$empty_array);
			}	*/		
		}
				
			$nameArray = Mage::helper('core/string')->str_split($item->getName(), 20, true, true);
			
			/*if (trim($custome_invoice_display)!=''){	
				$custome_invoice_display_array = Mage::helper('core/string')->str_split($custome_invoice_display, 30, true, true);	
				$nameArray = array_merge($nameArray,$custome_invoice_display_array);
			}*/
			$custom_product_list = '';
			if($product['attribute_set_id'] == 18 || $product['attribute_set_id'] == 4 || $product['attribute_set_id'] == 10 || $product['attribute_set_id'] == 17 || $product['attribute_set_id'] == 16)
			{
				$extra_infor = $item->getOrderItem()->getExtraInfor();
				if($extra_infor){
					$extraInforData = explode(',' , unserialize($extra_infor));
					foreach($extraInforData as $data){
						if($data) {
							$custom_product_list = $this->convertHtmlSplCharsForPdf($data);
							$custom_product_list_array = Mage::helper('core/string')->str_split($custom_product_list, 50, true, true);	
							$nameArray = array_merge($nameArray,$custom_product_list_array);
						}
					}
				}else{
					if($product['custom_product_list1'] != '') {
						$custom_product_list1 = $this->convertHtmlSplCharsForPdf($product->getCustomProductList1());
						$custom_product_list1_array = Mage::helper('core/string')->str_split($custom_product_list1, 50, true, true);	
						$nameArray = array_merge($nameArray,$custom_product_list1_array);
					}
					if($product['custom_product_list2'] != '') {
						$custom_product_list2 = $this->convertHtmlSplCharsForPdf($product->getCustomProductList2());
						$custom_product_list2_array = Mage::helper('core/string')->str_split($custom_product_list2, 50, true, true);	
						$nameArray = array_merge($nameArray,$custom_product_list2_array);
					}
					if($product['custom_product_list3'] != '') {
						$custom_product_list3 = $this->convertHtmlSplCharsForPdf($product->getCustomProductList3());
						$custom_product_list3_array = Mage::helper('core/string')->str_split($custom_product_list3, 50, true, true);	
						$nameArray = array_merge($nameArray,$custom_product_list3_array);
					}
					if($product['custom_product_list4'] != '') {
						$custom_product_list4 = $this->convertHtmlSplCharsForPdf($product->getCustomProductList4());
						$custom_product_list4_array = Mage::helper('core/string')->str_split($custom_product_list4, 50, true, true);	
						$nameArray = array_merge($nameArray,$custom_product_list4_array);
					}
					if($product['custom_product_list5'] != '') {
						$custom_product_list5 = $this->convertHtmlSplCharsForPdf($product->getCustomProductList5());
						$custom_product_list5_array = Mage::helper('core/string')->str_split($custom_product_list5, 50, true, true);	
						$nameArray = array_merge($nameArray,$custom_product_list5_array);
					}
					if($product['custom_product_list6'] != '') {
						$custom_product_list6 = $this->convertHtmlSplCharsForPdf($product->getCustomProductList6());
						$custom_product_list6_array = Mage::helper('core/string')->str_split($custom_product_list6, 50, true, true);	
						$nameArray = array_merge($nameArray,$custom_product_list6_array);
					}
					if($product['custom_product_list7'] != '') {
						$custom_product_list7 = $this->convertHtmlSplCharsForPdf($product->getCustomProductList7());
						$custom_product_list7_array = Mage::helper('core/string')->str_split($custom_product_list7, 50, true, true);	
						$nameArray = array_merge($nameArray,$custom_product_list7_array);
					}
				}
			}
			//add warranty details
			$warrantyData = array('Warranty');
			$allRelationData = explode(',',$item->getOrderItem()->getProductWarrantyRelation());
			if(count($allRelationData) > 0){
				$totalWarranty = 0;
				foreach($allRelationData as $data){
					$relationData = explode('_',$data);
					if( isset( $relationData[0] ) ){
						$mainProductId = $relationData[0];
					}
					if( isset( $relationData[1] ) ) {
						$warrantyId = $relationData[1];
					}
					if($warrantyId){
						$product = Mage::getModel('catalog/product')->load($warrantyId);
						$warrantyData[]= $product->getData('name');
						$totalWarranty++;
					}
				}
			}
			//$nameArray = array_merge($nameArray,$warrantyData);
		// draw Product name
        $lines[0] = array(array(
            'text' => $nameArray,
            'feed' => 35,
        ));

		$serialArray = Mage::helper('core/string')->str_split($item->getSerial(), 15, true, true);	

        $lines[0][] = array(
            'text'  => $serialArray,
            'feed'  => 145,
            'align' => 'center',
        );		 

        // draw SKU
        $lines[0][] = array(
            'text'  => $skuarray,
            'feed'  => 230,
            'align' => 'left',
			'font'  => 'bold',
			'font_size' => 11
        );		

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => 400,
			'font'  => 'bold',
            'align' => 'center'
        );

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 320;
        $feedSubtotal = $feedPrice + 205;
        foreach ($prices as $priceData){
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = array(
                    'text'  => $priceData['label'],
                    'feed'  => $feedPrice,
                    'align' => 'left'
                );
                // draw Subtotal label
                $lines[$i][] = array(
                    'text'  => $priceData['label'],
                    'feed'  => $feedSubtotal,
                    'align' => 'left'
                );
                $i++;
            }
            // draw Price
            $lines[$i][] = array(
                'text'  => Mage::helper('core/string')->str_split($priceData['price'], 15),
                'feed'  => $feedPrice,
                'font'  => 'bold',
                'align' => 'left'
            );
            // draw Subtotal
			$subtotalIncPrice = $item->getOrderItem()->getPriceInclTax();
			// old 
			//$priceData['subtotal'];
            $lines[$i][] = array(
                'text'  => Mage::helper('core/string')->str_split($order->formatPriceTxt($subtotalIncPrice), 16),
                'feed'  => 450,
                'font'  => 'bold',
                'align' => 'left'
            );
            $i++;
        }

        // draw Tax
       /* $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($order->formatPriceTxt($item->getTaxAmount()), 15),
            'feed'  => 380,
            'font'  => 'bold',
            'align' => 'left'
        );*/
		
		/*$taxPercentRough = $item->getOrderItem()->getTaxPercent(); 
		$taxPercent = number_format($taxPercentRough,2); 
		$taxPercentage = $taxPercent." %";		
		
		$lines[0][] = array(
            'text'  => $taxPercentage,
            'feed'  => 450,
            'font'  => 'bold',
            'align' => 'left'
        );	*/	

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 30, true, true),
                            'feed' => 40
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 16
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
	
	public function convertHtmlSplCharsForPdf($htmlStr){
	
	
		$pdfStr = str_replace('&#0153;', "&trade;", $htmlStr);
			
		$pdfStr = "- ".html_entity_decode($pdfStr, ENT_NOQUOTES,"UTF-8");
		
		return $pdfStr;
		
	}
}
