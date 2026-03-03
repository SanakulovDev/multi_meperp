<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stock_info}}`.
 */
class m240123_135808_create_stock_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%stock_info}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer(),
            'warehouse_id' => $this->integer(),
            'qty' => $this->decimal(20,2),
            'give_user_id' => $this->integer(),
            'created_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%stock_info}}');
    }
}
