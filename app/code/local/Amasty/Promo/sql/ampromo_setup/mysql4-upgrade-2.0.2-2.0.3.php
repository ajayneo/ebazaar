<?php
/**
 * @copyright Amasty.
 */
$this->startSetup();


$fieldsSql = 'SHOW COLUMNS FROM ' . $this->getTable('salesrule/rule');
$cols = $this->getConnection()->fetchCol($fieldsSql);

if (!in_array('ampromo_type', $cols)){
    $this->run("ALTER TABLE `{$this->getTable('salesrule/rule')}` ADD `ampromo_type` SMALLINT(4) NOT NULL AFTER `promo_sku`");
}

$this->endSetup();