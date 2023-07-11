<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_release}}`.
 */
class m230711_202759_add_created_column_updated_column_to_production_release_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_release}}', 'created', $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{%production_release}}', 'updated', $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'));
        // created_by
        // updated_by
        $this->addColumn('{{%production_release}}', 'created_by', $this->integer()->null());
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_release}}', 'created');
        $this->dropColumn('{{%production_release}}', 'updated');
        $this->dropColumn('{{%production_release}}', 'created_by');
    }
}
