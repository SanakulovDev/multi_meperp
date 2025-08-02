<?php
use yii\db\Migration;

class m200721_110000_add_container_type_to_container_invoice_table extends Migration {

  public function safeUp() {
    $this->addColumn(
      'container_invoice',
      'container_type',
      $this->string(20)->null()->after('ship_mode_id')
    );
  }

  public function safeDown() {
    $this->dropColumn('container_invoice', 'container_type');
  }

}

