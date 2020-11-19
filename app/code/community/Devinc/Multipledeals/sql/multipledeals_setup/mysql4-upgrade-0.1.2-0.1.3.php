<?php
$installer = $this;

$installer->startSetup();

//add new datetime columns
$installer->getConnection()->addColumn($installer->getTable('multipledeals'), 'deal_mrpprice', 'decimal(12,2) NOT NULL after `product_id`');
$installer->endSetup(); 