<?php
use yii\db\Migration;

class m201207_124300_add_type_to_production_plan_sub_table extends Migration {

  public function Up() {
    
    $this->addColumn('production_plan_sub','type', $this->string(1)->after('part_id'));
    
  }

}