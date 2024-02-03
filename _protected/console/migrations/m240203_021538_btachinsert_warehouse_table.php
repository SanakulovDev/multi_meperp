<?php

use yii\db\Migration;

/**
 * Class m240203_021538_btachinsert_warehouse_table
 */
class m240203_021538_btachinsert_warehouse_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $columns = ['name', 'description', 'status', 'warehouse_type', 'warehouse_report_group_id', 'created_by', 'created_at'];
      $data = [
          ['Микс-склад', 'Микс-склад', 1, 5, 1, 2, time()],
          ['Склад мусора', 'Склад мусора', 1,5,1,2, time()]
      ];
      Yii::$app->db->createCommand()->batchInsert('warehouse', $columns, $data)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      Yii::$app->db->createCommand()->delete('warehouse', ['name'=>'Микс-склад'])->execute();
      Yii::$app->db->createCommand()->delete('warehouse', ['name'=>'Склад мусора'])->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240203_021538_btachinsert_warehouse_table cannot be reverted.\n";

        return false;
    }
    */
}
