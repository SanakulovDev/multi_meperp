<?php

use yii\db\Schema;
use yii\db\Migration;

class m200420_120300_coverage_vehicle_detail_t extends Migration
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
            '{{%coverage_vehicle_detail_t}}',
            [
                'id'=> $this->primaryKey(11),
                'coverage_vehicle_id'=> $this->integer(11)->notNull(),
                'type'=> $this->string(2)->null()->comment('D - Daily, W - Weekly'),
                'col1'=> $this->integer()->null(),
                'col2'=> $this->integer()->null(),
                'col3'=> $this->integer()->null(),
                'col4'=> $this->integer()->null(),
                'col5'=> $this->integer()->null(),
                'col6'=> $this->integer()->null(),
                'col7'=> $this->integer()->null(),
                'col8'=> $this->integer()->null(),
                'col9'=> $this->integer()->null(),
                'col10'=> $this->integer()->null(),
                'col11'=> $this->integer()->null(),
                'col12'=> $this->integer()->null(),
                'col13'=> $this->integer()->null(),
                'col14'=> $this->integer()->null(),
                'col15'=> $this->integer()->null(),
                'col16'=> $this->integer()->null(),
                'col17'=> $this->integer()->null(),
                'col18'=> $this->integer()->null(),
                'col19'=> $this->integer()->null(),
                'col20'=> $this->integer()->null(),
                'col21'=> $this->integer()->null(),
                'col22'=> $this->integer()->null(),
                'col23'=> $this->integer()->null(),
                'col24'=> $this->integer()->null(),
                'col25'=> $this->integer()->null(),
                'col26'=> $this->integer()->null(),
                'col27'=> $this->integer()->null(),
                'col28'=> $this->integer()->null(),
                'col29'=> $this->integer()->null(),
                'col30'=> $this->integer()->null(),
                'col31'=> $this->integer()->null(),
                'col32'=> $this->integer()->null(),
                'col33'=> $this->integer()->null(),
                'col34'=> $this->integer()->null(),
                'col35'=> $this->integer()->null(),
                'col36'=> $this->integer()->null(),
                'col37'=> $this->integer()->null(),
                'col38'=> $this->integer()->null(),
                'col39'=> $this->integer()->null(),
                'col40'=> $this->integer()->null(),
                'col41'=> $this->integer()->null(),
                'col42'=> $this->integer()->null(),
                'col43'=> $this->integer()->null(),
                'col44'=> $this->integer()->null(),
                'col45'=> $this->integer()->null(),
                'col46'=> $this->integer()->null(),
                'col47'=> $this->integer()->null(),
                'col48'=> $this->integer()->null(),
                'col49'=> $this->integer()->null(),
                'col50'=> $this->integer()->null(),
                'col51'=> $this->integer()->null(),
                'col52'=> $this->integer()->null(),
                'col53'=> $this->integer()->null(),
                'col54'=> $this->integer()->null(),
                'col55'=> $this->integer()->null(),
                'col56'=> $this->integer()->null(),
                'col57'=> $this->integer()->null(),
                'col58'=> $this->integer()->null(),
                'col59'=> $this->integer()->null(),
                'col60'=> $this->integer()->null(),
                'col61'=> $this->integer()->null(),
                'col62'=> $this->integer()->null(),
                'col63'=> $this->integer()->null(),
                'col64'=> $this->integer()->null(),
                'col65'=> $this->integer()->null(),
                'col66'=> $this->integer()->null(),
                'col67'=> $this->integer()->null(),
                'col68'=> $this->integer()->null(),
                'col69'=> $this->integer()->null(),
                'col70'=> $this->integer()->null(),
                'col71'=> $this->integer()->null(),
            ],$tableOptions
        );
        $this->createIndex('idx-coverage_vehicle_detail_t-coverage_vehicle_id','{{%coverage_vehicle_detail_t}}',['coverage_vehicle_id'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('idx-coverage_vehicle_detail_t-coverage_vehicle_id', '{{%coverage_vehicle_detail_t}}');
        $this->dropTable('{{%coverage_vehicle_detail_t}}');
    }
}
