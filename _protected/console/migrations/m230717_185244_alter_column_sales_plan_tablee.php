<?php

use yii\db\Migration;

/**
 * Class m230717_185244_alter_column_sales_plan_tablee
 */
class m230717_185244_alter_column_sales_plan_tablee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->alterColumn('{{%sales_plan}}', 'target_qty', $this->double());
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->alterColumn('{{%production_release}}', 'target_qty', $this->decimal(20,2));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230717_185244_alter_column_sales_plan_tablee cannot be reverted.\n";

        return false;
    }
    */
}
