<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%production_release_item}}`.
 */
class m230804_140447_add_partId_column_to_production_release_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_release_item}}', 'partId', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%production_release_item}}', 'partId');
    }
}
