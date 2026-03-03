<?php
use yii\db\Migration;

class m200409_164600_add_container_invoice_etadate_permissions extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('container-invoice-etadate-note',2),
				('container-invoice-etadate-alert',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('mfu','container-invoice-etadate-note'),
				('mfu','container-invoice-etadate-alert'),
				('admin','container-invoice-etadate-note'),
				('superadmin','container-invoice-etadate-note')"
    )->execute();
  }

}