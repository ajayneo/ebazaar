<?php

class Devinc_Multipledeals_Helper_Data extends Mage_Core_Helper_Abstract
{
	const STATUS_RUNNING = Devinc_Multipledeals_Model_Source_Status::STATUS_RUNNING;
	const STATUS_DISABLED = Devinc_Multipledeals_Model_Source_Status::STATUS_DISABLED;
	const STATUS_ENDED = Devinc_Multipledeals_Model_Source_Status::STATUS_ENDED;
	const STATUS_QUEUED = Devinc_Multipledeals_Model_Source_Status::STATUS_QUEUED;
	
	//check if extension is enabled
	public static function isEnabled()
	{
		$storeId = Mage::app()->getStore()->getId();
		$isModuleEnabled = Mage::getStoreConfig('advanced/modules_disable_output/Devinc_Multipledeals', $storeId);
		$isEnabled = Mage::getStoreConfig('multipledeals/configuration/enabled', $storeId);
		return ($isModuleEnabled == 0 && $isEnabled == 1);
	}
	
	//$toDate format(year-month-day hour:minute:second) = 0000-00-00 00:00:00
    public function getCountdown($toDate, $countdownId = 'main', $finished = false, $params = false)
    {
    	//from/to date variables
		$fromDate = $this->getCurrentDateTime();
		$jsFromDate = date('F d, Y H:i:s', strtotime($fromDate));
		$jsToDate = date('F d, Y H:i:s', strtotime($toDate));			
		if ($finished) {
			$toDate = $fromDate;
			$jsToDate = $jsFromDate;	
		}	
		
		$countdownType = Mage::getStoreConfig('multipledeals/configuration/countdown_type');
		//js configuration
		$jsTextColor = Mage::getStoreConfig('multipledeals/js_countdown_configuration/textcolor');
		$jsDaysText = Mage::getStoreConfig('multipledeals/js_countdown_configuration/days_text');
		
		//flash configuration		
		$displayDays = Mage::getStoreConfig('multipledeals/countdown_configuration/display_days');
		$bgMain = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/bg_main'));
		$bgColor = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/bg_color'));
		$digitColor = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/textcolor'));
		$alpha = Mage::getStoreConfig('multipledeals/countdown_configuration/alpha');
		$secText = Mage::getStoreConfig('multipledeals/countdown_configuration/sec_text');
		$minText = Mage::getStoreConfig('multipledeals/countdown_configuration/min_text');
		$hourText = Mage::getStoreConfig('multipledeals/countdown_configuration/hour_text');
		$daysText = Mage::getStoreConfig('multipledeals/countdown_configuration/days_text');
		$textColor = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/txt_color'));
					
		if (isset($params['bg_main'])) $bgMain = str_replace('#','0x',$params['bg_main']);
    	$width = (isset($params['width'])) ? $params['width'] : '100%';
    	$height = (isset($params['height'])) ? $params['height'] : '100%';
		
		//get flash source
		$date1 = strtotime($fromDate);
	    $date2 = strtotime($toDate);	   
		$dateDiff = $date2 - $date1;
		$fullDays = floor($dateDiff/(60*60*24));
		
		//set width class
		$class = 'countdown';		
		if ($fullDays>0 && (($countdownType==1 && $displayDays==1) || $countdownType!=1)) {
			$class = 'days-'.$class;
		}	
		if ($countdownType==1 && !Mage::getModel('license/module')->isMobile()) {
			$class = 'flash-'.$class;
		} else {
			$class = 'js-'.$class;			
		}	
		
		if ($displayDays==1) {
			if ($fullDays<=0) {
				$swfPath = 'multipledeals/flash/countdown.swf';
			} else {
				$swfPath = 'multipledeals/flash/countdown_days.swf';
			} 
		} else {
			if ($dateDiff>0) {
				$diff = abs($dateDiff); 
				$years   = floor($diff / (365*60*60*24)); 
				$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
				
				$hoursLeft = $days*24+$hours;
				if ($hoursLeft<100) {
					$swfPath = 'multipledeals/flash/countdown_multiple_2.swf';	
				} else {
					$swfPath = 'multipledeals/flash/countdown_multiple_3.swf';		
				}
			} else {
				$swfPath = 'multipledeals/flash/countdown_multiple_2.swf';		
			}
		}	
		$store = Mage::app()->getStore();
		$source = Mage::getDesign()->getSkinUrl($swfPath, array('_area' => 'frontend', '_store' => $store));
		
		//encode flash variables		
		//if you want to pass your custom variables to the countdown just add them to the array, at the end. Then you can call them in the countdown actionscript.
		$flashVars = array($fromDate, $toDate, $alpha, $bgColor, $digitColor, $bgMain, $textColor);	
		$variables = Mage::getModel('license/module')->encodeFlashVariables($flashVars);
		$textVariables = $secText.'|||'.$minText.'|||'.$hourText.'|||'.$daysText; 
		
		$random = rand(10e16, 10e20);
		$html = '<div class="countdown-container '.$class.'">
					<div id="countdown-'.$countdownId.'-'.$random.'">
						<script type="text/javascript">
				 			var jsCountdown = new JsCountdown("'.$jsFromDate.'", "'.$jsToDate.'", "countdown-'.$countdownId.'-'.$random.'", "'.$jsDaysText.'", "'.$jsTextColor.'");
				 		</script>
				 	</div>
				</div>';
		
		//if countdown type flash and flash present, replace default javascript countdown
		if ($countdownType==1) {   
			$html .= '<script type="text/javascript">						
					     var params = {}; 	
					     var flashvars = {};
					     var attributes = {};
					     
 					     params.menu = "false"; 
 					     params.salign = "MT";
 					     params.allowFullscreen = "true";
					     if (navigator.userAgent.indexOf("Opera") <= -1) {
 					         params.wmode = "opaque";
					     }				 	
					     flashvars.vs = "'.$variables.'";
					     flashvars.smhd = "'.$textVariables.'";				 		
					     
					     swfobject.embedSWF("'.$source.'", "countdown-'.$countdownId.'-'.$random.'", "'.$width.'", "'.$height.'", "9.0.0", false, flashvars, params, attributes);				 			
					 </script>';
		}
	
        return $html;
    }
        
