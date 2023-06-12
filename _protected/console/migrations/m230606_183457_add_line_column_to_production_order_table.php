<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_order}}`.
 */
class m230606_183457_add_line_column_to_production_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_order}}', 'line', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_order}}', 'line');
    }
}
