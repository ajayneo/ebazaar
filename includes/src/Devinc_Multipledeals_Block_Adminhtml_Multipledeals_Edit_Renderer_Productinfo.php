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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer new password field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Edit_Renderer_Productinfo extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element) 
	{			  
		$html = '<tr id="prod_select"></tr>
				 <tr id="prod_name"></tr>
				 <tr id="prod_price"></tr>
				 <tr id="prod_qty"></tr>
				 <tr id="prod_edit"><td></td><td id="prod_edit_value"></td></tr>
				 <tr id="prod_id"><td></td><td style="width:200px;"><span><input type="hidden" class="input-text required-entry" value="'.$element->getValue().'" name="product_id" id="product_id" /></span></td></tr>';		
		
        return $html;
    }		

}
