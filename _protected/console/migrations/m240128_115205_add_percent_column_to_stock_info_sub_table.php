<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%stock_info_sub}}`.
 */
class m240128_115205_add_percent_column_to_stock_info_sub_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%stock_info_sub}}', 'percent', $this->decimal(20,2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%stock_info_sub}}', 'percent');
    }
}
