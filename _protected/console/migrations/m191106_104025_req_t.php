<?php

use yii\db\Schema;
use yii\db\Migration;

class m191106_104025_req_t extends Migration
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
            '{{%req_t}}',
            [
                'id'=> $this->primaryKey(11),
                'type'=> $this->string(2)->null()->defaultValue(null)->comment('D - Daily, W - Weekly'),
                'part_id'=> $this->integer(11)->notNull(),
                'whbal'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'linebal'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'semistock'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'fgstock'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'outsourcing'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'pending'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'arrive'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'calc_at'=> $this->datetime()->null()->defaultValue(null),
                'days_count'=> $this->integer(11)->null()->defaultValue(null),
            ],$tableOptions
        );
        $this->createIndex('part_id','{{%req_t}}',['part_id'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('part_id', '{{%req_t}}');
        $this->dropTable('{{%req_t}}');
    }
}
