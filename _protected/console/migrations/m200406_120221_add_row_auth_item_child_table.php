<?php
use yii\db\Migration;

class m200406_120221_add_row_auth_item_child_table extends Migration {

  public function Up() {
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('mrpc','stock-index'),
				('mrpc','stock-xls'),
				('counter','production-order-delete'),
				('counter','production-order-view')"
    )->execute();
  }

}