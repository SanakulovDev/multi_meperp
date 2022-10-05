<?php

use yii\db\Migration;

class m200721_153300_point extends Migration
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
            '{{%point}}',
            [
                'id'=> $this->primaryKey(11),
                'ship_mode'=> $this->tinyInteger()->notNull(),
                'name'=> $this->string()->notNull(),
                'description'=> $this->text(),

            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%point}}');
    }
}
