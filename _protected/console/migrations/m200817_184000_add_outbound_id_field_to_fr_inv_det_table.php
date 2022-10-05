<?php
use yii\db\Migration;

class m200817_184000_add_outbound_id_field_to_fr_inv_det_table extends Migration {

  public function safeUp() {

    $this->addColumn('freight_invoice_detail','outbound_id', $this->integer());

  }

  public function safeDown() {

    $this->dropColumn('freight_invoice_detail','outbound_id');

  }

}

