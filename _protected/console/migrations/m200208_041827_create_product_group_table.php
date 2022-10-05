<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_group}}`.
 */
class m200208_041827_create_product_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%product_group}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(100)->notNull(),
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_part_product_group_id', '{{%part}}');
//        $this->dropColumn('{{%part}}','product_group_id');
        $this->dropTable('{{%product_group}}');
    }
}
