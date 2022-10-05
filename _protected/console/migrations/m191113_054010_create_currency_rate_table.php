<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%currency_rate}}`.
 */
class m191113_054010_create_currency_rate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%currency_rate}}', [
            'id' => $this->primaryKey(),
            'rate_date' => $this->date()->notNull(),
            'currency_id' => $this->integer()->notNull(),
            'uzs_value' => $this->decimal(10,5)->notNull(),
            'created_by' => $this->integer(11)->null(),
            'created_at' => $this->integer(11)->notNull(),
            'updated_by' => $this->integer(11)->null()->defaultValue(null),
            'updated_at' => $this->integer(11)->null()->defaultValue(null),
        ]);
        
        $this->addForeignKey('frk-currency_rate-currency_id', 'currency_rate','currency_id', 'currency','id');
        $this->addForeignKey('frk-currency_rate-created_by', 'currency_rate','created_by', 'user','id');
        $this->addForeignKey('frk-currency_rate-updated_by', 'currency_rate','updated_by', 'user','id');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('frk-currency_rate-currency_id', 'currency_rate');
        $this->dropForeignKey('frk-currency_rate-created_by', 'currency_rate');
        $this->dropForeignKey('frk-currency_rate-updated_by', 'currency_rate');
        $this->dropTable('currency_rate');
    }
}
