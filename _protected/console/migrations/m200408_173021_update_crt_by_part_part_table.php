<?php
use yii\db\Migration;

class m200408_173021_update_crt_by_part_part_table extends Migration {

  public function Up() {
    Yii::$app->db->createCommand(
      "
            UPDATE part_part SET created_by =(SELECT id  FROM user LIMIT 1) WHERE created_by IS  NULL;
            ALTER TABLE `part_part` CHANGE `created_by` `created_by` INT(11) NOT NULL;
          "
    )->execute();
  }

}