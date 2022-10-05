<?php
use yii\db\Migration;

class m201215_094500_add_dummy_order_field_to_payment_control_table extends Migration {

  public function safeUp() {

    $this->addColumn('payment_control','dummy_order', $this->tinyInteger(1)->defaultValue(0));

  }

  public function safeDown() {

    $this->dropColumn('payment_control','dummy_order');

  }

}

