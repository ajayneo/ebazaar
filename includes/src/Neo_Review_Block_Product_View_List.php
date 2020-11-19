<?php
    class Neo_Review_Block_Product_View_List extends Mage_Core_Block_Template
    {
        public function getReviewLink($id)
        {
            return Mage::getUrl('review/product/list', array('id'=> $id));
        }
        
        public function AggregrateRating($id)
        {
            $_reviews = Mage::getModel('review/review')->getResourceCollection();
            $_reviews->addStoreFilter( Mage::app()->getStore()->getId() )
                    ->addEntityFilter('product',$id)
                    ->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
                    ->setDateOrder()
                    ->addRateVotes();
            $avg = 0;
            
            $ratings = array();
            if (count($_reviews) > 0){
                foreach ($_reviews->getItems() as $_review):
                    foreach( $_review->getRatingVotes() as $_vote ):
                        $ratings[] = $_vote->getPercent();
                    endforeach;
                endforeach;
                $avg = array_sum($ratings)/count($ratings);
            }
            return $avg;
        }
        
        /*get the Ratings count */
        public function RatingCounts($id)
        {
            $_reviews = Mage::getModel('review/review')->getResourceCollection();
            $_reviews->addStoreFilter( Mage::app()->getStore()->getId() )
                    ->addEntityFilter('product',$id)
                    ->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
                    ->setDateOrder()
                    ->addRateVotes();
            $avg = 0;
            
            $ratingscount = 0;
            if (count($_reviews) > 0){
                foreach ($_reviews->getItems() as $_review):
                    foreach($_review->getRatingVotes() as $_vote):
                        $ratingscount++; 
                    endforeach;
                endforeach;
            }
            return $ratingscount;
        }
        
        public function AggregrateRatingInFiveStars($id)
        {
            $_reviews = Mage::getModel('review/review')->getResourceCollection();
            $_reviews->addStoreFilter( Mage::app()->getStore()->getId() )
                    ->addEntityFilter('product',$id)
                    ->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
                    ->setDateOrder()
                    ->addRateVotes();
            $avg = 0;
            $ratings = array();
            if (count($_reviews) > 0){
                foreach ($_reviews->getItems() as $_review):
                    foreach( $_review->getRatingVotes() as $_vote ):
                        $ratings[] = $_vote->getPercent();
                    endforeach;
                endforeach;
                $avg = array_sum($ratings)/count($ratings);
                $avg = $avg / 20;
            }
            return round($avg,1);
        }
        
        public function AggregrateRatingStarsCount($id)
        {
            $_reviews = Mage::getModel('review/review')->getResourceCollection();
            $_reviews->addStoreFilter( Mage::app()->getStore()->getId() )
                    ->addEntityFilter('product',$id)
                    ->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
                    ->setDateOrder()
                    ->addRateVotes();
            $avg = 0;
            $count_stars = array();
            
            for($i=1;$i<=5;$i++){
                $count_stars[$i] = 0;
            }
            $total_rating_count = $this->RatingCounts($id);
            $ratings = array();
            if(count($_reviews) > 0){
                foreach ($_reviews->getItems() as $_review):
                    foreach( $_review->getRatingVotes() as $_vote ):
                        $count_stars[$_vote->getValue()] = $count_stars[$_vote->getValue()] + 1;
                    endforeach;
                endforeach;
            }
            
			if($total_rating_count){
				$count_star_percentage = $this->getPercentageinStars($count_stars,$total_rating_count);
            	return $count_star_percentage;	
			}
        }
        
        public function getPercentageinStars($count_stars,$total_rating_count){
            $count_star_percentage = array();
            foreach($count_stars as $key => $value){
                $count_star_percentage[$key] = ($value/$total_rating_count)*100;
            }
            return $count_star_percentage;
        }
        
        /*public function AggregrateRatingStarsCount($id)
        {
            $_reviews = Mage::getModel('review/review')->getResourceCollection();
            $_reviews->addStoreFilter( Mage::app()->getStore()->getId() )
                    ->addEntityFilter('product',$id)
                    ->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )
                    ->setDateOrder()
                    ->addRateVotes();
            $avg = 0;
            $count_stars = array();
            
            for($i=1;$i<=5;$i++){
                $count_stars[$i] = 0;
            }
            //echo $this->RatingCounts($id); exit;
            $ratings = array();
            if(count($_reviews) > 0){
                foreach ($_reviews->getItems() as $_review):
                    foreach( $_review->getRatingVotes() as $_vote ):
                        $count_stars[$_vote->getValue()] = $count_stars[$_vote->getValue()] + 1;
                    endforeach;
                endforeach;
            }
            return $count_stars;
        }*/
    }
?>