<?php
/**
 * Adminhtml all tags grid
 *
 * @category   Neo
 * @package    Neo_Adminhtml
 * @author     Neo Core Team <Rajesh Maurya>
 */
class Neo_Adminhtml_Block_Tag_Tag_Grid extends Mage_Adminhtml_Block_Tag_Tag_Grid
{
   
    protected function _prepareColumns()
    {
        $this->addColumnAfter('tag_keywords', array(
            'header'        => Mage::helper('tag')->__('Tag Page Keywords'),
            'index'         => 'tag_keywords',
        ),'name');
		
		$this->addColumnAfter('tag_description', array(
            'header'        => Mage::helper('tag')->__('Tag Page Description'),
            'index'         => 'tag_description',
        ),'name');
		
		$this->addColumnAfter('tag_page_title', array(
            'header'        => Mage::helper('tag')->__('Tag Page Title'),
            'index'         => 'tag_page_title',
        ),'name');
		
        return parent::_prepareColumns();
    }
}
