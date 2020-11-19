<?php
/**
 * Home Block for display model list on home page
 *
 * @category    Neo
 * @package     Neo_CustomCategoryAttribute
 * @author      Shankar Ingale
 * @copyright   Copyright (c) 2014, NeoSoft Technologies
 * 
 */

    class Neo_CustomCategoryAttribute_Block_Home extends Mage_Core_Block_Template
    {
        /**
         *
         * Function for get all model categories
         * @author Shankar Ingale
         * @param  nothing
         * @return Model categoriers
         *  
         */
        public function getAllCategories(){
            $defaultCategory = Mage::getModel('catalog/category')->load(2);
            $categories = explode(",",$defaultCategory->getChildren());    
            return $categories;
        }
        
        /**
         *
         * Function for get category data
         * @author Shankar Ingale
         * @param  category Id
         * @return Model category data or NULL if category id is not provided
         *  
         */
        public function getCategory($catId=NULL)
        {
            if($catId){
                return Mage::getModel('catalog/category')->load($catId);    
            }else{
                return NULL;
            }
        }
        
        /**
         *
         * Function for get Model average rating
         * @author Shankar Ingale
         * @param  category id
         * @return Model average rating or null if category id is not provided
         *  
         */
        public function getModelRating($catid=NULL){
          
            if($catid){
                
                $resource = Mage::getSingleton('core/resource');
                $read = $resource->getConnection('catalog_read');
		$sql	=	"SELECT * FROM `categoryreview` where catid=$catid and status!='1'";
		$catreviews = $read->fetchAll($sql);
	   	$averageQuality = 0;
                $averagePrice   = 0;
                $averageValue   = 0;
                $noOfRewiew = count($catreviews);
                if($noOfRewiew){
                    foreach($catreviews as $review){
                        $averageQuality += (int)$review['rating_quality'];
                        $averagePrice   += (int)$review['rating_price'];
                        $averageValue   += (int)$review['rating_value'];
                    }
                    
                    $averageQuality = $averageQuality/$noOfRewiew;
                    $averagePrice   = $averagePrice/$noOfRewiew;
                    $averageValue   = $averageValue/$noOfRewiew;
                    
                    return $totalRating    = ($averageQuality + $averagePrice+ $averageValue)/3;
                    
                }else{
                    return 0;   
                }
            }else{
                return 0;
            }
            
        }
    }
