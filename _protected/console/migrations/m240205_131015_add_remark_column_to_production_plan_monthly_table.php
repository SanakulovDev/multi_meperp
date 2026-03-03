<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_plan_monthly}}`.
 */
class m240205_131015_add_remark_column_to_production_plan_monthly_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_monthly_plan}}', 'remark', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_monthly_plan}}', 'remark');
    }
}
