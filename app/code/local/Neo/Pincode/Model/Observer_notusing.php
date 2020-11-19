<?php
class Neo_Pincode_Model_Observer{
	
	public function checkEighteenplus($observer){
		$eighteenplus = Mage::getModel('core/cookie')->get('eighteenplus');
		if(empty($eighteenplus)):
			header('Location: entry.phtml');
			exit;
		endif;
	}
}
?>