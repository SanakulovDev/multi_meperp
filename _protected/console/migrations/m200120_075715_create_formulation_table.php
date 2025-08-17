<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%formulation}}`.
 */
class m200120_075715_create_formulation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%formulation}}', [
            'id' => $this->primaryKey(11),
            'formulation_base_id' => $this->integer(11)->notNull(),
            'amount' => $this->decimal(20,5),
            'customer_id' => $this->integer(11)->notNull(),
            'order_no' => $this->integer(11),
            'ulock' => $this->integer(11)->notNull(),
            'due_at' => $this->datetime()->null(),
            'start_at' => $this->datetime()->null(),
            'finish_at' => $this->datetime()->null(),
            'act_rate' => $this->decimal(20,5)->null(),
            'grind' => $this->string(50)->null(),
            'packages' => $this->text()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('frk-formulation-formulation_base_id',
                             '{{%formulation}}', 'formulation_base_id',
                             '{{%formulation_base}}', 'id'
        );
        $this->addForeignKey('frk-formulation-customer_id',
                             '{{%formulation}}', 'customer_id',
                             '{{%customer}}', 'id'
        );
        $this->addForeignKey('frk-formulation-ulock',
                             '{{%formulation}}', 'ulock',
                             '{{%warehouse}}', 'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%formulation}}');
    }
}
