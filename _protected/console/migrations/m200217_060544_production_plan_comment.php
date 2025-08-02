<?php

use yii\db\Migration;

/**
 * Class m200217_060544_production_plan_comment
 */
class m200217_060544_production_plan_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%production_plan_comment}}', [
            'id' => $this->primaryKey(11),
            'production_plan_id' => $this->integer(11)->notNull()->unsigned(),
            'comment' => $this->text()->notNull(),
            'created_at' => $this->integer(11)->notNull(),
            'created_by' => $this->integer(11)->notNull(),
        ], $tableOptions);
        $this->addForeignKey('frk-production_plan_comment-production_plan_id',
                             '{{%production_plan_comment}}', 'production_plan_id',
                             '{{%production_plan}}', 'id',
                             'CASCADE', 'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%production_plan_comment}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200217_060544_production_plan_comment cannot be reverted.\n";

        return false;
    }
    */
}
