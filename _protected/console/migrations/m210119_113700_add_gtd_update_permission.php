<?php
use yii\db\Migration;

class m210119_113700_add_gtd_update_permission extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('gtd-update',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('admin','gtd-update'),
				('superadmin','gtd-update')"
    )->execute();
  }

}