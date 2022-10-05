<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%formulation_specification}}`.
 */
class m200120_083914_create_formulation_specification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%formulation_specification}}', [
            'id' => $this->primaryKey(11),
            'formulation_id' => $this->integer(11)->notNull(),
            'item' => $this->string(100)->notNull(),
            'min' => $this->decimal(20,5),
            'max' => $this->decimal(20,5),
            'result' => $this->decimal(20,5),
        ], $tableOptions);
        $this->addForeignKey('frk-formulation_specification-formulation_id',
                             '{{%formulation_specification}}', 'formulation_id',
                             '{{%formulation}}', 'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%formulation_specification}}');
    }
}
