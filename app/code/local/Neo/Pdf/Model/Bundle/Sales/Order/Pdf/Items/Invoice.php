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
 * @package     Mage_Bundle
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
class Neo_Pdf_Model_Bundle_Sales_Order_Pdf_Items_Invoice extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Invoice
{
    /**
     * Draw item line
     *
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();		
	
		
        $this->_setFontRegular();
        $items = $this->getChilds($item);
			
        $_prevOptionId = '';
        $drawItems = array();

        foreach ($items as $_item) {			
			$itemtag = $_item->getItemtag();
			$esn = $_item->getEsnNumber();
			
			$sku = $_item->getSku();		
			$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku); 			
			
			if($product['custom_invoice_display'] != '') {
			 $custome_invoice_display = $product->getCustomInvoiceDisplay();
			}
						
            $line   = array();

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId   = $attributes['option_id'];
            }
            else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = array(
                    'lines'  => array(),
                    'height' => 15
                );
            }

            if ($_item->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
					if (substr($attributes['option_label'],0,2) == '__'  ){
						$attributes['option_label'] = substr($attributes['option_label'],2,strlen($attributes['option_label']));
					}
                    $line[0] = array(
                        'font'  => 'bold',
                        'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 30, true, true),
                        'feed'  => 35
                    );

                    $drawItems[$optionId] = array(
                        'lines'  => array($line),
                        'height' => 15
                    );

                    $line = array();

                    $_prevOptionId = $attributes['option_id'];
                }
            }

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($_item->getOrderItem()->getParentItem()) {
                $feed = 35;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = 30;
                $name = $_item->getName();
            }	
			
			$nameArray = Mage::helper('core/string')->str_split($name, 30, true, true);	

			
			if (trim($custome_invoice_display)!=''){	
				$custome_invoice_display_array = Mage::helper('core/string')->str_split($custome_invoice_display, 30, true, true);					
				$nameArray = array_merge($nameArray,$custome_invoice_display_array);
			}
			//print_r($resultNameArray);echo "<br><br>";
			
            $line[] = array(
                'text'  => $nameArray,
				'font_size' => 8,
				'font' => 'bold',
                'feed'  => $feed
            );
			
			/** SKU **/
			$skuarray = array();
			$skuarray[] = $_item->getSku();
			$arr_item = array();
			$servicetag = array();
			$esnnumber = array();
			$meid = array();
		
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
			
			
			// draw SKUs
					$line[] = array(
                    'text'  => $skuarray,
					'align' => 'left',
                    'feed'  => 245,
					'font'  => 'bold',
						'font_size' => 11
                );
			// draw prices
            if ($this->canShowPriceInfo($_item)) {
                $price = $order->formatPriceTxt($_item->getPrice());
                $line[] = array(
                    'text'  => Mage::helper('core/string')->str_split($price, 15),
                    'feed'  => 320,
                    'font'  => 'bold',
                    'align' => 'left'
                );
                $line[] = array(
                    'text'  => $_item->getQty()*1,
                    'feed'  => 400,
                    'font'  => 'bold',
					'align' => 'center'
                );
				
				
				
				
                /* $tax = $order->formatPriceTxt($_item->getTaxAmount());
				
                $line[] = array(
                    'text'  => Mage::helper('core/string')->str_split($tax, 15),
                    'feed'  => 380,
                    'font'  => 'bold',
                    'align' => 'left'
                );
						
				$taxPercentRough = $_item->getOrderItem()->getTaxPercent(); 
				$taxPercent = number_format($taxPercentRough,2); 
				$taxPercentage = $taxPercent." %";				
				
                $line[] = array(
                    'text'  => $taxPercentage,
                    'feed'  => 450,
                    'font'  => 'bold',
                    'align' => 'left'
                );	
				*/
				$subTotalInctax = $_item->getOrderItem()->getPriceInclTax();
               // $row_total = $order->formatPriceTxt($_item->getRowTotal());
                $line[] = array(
                    'text'  => Mage::helper('core/string')->str_split($order->formatPriceTxt($subTotalInctax), 16),
                    'feed'  => 500,
                    'font'  => 'bold',
                    'align' => 'left'
                );
            }

            $drawItems[$optionId]['lines'][] = $line;
			$custome_invoice_display = '';
			
			
        }	

	
        // custom options
        $options = $item->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $lines = array();
                    $lines[][] = array(
                        'text'  => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                        'font'  => 'italic',
                        'feed'  => 35
                    );

                    if ($option['value']) {
                        $text = array();
                        $_printValue = isset($option['print_value'])
                            ? $option['print_value']
                            : strip_tags($option['value']);
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 30, true, true) as $_value) {
                                $text[] = $_value;
                            }
                        }

                        $lines[][] = array(
                            'text'  => $text,
                            'feed'  => 40
                        );
                    }

                    $drawItems[] = array(
                        'lines'  => $lines,
                        'height' => 20
                    );
                }
            }
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, array('table_header' => true));

        $this->setPage($page);
    }
	
	/**
     * Retrieve tax with persent html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function displayTaxPercent(Varien_Object $item)
    {
        if ($item->getTaxPercent()) {
            return sprintf('%s%%', $item->getTaxPercent() + 0);
        } else {
            return '0%';
        }
    }
	 public function getValueHtml($item)
    {
		
        $result = strip_tags($item->getName());
        if (!$this->isShipmentSeparately($item) || $this->isShipmentSeparately($item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result = "- ".sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }
        if (!$this->isChildCalculated($item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result .= " " . strip_tags($this->getOrderItem()->getOrder()->formatPrice($attributes['price']));
            }
        }
        return $result;
    }
	
}
