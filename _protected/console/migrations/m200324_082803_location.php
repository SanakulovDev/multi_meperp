<?php

use yii\db\Migration;

/**
 * Class m200324_082803_location
 */
class m200324_082803_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%location}}', [
            'id' => $this->primaryKey(11),
            'location_type_id' => $this->integer(11)->notNull(),
            'code' => $this->string(50)->notNull(),
            'name' => $this->string(100)->notNull(),
            'alias' => $this->string(100)->notNull(),
            'is_main' => $this->integer(11)->null()->defaultValue(0),
            'area' => $this->decimal(10,2)->null()->defaultValue(null),
            'conveyor_type_id' => $this->integer(11)->notNull(),
            'parent_id' => $this->integer(11)->null(),
            'address' => $this->string(255)->notNull(),
        ], $tableOptions);
        $this->addForeignKey('frk-location-parent_id',
                             '{{%location}}', 'parent_id',
                             '{{%location}}', 'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%location}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200324_082803_location cannot be reverted.\n";

        return false;
    }
    */
}
