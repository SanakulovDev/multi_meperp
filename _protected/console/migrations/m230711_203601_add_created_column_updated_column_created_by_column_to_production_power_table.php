<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_power}}`.
 */
class m230711_203601_add_created_column_updated_column_created_by_column_to_production_power_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_power}}', 'created', $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%production_power}}', 'updated', $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'));
        $this->addColumn('{{%production_power}}', 'created_by', $this->integer()->null());
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_power}}', 'created');
        $this->dropColumn('{{%production_power}}', 'updated');
        $this->dropColumn('{{%production_power}}', 'created_by');
    }
}
