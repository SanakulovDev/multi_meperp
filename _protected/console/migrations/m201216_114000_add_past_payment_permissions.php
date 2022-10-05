<?php
use yii\db\Migration;

class m201216_114000_add_past_payment_permissions extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('past-payment-note',2),
				('past-payment-alert',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('mfu','past-payment-note'),
				('mfu','past-payment-alert'),
				('admin','past-payment-note'),
				('superadmin','past-payment-note')"
    )->execute();
  }

}