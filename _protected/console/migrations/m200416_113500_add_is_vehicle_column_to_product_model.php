<?php
use yii\db\Migration;

class m200416_113500_add_is_vehicle_column_to_product_model extends Migration {

  public function safeUp() {
    $this->addColumn(
      '{{%product_model}}',
      'is_vehicle',
      $this->tinyInteger(1)->unsigned()->null()->defaultValue(null)
    );
  }
  public function safeDown(){
    $this->dropColumn('{{%product_model}}', 'is_vehicle');
  }
}
