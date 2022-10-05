<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%formulation_base}}`.
 */
class m200117_093233_create_formulation_base_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%formulation_base}}', [
            'id' => $this->primaryKey(11),
            'part_id' => $this->integer(11)->notNull(),
            'pack' => $this->decimal(20,5)->notNull(),
            'version' => $this->integer(11)->notNull(),
            'status' => $this->integer(1)->notNull(),
            'std_rate' => $this->decimal(20,5)->notNull(),
            'items' => $this->text()->notNull(),
            'specifications' => $this->text()->notNull(),
            'instructions' => $this->text()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('frk-formulation_base-part_id',
                             '{{%formulation_base}}', 'part_id',
                             '{{%part}}', 'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%formulation_base}}');
    }
}
