<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%sales_plan}}`.
 */
class m230605_183012_add_status_column_to_sales_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sales_plan}}', 'status', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sales_plan}}', 'status');
    }
}
