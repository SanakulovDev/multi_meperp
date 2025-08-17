<?php
use yii\db\Migration;

class m200416_141347_vehicle_coverage_input_permissin extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    /** add permission */
    Yii::$app->db->createCommand(
      "
         DELETE FROM auth_item_child WHERE child = 'vehicle-coverage-input-create';
         DELETE FROM auth_item_child WHERE child = 'vehicle-coverage-input-update';
         DELETE FROM auth_item_child WHERE child = 'vehicle-coverage-input-delete';
         DELETE FROM auth_item WHERE name = 'vehicle-coverage-input-create';
         DELETE FROM auth_item WHERE name = 'vehicle-coverage-input-update';
         DELETE FROM auth_item WHERE name = 'vehicle-coverage-input-delete';
                        
         INSERT IGNORE auth_item(name, type) 
         VALUES ('vehicle-coverage-input-index',  2), 
                ('vehicle-coverage-input-refresh', 2);                
         INSERT IGNORE auth_item_child(parent, child) 
         VALUES ('superadmin', 'vehicle-coverage-input-index'),
                ('superadmin', 'vehicle-coverage-input-refresh');
          "
    )->execute();
  }

}
