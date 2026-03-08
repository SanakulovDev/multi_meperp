<?php

use yii\db\Migration;

class m260307_150000_add_qty_meter_column_to_stock_info_wrapper_and_stock_info_tables extends Migration
{
    public function safeUp()
    {
        $this->addColumn('stock_info_wrapper', 'qty_meter', $this->decimal(15, 4)->null()->after('qty'));
        $this->addColumn('stock_info', 'qty_meter', $this->decimal(15, 4)->null()->after('qty'));
    }

    public function safeDown()
    {
        $this->dropColumn('stock_info_wrapper', 'qty_meter');
        $this->dropColumn('stock_info', 'qty_meter');
    }
}
