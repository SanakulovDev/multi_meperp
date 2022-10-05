<?php
use yii\db\Migration;

class m200511_163000_add_fix_problem_permissions extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
           VALUES 
           ('invoice-detail-fix-problem',2)
				"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
          VALUES 
          ('mfu','invoice-detail-fix-problem'),
          ('buyer','invoice-detail-fix-problem'),
          ('admin','invoice-detail-fix-problem'),	('superadmin','invoice-detail-fix-problem')
          "
    )->execute();
  }

}