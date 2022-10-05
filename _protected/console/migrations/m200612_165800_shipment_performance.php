<?php

use yii\db\Migration;

class m200612_165800_shipment_performance extends Migration
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
            '{{%shipment_performance}}',
            [
                'id'=> $this->primaryKey(11),
                'report_date'=> $this->date()->unique()->notNull(),
                'created_at'=> $this->dateTime(),
                'updated_at'=> $this->dateTime(),
            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%shipment_performance}}');
    }
}
