<?php

use yii\db\Migration;

class m200509_104200_shipment extends Migration
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
            '{{%shipment}}',
            [
                'id'=> $this->primaryKey(11),
                'report_date'=> $this->date()->notNull(),
                'created_at'=> $this->date()->notNull()
            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%shipment}}');
    }
}
