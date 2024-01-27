<?php

use yii\db\Migration;

/**
 * Class m240123_161755_batch_insert_stock_info
 */
class m240123_161755_batch_insert_stock_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $arr = [
        'stock-info',
        'stock-xls-info',
        'document-create-info'
      ];
         foreach($arr as $item){

           $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'], [
             [$item, 2, $item, null, null, time(), time()],
            ]);
            $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
                        ['superadmin', $item],
            ]);
          }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $arr = [
        'stock-info',
        'stock-xls-info',
        'document-create-info'
      ];
      foreach($arr as $item){
        $this->delete('{{%auth_item_child}}', ['child' => $item]);
        $this->delete('{{%auth_item}}', ['name' => $item]);
      }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_161755_batch_insert_stock_info cannot be reverted.\n";

        return false;
    }
    */
}
