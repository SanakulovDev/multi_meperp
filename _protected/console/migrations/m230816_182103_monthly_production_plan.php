<?php

use yii\db\Migration;

/**
 * Class m230816_182103_monthly_production_plan
 */
class m230816_182103_monthly_production_plan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //autn item table batch insert production-plan-monthly
        $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'], [
            ['production-plan-monthly-*', 2, 'Monthly Planning', null, null, time(), time()],
        ]);

        //autn item child table batch insert production-plan-monthly
        $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
            ['superadmin', 'production-plan-monthly-*'],
            ['admin', 'production-plan-monthly-*'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       //delete row
        $this->delete('{{%auth_item}}', ['name' => 'production-plan-monthly-*']);
        $this->delete('{{%auth_item_child}}', ['parent' => 'superadmin', 'child' => 'production-plan-monthly-*']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230816_182103_monthly_production_plan cannot be reverted.\n";

        return false;
    }
    */
}