    //called on product list pages
    public function getProductCountdown(Varien_Object $_product, $_params = false, $_timeLeftText = true) {
		$deal = $this->getDealByProduct($_product);
		$html = '';		
		if (Mage::helper('multipledeals')->isEnabled() && $deal) {		
			$toDate = $deal->getDatetimeTo();
			$currentDateTime = Mage::helper('multipledeals')->getCurrentDateTime(0);
			//set it to finished if the deal's time is up or product is out of stock
			if ($currentDateTime>=$deal->getDatetimeFrom() && $currentDateTime<=$deal->getDatetimeTo()) {
    			$finished = ($_product->isSaleable()) ? false : true;
    		} else {
				Mage::getModel('multipledeals/multipledeals')->refreshDeal($deal);
    			$finished = true;
    		}
    		$html .= ($_timeLeftText) ? '<b>'.$this->__('Time left to buy:').'</b>' : '';
			$html .= $this->getCountdown($toDate, $_product->getId(), $finished, $_params);
		}
				
		return $html;
    }
	
	public function getCurrentDateTime($_storeId = null, $_format = 'Y-m-d H:i:s') {
		if (is_null($_storeId)) {
			$_storeId = Mage::app()->getStore()->getId();
		}
		$storeDatetime = new DateTime();
		$storeDatetime->setTimezone(new DateTimeZone(Mage::getStoreConfig('general/locale/timezone', $_storeId)));	
		
		return $storeDatetime->format($_format);
	}
	
	//returns the page number for the "Select a Product" tab on the deal edit page
    public function getProductPage($productId)
    {
    	$visibility = array(2, 4);
    	$collectionSize = Mage::getModel('catalog/product')->getCollection()->setOrder('entity_id', 'DESC')->addAttributeToFilter('visibility', $visibility)->addAttributeToFilter('entity_id', array('gteq' => $productId))->getSize();
    	
    	return ceil($collectionSize/20);
    }
    
    public function getMagentoVersion() {
		return (int)str_replace(".", "", Mage::getVersion());
    }
    
    //return true if deal should run on store
    public function runOnStore(Varien_Object $_deal, $_storeId = false) {
    	if ($_deal->getStores()!='') {
    		$dealStoreIds = array();
    		if (strpos($_deal->getStores(), ',')) {
				$dealStoreIds = explode(',', $_deal->getStores());
			} else {
				$dealStoreIds[] = $_deal->getStores();
			}
			if (!$_storeId) {
    			$_storeId = Mage::app()->getStore()->getId();
    		}
    		return (in_array($_storeId, $dealStoreIds)) ? true : false;
    	}
    	
    	return false;
    }
    
    //returns the main running deal of the current store
    public function getDeal($_excludeProductIds = array(0)) {  		    	
		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id', array('nin'=>$_excludeProductIds))->addFieldToFilter('status', array('eq'=>self::STATUS_RUNNING))->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');	
			
		if (count($dealCollection)) {
	        foreach ($dealCollection as $deal) {
    	    	if ($this->runOnStore($deal)) {
		    		return $deal;
		    	}
		    }
		}	
		
		return false;	
    }
    
    //if the product is a deal and the deal is running under the current store, returns the deal data
    public function getDealByProduct(Varien_Object $_product) {  		    	
		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id', array('eq'=>$_product->getId()))->addFieldToFilter('status', array('eq'=>self::STATUS_RUNNING))->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');
				
		if (count($dealCollection)) {
	        foreach ($dealCollection as $deal) {
    	    	if ($this->runOnStore($deal)) {
		    		return $deal;
		    	}
		    }
		}	
		
		return false;	
    }
    
}
