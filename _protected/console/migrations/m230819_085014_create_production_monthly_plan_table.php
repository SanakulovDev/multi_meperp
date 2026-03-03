<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%production_monthly_plan}}`.
 */
class m230819_085014_create_production_monthly_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%production_monthly_plan}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer(),
            'production_date' => $this->date(),
            'warehouse_id' => $this->integer(),
            'shift' => $this->integer(),
            'target_qty' => $this->integer(),
            'line' => $this->integer(),
            'type' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%production_monthly_plan}}');
    }
}
