<?php

use yii\db\Migration;

class m200612_170000_shipment_performance_detail extends Migration
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
            '{{%shipment_performance_detail}}',
            [
                'id'=> $this->primaryKey(11),
                'shipment_performance_id'=> $this->integer()->notNull(),
                'part_id'=> $this->integer()->notNull(),
                'less_doh_qty'=> $this->decimal(20,5),
                'shipped_qty'=> $this->decimal(20,5),
                'shipped_at'=> $this->date(),
                'over_doh_qty'=> $this->decimal(20,5),
            ],$tableOptions
        );

        $this->addForeignKey('frk-spd-shipment_performance_id', 'shipment_performance_detail', 'shipment_performance_id', 'shipment_performance', 'id', 'cascade', 'cascade');
        $this->addForeignKey('frk-spd-part_id', 'shipment_performance_detail', 'part_id', 'part', 'id', 'cascade', 'cascade');

    }

    public function safeDown()
    {
        $this->dropForeignKey('frk-spd-shipment_performance_id', 'shipment_performance_detail');
        $this->dropForeignKey('frk-spd-part_id', 'shipment_performance_detail', 'part_id');

        $this->dropTable('{{%shipment_performance_detail}}');
    }
}
