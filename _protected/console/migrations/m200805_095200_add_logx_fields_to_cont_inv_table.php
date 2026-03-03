<?php
use yii\db\Migration;

class m200805_095200_add_logx_fields_to_cont_inv_table extends Migration {

  public function safeUp() {

    $this->addColumn('container_invoice','net_weight', $this->decimal(10,2));
    $this->addColumn('container_invoice','gross_weight', $this->decimal(10,2));
    $this->addColumn('container_invoice','cbm', $this->decimal(10,2));
    $this->addColumn('container_invoice','station_date', $this->date());
    $this->addColumn('container_invoice','cargo_type', $this->tinyInteger());

  }

  public function safeDown() {

    $this->dropColumn('container_invoice','net_weight');
    $this->dropColumn('container_invoice','gross_weight');
    $this->dropColumn('container_invoice','cbm');
    $this->dropColumn('container_invoice','station_date');
    $this->dropColumn('container_invoice','cargo_type');

  }

}

