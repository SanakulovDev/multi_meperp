<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_order}}`.
 */
class m240202_183814_add_mix_quantity_column_trash_quantity_column_to_production_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_order}}', 'mix_quantity', $this->decimal(20,2)->defaultvalue(0));
        $this->addColumn('{{%production_order}}', 'trash_quantity', $this->decimal(20,2)->defaultvalue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_order}}', 'mix_quantity');
        $this->dropColumn('{{%production_order}}', 'trash_quantity');
    }
}
