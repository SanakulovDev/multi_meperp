<?php
use yii\db\Migration;

class m201228_105000_add_for_month_field_to_part_order_table extends Migration {

  public function safeUp() {

    $this->addColumn('part_order','for_month', $this->string(7)->null());

  }

  public function safeDown() {

    $this->dropColumn('part_order','for_month');

  }

}

