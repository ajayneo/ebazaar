<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Bannerslider
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Bannerslider Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Bannerslider
 * @author  	Magestore Developer
 */
class Magestore_Bannerslider_Model_Bannerslider extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('bannerslider/bannerslider');
	}

	/* To generate random numbers for banners sliders to display at frontend */
	function UniqueRandomNumbersWithinRange($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }

    /* @author : Sonali Kosrabe
    * @date : 2nd dec 2017
    * @purpose : To change the preference for slider.  Slider preferences will change and will be same for both app and website. 
    */
    public function changePreferences($sliderId = null) {
        
        
        if($sliderId == null)
            $sliderId = 1; // 1= home page slide for application //$this->getRequest()->getParam('id');


        $slider = Mage::getModel('bannerslider/bannerslider')->load($sliderId);
        $banners = Mage::getModel('bannerslider/banner')->getCollection();
        $banners->getSelect()->join(array('table_alias' => $banners->getTable('bannerslider/bannerslider')), 'main_table.bannerslider_id = table_alias.bannerslider_id', array('slider_id' => 'table_alias.bannerslider_id'));
        $banners->addFieldToFilter('main_table.bannerslider_id',$sliderId);


        
        $bannerIds = $banners->getColumnValues('banner_id');
        $totalBanners = count($bannerIds); //Mage::getModel('bannerslider/banner')->getCollection()->count();
        $preferences = $this->UniqueRandomNumbersWithinRange(1,$totalBanners,$totalBanners);
        //print_r($bannerIds);die;
        foreach ($bannerIds as $key => $bannerId) {
           Mage::getModel('bannerslider/banner')->load($bannerId)->setPreference($preferences[$key])->save();
        }

        $similiarTo = $slider->getSimilarTo();
        $slider1 = Mage::getModel('bannerslider/banner')->getCollection();
        $slider1->getSelect()->join(array('table_alias' => $slider1->getTable('bannerslider/bannerslider')), 'main_table.bannerslider_id = table_alias.bannerslider_id', array('slider_id' => 'table_alias.bannerslider_id'));
        $slider1->addFieldToFilter('main_table.bannerslider_id',$similiarTo);
        
        $bannerIds1 = $slider1->getColumnValues('banner_id');
        $totalBanners1 = count($bannerIds1); //Mage::getModel('bannerslider/banner')->getCollection()->count();
        //$preferences = $this->UniqueRandomNumbersWithinRange(1,$banners,$totalBanners);
        foreach ($bannerIds1 as $key1 => $bannerId1) {
           Mage::getModel('bannerslider/banner')->load($bannerId1)->setPreference($preferences[$key1])->save();
        }       

        $dirs = array(
           // 'downloader/.cache/',
           // 'downloader/pearlib/cache/*',
           // 'downloader/pearlib/download/*',
            'var/cache/',
            'var/locks/',
            'var/log/',
            'var/report/',
            //'var/session/',
            'var/tmp/'
        );

        foreach($dirs as $dir) {
            $folder = Mage::getBaseDir().'/'.$dir;
            exec('rm -rf '.$folder);
        }

    }
}