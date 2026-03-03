<?php

use yii\db\Migration;

/**
 * Class m240126_131021_add_batchInsert_warehouse_information_new_warehouse
 */
class m240126_131021_add_batchInsert_warehouse_information_new_warehouse extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $columns = ['name', 'description', 'status', 'warehouse_type', 'warehouse_report_group_id', 'created_by', 'created_at'];
      $data = [
          ['Stock Info', 'Stock  Info', 1, 4, 1, 2, time()]
      ];
      Yii::$app->db->createCommand()->batchInsert('warehouse', $columns, $data)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //Delete Name=Stock Info
        Yii::$app->db->createCommand()->delete('warehouse', ['name'=>'Stock Info'])->execute();
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240126_131021_add_batchInsert_warehouse_information_new_warehouse cannot be reverted.\n";

        return false;
    }
    */
}
