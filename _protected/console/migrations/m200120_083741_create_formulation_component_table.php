<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%formulation_component}}`.
 */
class m200120_083741_create_formulation_component_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%formulation_component}}', [
            'id' => $this->primaryKey(11),
            'formulation_id' => $this->integer(11)->notNull(),
            'part_id' => $this->integer(11)->notNull(),
            'std_value' => $this->decimal(20,5)->notNull(),
            'actual_value' => $this->decimal(20,5)->notNull(),
        ], $tableOptions);
        $this->addForeignKey('frk-formulation_component-formulation_id',
                             '{{%formulation_component}}', 'formulation_id',
                             '{{%formulation}}', 'id'
        );
        $this->addForeignKey('frk-formulation_component-part_id',
                             '{{%formulation_component}}', 'part_id',
                             '{{%part}}', 'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%formulation_component}}');
    }
}
