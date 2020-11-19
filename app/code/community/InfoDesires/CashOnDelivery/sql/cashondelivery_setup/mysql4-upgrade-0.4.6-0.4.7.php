<?php

$this->startSetup();

    $setup = new Mage_Sales_Model_Mysql4_Setup('sales_setup');

    $setup->addAttribute('order', 'cod_fee', array('type' => 'decimal'));
    $setup->addAttribute('order', 'base_cod_fee', array('type' => 'decimal'));
    $setup->addAttribute('order', 'cod_fee_invoiced', array('type' => 'decimal'));
    $setup->addAttribute('order', 'base_cod_fee_invoiced', array('type' => 'decimal'));
    $setup->addAttribute('order', 'cod_tax_amount', array('type' => 'decimal'));
    $setup->addAttribute('order', 'base_cod_tax_amount', array('type' => 'decimal'));
    $setup->addAttribute('order', 'cod_tax_amount_invoiced', array('type' => 'decimal'));
    $setup->addAttribute('order', 'base_cod_tax_amount_invoiced', array('type' => 'decimal'));

    $setup->addAttribute('invoice', 'cod_fee', array('type' => 'decimal'));
    $setup->addAttribute('invoice', 'base_cod_fee', array('type' => 'decimal'));
    $setup->addAttribute('invoice', 'cod_tax_amount', array('type' => 'decimal'));
    $setup->addAttribute('invoice', 'base_cod_tax_amount', array('type' => 'decimal'));

    $setup->addAttribute('quote', 'cod_fee', array('type' => 'decimal'));
    $setup->addAttribute('quote', 'base_cod_fee', array('type' => 'decimal'));
    $setup->addAttribute('quote', 'cod_tax_amount', array('type' => 'decimal'));
    $setup->addAttribute('quote', 'base_cod_tax_amount', array('type' => 'decimal'));
    $setup->addAttribute('quote_address', 'cod_fee', array('type' => 'decimal'));
    $setup->addAttribute('quote_address', 'base_cod_fee', array('type' => 'decimal'));
    $setup->addAttribute('quote_address', 'cod_tax_amount', array('type' => 'decimal'));
    $setup->addAttribute('quote_address', 'base_cod_tax_amount', array('type' => 'decimal'));

$this->endSetup();

?>
