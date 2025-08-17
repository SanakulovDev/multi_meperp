<?php
use yii\db\Migration;

class m200820_165900_add_report_index_permission_to_some_roles extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES
				('buyer','report-index'),
				('mrpc','report-index')
			"
		)->execute();
		
  }

}