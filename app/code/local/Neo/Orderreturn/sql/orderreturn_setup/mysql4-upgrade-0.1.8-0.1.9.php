<?php
$installer = $this;
// Required tables
$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');
// Insert statuses
$installer->getConnection()->insertArray(
	$statusTable,
	array('status', 'label'),
	array(
		array(
		'status' => 'out_for_delivery', 
		'label' => 'Out for delivery'
		)
	)
);

// Insert states and mapping of statuses to states
$installer->getConnection()->insertArray(
	$statusStateTable,
	array('status', 'state', 'is_default'),
	array(
		array(
		'status' => 'out_for_delivery', 
		'state' => 'out_for_delivery', 
		'is_default' => 1
		)
	)
);

$installer->endSetup();