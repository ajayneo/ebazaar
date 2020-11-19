<?php
class Neo_Ticker_Block_Ticker extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function getTicker()
    {
        if (!$this->hasData('ticker')) {
            $this->setData('ticker', Mage::registry('ticker'));
        }
        return $this->getData('ticker');
    }

    public function getTickerItems()
    {
        $items = Mage::getModel('ticker/ticker')->getCollection();
        return $items;
    }
}