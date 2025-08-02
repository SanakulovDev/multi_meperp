<?php
use app\models\Part;
use yii\db\Migration;

class m200514_140002_delete_prod_plan_inactive_part extends Migration {

  public function Up() {
    Yii::$app->db->createCommand(
      "DELETE FROM production_plan
               WHERE production_plan.part_id in (
                  SELECT part.id FROM part 
                  WHERE ( part.status = ".Part::STATUS_INACTIVE." ) 
                        AND production_plan.production_date >= date(FROM_UNIXTIME(part.updated_at )) 
              );"
    )->execute();
  }

}