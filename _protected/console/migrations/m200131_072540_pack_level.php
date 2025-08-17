<?php

use yii\db\Migration;

/**
 * Class m200131_072540_pack_level
 */
class m200131_072540_pack_level extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%pack_level}}', [
            'id' => $this->primaryKey(11),
            'pack_id' => $this->integer(11)->notNull(),
            'in_pack_id' => $this->integer(11)->notNull(),
            'quantity' => $this->smallInteger()->unsigned()->notNull()->defaultValue(1),
            'level' => $this->smallInteger()->unsigned()->notNull()->defaultValue(1),
            'version' => $this->smallInteger()->unsigned()->notNull()->defaultValue(1),

            'created_by' => $this->integer(11)->null()->defaultValue(null),
            'created_at' => $this->integer(11)->notNull(),
            'updated_by' => $this->integer(11)->null()->defaultValue(null),
            'updated_at' => $this->integer(11)->null()->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('uk_pack_id_in_pack_id_version', '{{%pack_level}}', ['pack_id', 'in_pack_id', 'version'], true);
        $this->addForeignKey('fk_pack_level_pack_id',
            '{{%pack_level}}', 'pack_id',
            '{{%pack}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey('fk_pack_level_in_pack_id',
            '{{%pack_level}}', 'in_pack_id',
            '{{%pack}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_pack_level_in_pack_id', 'pack_level');
        $this->dropForeignKey('fk_pack_level_pack_id', 'pack_level');
        $this->dropIndex('uk_pack_id_in_pack_id_version', 'pack_level');
        $this->dropTable('{{%pack_level}}');
    }

}
