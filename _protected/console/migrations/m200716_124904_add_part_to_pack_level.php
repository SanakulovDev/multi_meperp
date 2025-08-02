<?php

use yii\db\Migration;

/**
 * Class m200716_124904_add_part_to_pack_level
 */
class m200716_124904_add_part_to_pack_level extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pack_level}}', 'part_id', $this->integer()->null()->after('id'));
        $this->addForeignKey('frk-pack_level-part_id', 'pack_level', 'part_id',
                           'part', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropForeignKey('frk-pack_level-part_id', 'pack_level');
      $this->dropColumn('{{%pack_level}}', 'part_id');
    }
}
