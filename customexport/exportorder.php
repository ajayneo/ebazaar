<?php //export order from bluejalepeno
	//orders array to be passed in model
require_once('../app/Mage.php');
umask(0);
Mage::app();

	//Model exportOrders($orders)
	//105986
	//102173
	//SELECT entity_id  FROM `sales_flat_order` WHERE `entity_id` BETWEEN 102173 AND 105986
	
	$orders = array(102227);
	try{
		$model_file = Mage::getModel('bluejalappeno_orderexport/export_csv')->exportOrders($orders);
	echo "Export Order File ". $model_file;
	}catch(Exception $e){
		echo $e->getMessage();
	}
?>