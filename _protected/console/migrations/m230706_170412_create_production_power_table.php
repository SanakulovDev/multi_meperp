<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%production_power}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%unitId}}`
 */
class m230706_170412_create_production_power_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%production_power}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer(),
            'part_name' => $this->string(),
            'test_pr' => $this->string(),
            'target_date' => $this->date(),
            'line' => $this->integer(),
            'shift' => $this->integer(),
            'unitId' => $this->integer(),
            'plan_power' => $this->string(),
            'max_power' => $this->string(),
            'special' => $this->string(),
        ]);

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%production_power}}');
    }
}
