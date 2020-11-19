<?php
$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE sales_flat_invoice_item ADD COLUMN serial TEXT NULL;
");

$installer->endSetup();
