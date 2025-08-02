<?php
use yii\db\Migration;

class m200414_131000_add_coverage_balance_permissions extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('coverage-balance-index',2),
				('coverage-balance-update',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('admin','coverage-balance-index'),
				('admin','coverage-balance-update'),
				('superadmin','coverage-balance-index'),
				('superadmin','coverage-balance-update')"
    )->execute();
  }

}