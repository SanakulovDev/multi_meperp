<?php
use yii\db\Migration;

class m200428_183000_add_vehicle_etadate_permissions extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('vehicle-coverage-input-etadate-note',2),
				('vehicle-coverage-input-etadate-alert',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('mfu','vehicle-coverage-input-etadate-note'),
				('mfu','vehicle-coverage-input-etadate-alert'),
				('admin','vehicle-coverage-input-etadate-note'),
				('superadmin','vehicle-coverage-input-etadate-note')"
    )->execute();
  }

}