<?php

use yii\db\Migration;

class m260307_140000_add_usage_qty_meter_column_to_product_specification_item_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('product_specification_item', 'usage_qty_meter', $this->decimal(15, 4)->null()->after('usage_qty'));
    }

    public function safeDown()
    {
        $this->dropColumn('product_specification_item', 'usage_qty_meter');
    }
}
