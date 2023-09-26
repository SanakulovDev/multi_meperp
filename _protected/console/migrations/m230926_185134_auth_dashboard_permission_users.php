<?php

use yii\db\Migration;

/**
 * Class m230926_185134_auth_dashboard_permission_users
 */
class m230926_185134_auth_dashboard_permission_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $arr = [
        'dashboard-index',
        'dashboard-fakt',
        'dashboard-ttn',
        'dashboard-prixod',
        'dashboard-norma-rasxod',
        // 'dashboard-analiz',
        'dashboard-analiz-ajax',
        'dashboard-analiz-form-modal',
        'dashboard-plan-prodaj',
        'dashboard-plan-prodaj-new',

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
        'dashboard-index',
        'dashboard-fakt',
        'dashboard-ttn',
        'dashboard-prixod',
        'dashboard-norma-rasxod',
        // 'dashboard-analiz',
        'dashboard-analiz-ajax',
        'dashboard-analiz-form-modal',
        'dashboard-plan-prodaj',
        'dashboard-plan-prodaj-new',
        
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
        echo "m230926_185134_auth_dashboard_permission_users cannot be reverted.\n";

        return false;
    }
    */
}
