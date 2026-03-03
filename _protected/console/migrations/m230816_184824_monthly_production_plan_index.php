<?php

use yii\db\Migration;

/**
 * Class m230816_184824_monthly_production_plan_index
 */
class m230816_184824_monthly_production_plan_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //autn item table batch insert production-plan-monthly
        $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'], [
          ['production-plan-monthly-index', 2, 'Monthly Planning', null, null, time(), time()],
      ]);

      //autn item child table batch insert production-plan-monthly
      $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
          ['superadmin', 'production-plan-monthly-index'],
          ['admin', 'production-plan-monthly-index'],
      ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //delete row
          $this->delete('{{%auth_item}}', ['name' => 'production-plan-monthly-index']);
          $this->delete('{{%auth_item_child}}', ['parent' => 'superadmin', 'child' => 'production-plan-monthly-index']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230816_184824_monthly_production_plan_index cannot be reverted.\n";

        return false;
    }
    */
}
