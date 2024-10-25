<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%unfamiliar_otchot}}`.
 */
class m241025_020615_create_unfamiliar_otchot_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%unfamiliar_otchot}}', [
            'id' => $this->primaryKey(),
            'user_id'   =>  $this->integer(),
            'part_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->defaultValue(0),
            'location' => $this->string(255),
            'status' => $this->string(100),
            'expected_arrival_date' => $this->date(),
            'remark' => $this->string(255),
            'created_at'    =>  $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'    =>  $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%unfamiliar_otchot}}');
    }
}
