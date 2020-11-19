<?php
        ini_set('display_errors', '1');

        $installer = $this;
        $installer->startSetup();
        $installer->getConnection()
                ->addColumn(
                $installer->getTable('feedback'), //Get the newsletter Table
                'cid', //New Field Name
                array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER, //Field Type like TYPE_INTEGER ...
                'nullable'  => true,
                'length'    => 255,
                //'default'   => 'NULL',
                'comment'   => 'cid'
                ));
        $installer->getConnection()                
                ->addColumn(
                $installer->getTable('feedback'), //Get the newsletter Table
                'orderid', //New Field Name
                array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER, //Field Type like TYPE_INTEGER ...
                'nullable'  => true,
                'length'    => 255,
                //'default'   => 'NULL',
                'comment'   => 'orderid'
                ));
        $installer->getConnection()                
                ->addColumn(
                $installer->getTable('feedback'), //Get the newsletter Table
                'scale', //New Field Name
                array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER, //Field Type like TYPE_INTEGER ...
                'nullable'  => true,
                'length'    => 10,
                //'default'   => 'NULL',
                'comment'   => 'scale'
                ));                                               
        $installer->endSetup();
?>