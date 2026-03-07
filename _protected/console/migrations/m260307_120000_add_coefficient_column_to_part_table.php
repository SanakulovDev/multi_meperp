<?php

use yii\db\Migration;

class m260307_120000_add_coefficient_column_to_part_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('part', 'coefficient', $this->decimal(10, 4)->null()->after('pack_size'));
    }

    public function safeDown()
    {
        $this->dropColumn('part', 'coefficient');
    }
}
