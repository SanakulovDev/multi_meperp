<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_release}}`.
 */
class m230818_063047_add_updated_by_column_to_production_release_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_release}}', 'updated_by', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_release}}', 'updated_by');
    }
}
