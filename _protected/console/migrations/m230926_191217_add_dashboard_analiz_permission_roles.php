<?php

use yii\db\Migration;

/**
 * Class m230926_191217_add_dashboard_analiz_permission_roles
 */
class m230926_191217_add_dashboard_analiz_permission_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $arr = [
        'dashboard-analiz',
        'dashboard-analiz-ajax',
        'dashboard-analiz-form-modal',
      ];
      foreach($arr as $item){
         $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
                     ['monitor', $item],
         ]);
       }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $arr = [
        'dashboard-analiz',
        'dashboard-analiz-ajax',
        'dashboard-analiz-form-modal',
        'document-index'
      ];
      foreach($arr as $item){
        $this->delete('{{%auth_item_child}}', ['child' => $item]);
      }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230926_191217_add_dashboard_analiz_permission_roles cannot be reverted.\n";

        return false;
    }
    */
}
