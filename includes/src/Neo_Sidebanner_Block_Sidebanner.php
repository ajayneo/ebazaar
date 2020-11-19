<?php
class Neo_Sidebanner_Block_Sidebanner extends Mage_Core_Block_Template {
	private $_configs;
	private $_vbanners;
	private $_hbanners;
	private $_media;
	
	public function _construct()
    {
        parent::_construct();
        $this->_configs = Mage::getStoreConfig('sidebanner_section');
        $this->_media = Mage::getBaseUrl('media') . 'homepagebanner/images/';
        $this->setVerticalBanners();
		$this->setHorizontalBanners();
    }
    
	private function setVerticalBanners() {
		$vBannerItems = array();
		for($i = 0; $i<=3; $i++) {
			if (isset($this->_configs["verticalbanner$i"]['image'])) {
				$vBannerItems[$i]['image'] = $this->_media . $this->_configs["verticalbanner$i"]['image'];
                $vBannerItems[$i]['title'] = $this->_configs["verticalbanner$i"]['title'];
                $vBannerItems[$i]['link'] = $this->_configs["verticalbanner$i"]['link'];
			}
		}
		$this->_vbanners = $vBannerItems;
	}
	
	public function getVerticalBanners() {
		return $this->_vbanners;
	}
	
	private function setHorizontalBanners() {
		$hBannerItems = array();
		for($i = 0; $i<=3; $i++) {
			if (isset($this->_configs["horizontalbanner$i"]['image'])) {
				$hBannerItems[$i]['image'] = $this->_media . $this->_configs["horizontalbanner$i"]['image'];
                $hBannerItems[$i]['title'] = $this->_configs["horizontalbanner$i"]['title'];
                $hBannerItems[$i]['link'] = $this->_configs["horizontalbanner$i"]['link'];
			}
		}
		$this->_hbanners = $hBannerItems;
	}
	
	public function getHorizontalBanners() {
		return $this->_hbanners;
	}
}
?>