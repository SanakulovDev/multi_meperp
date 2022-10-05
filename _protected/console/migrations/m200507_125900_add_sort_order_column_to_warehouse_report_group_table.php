<?php

use yii\db\Migration;

/**
 * Class m200420_081509_add_lock_column_to_airshipment
 */
class m200507_125900_add_sort_order_column_to_warehouse_report_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%warehouse_report_group}}', 'sort_order', $this->tinyInteger()->null()->after('description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%warehouse_report_group}}', 'sort_order');
    }
}
