<?php

use yii\db\Migration;

/**
 * Class m200324_082626_location_type
 */
class m200324_082626_location_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%location_type}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(100)->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%location_type}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200324_082626_location_type cannot be reverted.\n";

        return false;
    }
    */
}
