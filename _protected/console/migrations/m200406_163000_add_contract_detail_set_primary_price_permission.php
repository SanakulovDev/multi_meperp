<?php
use yii\db\Migration;

class m200406_163000_add_contract_detail_set_primary_price_permission extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('contract-detail-set-primary-price',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('buyer','contract-detail-set-primary-price'),
				('admin','contract-detail-set-primary-price'),
				('superadmin','contract-detail-set-primary-price')"
    )->execute();
  }

}