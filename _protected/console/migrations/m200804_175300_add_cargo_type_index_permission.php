<?php
use yii\db\Migration;

class m200804_175300_add_cargo_type_index_permission extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('cargo-type-index',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('admin','cargo-type-index'),
				('superadmin','cargo-type-index')"
    )->execute();
  }

}