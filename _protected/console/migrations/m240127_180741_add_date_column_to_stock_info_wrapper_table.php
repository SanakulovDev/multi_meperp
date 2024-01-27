<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%stock_info_wrapper}}`.
 */
class m240127_180741_add_date_column_to_stock_info_wrapper_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%stock_info_wrapper}}', 'date', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%stock_info_wrapper}}', 'date');
    }
}
