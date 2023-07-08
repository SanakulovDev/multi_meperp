<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_power}}`.
 */
class m230706_181607_add_time_column_to_production_power_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_power}}', 'time', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_power}}', 'time');
    }
}
