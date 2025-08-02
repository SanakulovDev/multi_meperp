<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_plan}}`.
 */
class m230816_173828_add_monthly_column_to_production_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_plan}}', 'type', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_plan}}', 'type');
    }
}
