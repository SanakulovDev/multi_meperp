<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_order}}`.
 */
class m240126_133920_add_stock_info_code_column_to_production_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_order}}', 'stock_info_wrapper_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_order}}', 'stock_info_wrapper_id');
    }
}
