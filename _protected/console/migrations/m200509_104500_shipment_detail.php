<?php

use yii\db\Migration;

class m200509_104500_shipment_detail extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%shipment_detail}}',
            [
                'id'=> $this->primaryKey(11),
                'shipment_id'=> $this->integer(11)->notNull(),
                'part_id' => $this->integer(11)->notNull(),
                'supplier_id' => $this->integer(11)->null(),
                'pack_size'=> $this->decimal(20, 5)->null()->defaultValue('1.00000'),
                'disruption_date'=> $this->date()->null(),
                'coverage_qty'=> $this->decimal(20, 5)->null()->defaultValue(0),
                'need_qty'=> $this->decimal(20, 5)->null()->defaultValue(0),
                'ready_qty'=> $this->decimal(20, 5)->null()->defaultValue(0),
                'approved_qty'=> $this->decimal(20, 5)->null()->defaultValue(0),
                'comment'=> $this->text()->null()
            ],$tableOptions
        );

        $this->addForeignKey('frk-shipment_detail-shipment_id', 'shipment_detail', 'shipment_id', 'shipment', 'id', 'cascade', 'cascade');
        $this->addForeignKey('frk-shipment_detail-part_id', 'shipment_detail', 'part_id', 'part', 'id', 'cascade', 'cascade');
        $this->addForeignKey('frk-shipment_detail-supplier_id', 'shipment_detail', 'supplier_id', 'supplier', 'id', 'cascade', 'cascade');

    }

    public function safeDown()
    {
        $this->dropForeignKey('frk-shipment_detail-shipment_id', 'shipment_detail');
        $this->dropForeignKey('frk-shipment_detail-part_id', 'shipment_detail');
        $this->dropForeignKey('frk-shipment_detail-supplier_id', 'shipment_detail');

        $this->dropTable('{{%shipment_detail}}');
    }
}
