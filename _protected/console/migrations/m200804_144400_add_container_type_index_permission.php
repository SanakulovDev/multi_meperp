<?php
use yii\db\Migration;

class m200804_144400_add_container_type_index_permission extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('container-type-index',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('admin','container-type-index'),
				('superadmin','container-type-index')"
    )->execute();
  }

}