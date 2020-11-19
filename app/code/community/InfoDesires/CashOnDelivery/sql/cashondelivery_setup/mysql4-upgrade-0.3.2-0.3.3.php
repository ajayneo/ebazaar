<?php

$this->startSetup();

$this->run("
    UPDATE {$this->getTable('core_config_data')} SET `value` = 0 WHERE `path` = 'payment/cashondelivery/allowspecific';
    ");

$this->endSetup();

?>
