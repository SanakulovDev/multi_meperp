<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%stock_info}}`.
 */
class m240302_022531_add_old_qty_column_to_stock_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%stock_info}}', 'old_qty', $this->decimal(20,2)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%stock_info}}', 'old_qty');
    }
}
