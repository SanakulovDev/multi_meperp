<?php

use yii\db\Migration;

/**
 * Class m230612_234446_insert_stock_dashboard_permission
 */
class m230612_234446_insert_stock_dashboard_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $name = 'stock-dashboard';
         //autn item table batch insert stock-dashboard
         $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'], [
            [$name, 2, 'Stock Dashboard', null, null, time(), time()],
        ]);

        //autn item child table batch insert stock-dashboard
        $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
            ['superadmin', $name],
            ['admin', $name],
        ]);

        $name1 = 'stock-download-dashboard';
            //autn item table batch insert stock-dashboard
            $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'], [
                [$name1, 2, 'Stock Dashboard', null, null, time(), time()],
            ]);
        
            //autn item child table batch insert stock-dashboard
            $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
                ['superadmin', $name1],
                ['admin', $name1],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $name = 'stock-dashboard';
        $name1 = 'stock-download-dashboard';
        //autn item child table batch delete stock-dashboard
        $this->delete('{{%auth_item_child}}', ['child' => $name]);
        //autn item table batch delete stock-dashboard
        $this->delete('{{%auth_item}}', ['name' => $name]);

        //autn item child table batch delete stock-dashboard-download
        $this->delete('{{%auth_item_child}}', ['child' => $name1]);
        //autn item table batch delete stock-dashboard-download
        $this->delete('{{%auth_item}}', ['name' => $name1]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230612_234446_insert_stock_dashboard_permission cannot be reverted.\n";

        return false;
    }
    */
}
