<?php

$this->startSetup();

$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'cod_tax_amount', 'decimal(12,4)');
$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'base_cod_tax_amount', 'decimal(12,4)');
$this->_conn->addColumn($this->getTable('sales_flat_quote_address'), 'cod_tax_amount', 'decimal(12,4)');
$this->_conn->addColumn($this->getTable('sales_flat_quote_address'), 'base_cod_tax_amount', 'decimal(12,4)');

$eav = new Mage_Eav_Model_Entity_Setup('sales_setup');

$eav->addAttribute('order', 'cod_tax_amount', array('type' => 'decimal',));
$eav->addAttribute('order', 'base_cod_tax_amount', array('type' => 'decimal'));

$eav->addAttribute('order', 'cod_tax_amount_invoiced', array('type' => 'decimal',));
$eav->addAttribute('order', 'base_cod_tax_amount_invoiced', array('type' => 'decimal'));

//$eav->addAttribute('order', 'cod_tax_amount_refunded', array('type' => 'decimal',));
//$eav->addAttribute('order', 'base_cod_tax_amount_refunded', array('type' => 'decimal'));

//$eav->addAttribute('order', 'cod_tax_amount_canceled', array('type' => 'decimal',));
//$eav->addAttribute('order', 'base_cod_tax_amount_canceled', array('type' => 'decimal'));

$eav->addAttribute('invoice', 'cod_tax_amount', array('type' => 'decimal',));
$eav->addAttribute('invoice', 'base_cod_tax_amount', array('type' => 'decimal'));

//$eav->addAttribute('creditmemo', 'cod_tax_amount', array('type' => 'decimal',));
//$eav->addAttribute('creditmemo', 'base_cod_tax_amount', array('type' => 'decimal'));

$this->endSetup();

?>
