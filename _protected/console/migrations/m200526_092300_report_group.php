<?php

use yii\db\Migration;

class m200526_092300_report_group extends Migration
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
            '{{%report_group}}',
            [
                'id'=> $this->primaryKey(11),
                'name'=> $this->string()->unique()->notNull(),
                'order'=> $this->tinyInteger(),
                'icon'=> $this->string(),
                'color'=> $this->string()
            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%report_group}}');
    }
}
