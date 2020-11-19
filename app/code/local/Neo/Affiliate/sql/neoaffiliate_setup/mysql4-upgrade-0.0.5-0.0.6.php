<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getTable('neo_affiliate');
$installer->run("ALTER TABLE ".$table." ADD COLUMN qty_ordered VARCHAR(45)");
$installer->endSetup();
?>