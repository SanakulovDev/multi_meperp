<?php

use yii\db\Schema;
use yii\db\Migration;

class m200420_115200_coverage_vehicle_t extends Migration
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
            '{{%coverage_vehicle_t}}',
            [
                'id'=> $this->primaryKey(11),
                'type'=> $this->string(2)->null()->defaultValue(null)->comment('D - Daily, W - Weekly'),
                'model_id'=> $this->integer(11)->notNull(),
                'stock'=> $this->integer()->null(),
                'intransit'=> $this->integer()->null(),
                'orders'=> $this->integer()->null(),
                'doh'=> $this->integer()->null(),
                'stock_out'=> $this->date()->null(),
                'calc_at'=> $this->dateTime()->null()
            ],$tableOptions
        );
        $this->createIndex('idx-coverage_vehicle_t-model_id', 'coverage_vehicle_t','model_id', false);

    }

    public function safeDown()
    {
        $this->dropTable('{{%coverage_vehicle_t}}');
    }
}
