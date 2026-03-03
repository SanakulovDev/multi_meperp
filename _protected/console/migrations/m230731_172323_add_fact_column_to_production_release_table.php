<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_release}}`.
 */
class m230731_172323_add_fact_column_to_production_release_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_release}}', 'fact', $this->decimal(20,2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_release}}', 'fact');
    }
}
