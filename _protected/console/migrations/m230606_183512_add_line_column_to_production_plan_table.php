<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_plan}}`.
 */
class m230606_183512_add_line_column_to_production_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_plan}}', 'line', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_plan}}', 'line');
    }
}
