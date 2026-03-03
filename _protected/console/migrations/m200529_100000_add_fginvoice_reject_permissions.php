<?php
use yii\db\Migration;

class m200529_100000_add_fginvoice_reject_permissions extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
        "INSERT IGNORE `auth_item`(`name`, `type`) VALUES ('fg-invoice-reject',2);      
            INSERT IGNORE `auth_item_child`(`parent`, `child`) VALUES 
            ('admin','fg-invoice-reject'),	
            ('superadmin','fg-invoice-reject') 
           "
    )->execute();
  }

}