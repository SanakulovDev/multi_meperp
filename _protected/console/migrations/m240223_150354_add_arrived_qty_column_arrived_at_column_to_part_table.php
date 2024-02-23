<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%part}}`.
 */
class m240223_150354_add_arrived_qty_column_arrived_at_column_to_part_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%part}}', 'arrived_qty', $this->decimal(20,2));
        $this->addColumn('{{%part}}', 'arrived_at', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%part}}', 'arrived_qty');
        $this->dropColumn('{{%part}}', 'arrived_at');
    }
}
