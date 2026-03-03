<?php

use yii\db\Migration;

class m250802_184347_add_warehouse_confirmed_by_column_warehouse_confirmed_at_column_to_fg_invoice_table extends Migration{

      /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%fg_invoice}}', 'warehouse_confirmed_by', $this->integer());
        $this->addColumn('{{%fg_invoice}}', 'warehouse_confirmed_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%fg_invoice}}', 'warehouse_confirmed_by');
        $this->dropColumn('{{%fg_invoice}}', 'warehouse_confirmed_at');
    }
}