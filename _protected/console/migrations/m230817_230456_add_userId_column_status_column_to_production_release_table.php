<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_release}}`.
 */
class m230817_230456_add_userId_column_status_column_to_production_release_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_release}}', 'status', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_release}}', 'status');
    }
}
