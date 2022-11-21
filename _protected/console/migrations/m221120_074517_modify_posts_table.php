<?php

use yii\db\Migration;

/**
 * Class m221120_074517_modify_posts_table
 */
class m221120_074517_modify_posts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('posts','comment',$this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221120_074517_modify_posts_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221120_074517_modify_posts_table cannot be reverted.\n";

        return false;
    }
    */
}
