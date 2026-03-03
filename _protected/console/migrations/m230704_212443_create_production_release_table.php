<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%production_release}}`.
 */
class m230704_212443_create_production_release_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%production_release}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer(),
            'part_name' => $this->string(),
            'line' => $this->integer(),
            'pr_order_number' => $this->string(),
            'target_date' => $this->date(),
            'shift' => $this->string(),
            'time' => $this->string(),
            'quantity' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%production_release}}');
    }
}
