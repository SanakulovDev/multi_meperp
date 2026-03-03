<?php
use yii\db\Migration;

class m200722_170000_add_container_type_to_container_table extends Migration {

  public function safeUp() {
    $this->dropColumn('container_invoice', 'container_type');

    $this->addColumn(
      'container',
      'container_type',
      $this->string(10)->notNull()->after('container_no')
    );
  }

  public function safeDown() {
    $this->addColumn(
      'container_invoice',
      'container_type',
      $this->string(20)->null()->after('ship_mode_id')
    );

    $this->dropColumn('container', 'container_type');
  }

}

