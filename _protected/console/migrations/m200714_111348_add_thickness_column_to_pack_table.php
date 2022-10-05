<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%pack}}`.
 */
class m200714_111348_add_thickness_column_to_pack_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%pack}}', 'thickness', $this->decimal(10,3)->null()->after('weight'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('{{%pack}}', 'thickness');
    }
}
