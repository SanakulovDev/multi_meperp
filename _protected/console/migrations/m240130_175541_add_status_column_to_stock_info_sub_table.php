<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%stock_info_sub}}`.
 */
class m240130_175541_add_status_column_to_stock_info_sub_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%stock_info_sub}}', 'status', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%stock_info_sub}}', 'status');
    }
}
