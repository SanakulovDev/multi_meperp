<?php

use yii\db\Migration;

/**
 * Class m230602_135520_auth_item_child_batchinsert
 */
class m230602_135520_auth_item_child_batchinsert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //autn item table batch insert sales-plan-create-day
        $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'], [
            ['sales-plan-create-day', 2, 'Create a day sales plan', null, null, time(), time()],
        ]);

        //autn item child table batch insert sales-plan-create-day
        $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
            ['superadmin', 'sales-plan-create-day'],
            ['admin', 'sales-plan-create-day'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //autn item child table batch delete sales-plan-create-day
        $this->delete('{{%auth_item_child}}', ['child' => 'sales-plan-create-day']);
        //autn item table batch delete sales-plan-create-day
        $this->delete('{{%auth_item}}', ['name' => 'sales-plan-create-day']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230602_135520_auth_item_child_batchinsert cannot be reverted.\n";

        return false;
    }
    */
}
