<?php

use yii\db\Migration;

/**
 * Class m230926_183803_auth_pechat_product_permission_users
 */
class m230926_183803_auth_pechat_product_permission_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $arr = [
        'pechat-product-index',
        'pechat-product-view',
        'pechat-product-create',
        'pechat-product-update',
        'pechat-product-delete',
        'pechat-product-print',
        'pechat-product-print-form',
      ];
         foreach($arr as $item){

           $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'], [
             [$item, 2, 'Stock Dashboard', null, null, time(), time()],
            ]);
            $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
                        ['superadmin', $item],
                        ['admin', $item],
            ]);
          }

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $arr = [
        'pechat-product-index',
        'pechat-product-view',
        'pechat-product-create',
        'pechat-product-update',
        'pechat-product-delete',
        'pechat-product-print',
        'pechat-product-print-form',
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
        echo "m230926_183803_auth_pechat_product_permission_users cannot be reverted.\n";

        return false;
    }
    */
}
