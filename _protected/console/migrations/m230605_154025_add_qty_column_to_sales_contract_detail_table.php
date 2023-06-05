<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%sales_contract_detail}}`.
 */
class m230605_154025_add_qty_column_to_sales_contract_detail_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sales_contract_detail}}', 'qty', $this->decimal(20,5)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sales_contract_detail}}', 'qty');
    }
}
