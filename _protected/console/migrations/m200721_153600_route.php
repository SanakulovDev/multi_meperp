<?php

use yii\db\Migration;

class m200721_153600_route extends Migration
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
            '{{%route}}',
            [
                'id'=> $this->primaryKey(11),
                'ship_mode'=> $this->tinyInteger()->notNull(),
                'from_point_id'=> $this->integer()->notNull(),
                'to_point_id'=> $this->integer()->notNull(),
                'description'=> $this->text(),

            ],$tableOptions
        );

        $this->addForeignKey('frk-route-from_point_id', 'route', 'from_point_id', 'point', 'id');
        $this->addForeignKey('frk-route-to_point_id', 'route', 'to_point_id', 'point', 'id');

    }

    public function safeDown()
    {
        $this->dropForeignKey('frk-route-from_point_id', 'route');
        $this->dropForeignKey('frk-route-to_point_id', 'route');

        $this->dropTable('{{%route}}');
    }
}
