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
 * Sales Order Shipment Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class  Neo_Pdf_Model_Sales_Order_Pdf_Items_Shipment_Default extends Mage_Sales_Model_Order_Pdf_Items_Shipment_Default
{
    /**
     * Draw item line
     */
    public function draw()
    {
		$order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();
		$skuarray = Mage::helper('core/string')->str_split($this->getSku($item), 15,true,true);
		$nameArray = Mage::helper('core/string')->str_split($item->getName(), 50, true, true);
		$itemtag = $item->getItemtag();
		$esn = $item->getEsnNumber();
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
				
		}
		$sku =  $this->getSku($item);
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);

		$nameArray = Mage::helper('core/string')->str_split($item->getName(), 30, true, true);
			
			/*if (trim($custome_invoice_display)!=''){	
				$custome_invoice_display_array = Mage::helper('core/string')->str_split($custome_invoice_display, 30, true, true);	
				$nameArray = array_merge($nameArray,$custome_invoice_display_array);
			}*/
			$custom_product_list = '';
			if($product['attribute_set_id'] == 12 || $product['attribute_set_id'] == 4 || $product['attribute_set_id'] == 10 || $product['attribute_set_id'] == 17 || $product['attribute_set_id'] == 16)
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
		
		
		// draw Product name
        $lines[0] = array(array(
            'text' => $nameArray,
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => $skuarray,
            'feed'  => 270,
            'align' => 'right'
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => 380,
            'align' => 'right'
        );

		$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
		$currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
		//
        $lines[0][] = array(
                'text'  => $currency_symbol . ($item->getQty() * $item->getPrice()),
                'feed'  => 500,
                'align' => 'right'
         );
		
		
        // draw item Prices
		$SubTotalLast = 0;
		$RoWTotal = 0;
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        //$prices = $item->getPrice();
        $feedPrice = 395;
        $feedSubtotal = $feedPrice + 170;
		/*$lines[0][] = array(
                'text'  => $currency_symbol . ($item->getPrice() * 1),
                'feed'  => 280,             
            );*/		
        foreach ($prices as $priceData){
            // draw Price
            $lines[$i][] = array(
                'text'  => $priceData['price'],
                'feed'  => 320,             
            );				
            $i++;
        }
		
		
        

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 110
                );

                // draw options value
                if ($option['value']) {
                    $_printValue = isset($option['print_value'])
                        ? $option['print_value']
                        : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 115
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
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
