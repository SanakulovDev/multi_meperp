<?php

use yii\db\Migration;

/**
 * Class m210302_133323_create_table_line_stop_reason
 */
class m210302_133323_create_line_stop_reason_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
      $this->createTable('{{%line_stop_reason}}', [
        'id' => $this->primaryKey(),
        'name' => $this->string(255)->notNull(),
        'auth_item_name' => $this->string(100)->notNull()
      ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropTable('{{%line_stop_reason}}');
    }

}
