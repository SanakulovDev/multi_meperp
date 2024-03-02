<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%stock_info_wrapper}}`.
 */
class m240229_183553_add_shift_column_to_stock_info_wrapper_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%stock_info_wrapper}}', 'shift', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%stock_info_wrapper}}', 'shift');
    }
}
