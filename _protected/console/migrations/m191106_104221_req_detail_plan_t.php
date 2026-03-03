<?php

use yii\db\Schema;
use yii\db\Migration;

class m191106_104221_req_detail_plan_t extends Migration
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
            '{{%req_detail_plan_t}}',
            [
                'id'=> $this->primaryKey(11),
                'req_id'=> $this->integer(11)->notNull(),
                'type'=> $this->string(2)->null()->defaultValue(null)->comment('D - Daily, W - Weekly'),
                'col1'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col2'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col3'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col4'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col5'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col6'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col7'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col8'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col9'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col10'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col11'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col12'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col13'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col14'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col15'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col16'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col17'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col18'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col19'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col20'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col21'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col22'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col23'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col24'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col25'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col26'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col27'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col28'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col29'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col30'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col31'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col32'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col33'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col34'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col35'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col36'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col37'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col38'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col39'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col40'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col41'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col42'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col43'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col44'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col45'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col46'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col47'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col48'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col49'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col50'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col51'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col52'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col53'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col54'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col55'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col56'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col57'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col58'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col59'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col60'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col61'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col62'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col63'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col64'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col65'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col66'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col67'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col68'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col69'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col70'=> $this->decimal(20, 5)->null()->defaultValue(null),
                'col71'=> $this->decimal(20, 5)->null()->defaultValue(null),
            ],$tableOptions
        );
        $this->createIndex('frk-req_detail_plan-req_id','{{%req_detail_plan_t}}',['req_id'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('frk-req_detail_plan-req_id', '{{%req_detail_plan_t}}');
        $this->dropTable('{{%req_detail_plan_t}}');
    }
}
