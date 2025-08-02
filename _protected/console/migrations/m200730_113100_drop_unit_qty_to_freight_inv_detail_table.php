<?php
use yii\db\Expression;
use yii\db\Migration;

class m200730_113100_drop_unit_qty_to_freight_inv_detail_table extends Migration {

  public function safeUp() {
    $this->dropForeignKey('frk-freight_invoice_detail-unit_id', 'freight_invoice_detail');
    $this->dropColumn('freight_invoice_detail', 'unit_id');
    $this->dropColumn('freight_invoice_detail', 'quantity');
  }

  public function safeDown() {
    $this->execute("SET foreign_key_checks = 0;");
    $this->addColumn('freight_invoice_detail', 'unit_id', $this->integer(11)->notNull());
    $this->addColumn('freight_invoice_detail', 'quantity', $this->integer()->notNull());
    $this->addForeignKey('frk-freight_invoice_detail-unit_id',
      'freight_invoice_detail', 'unit_id',
      'unit', 'id');
    $this->execute("SET foreign_key_checks = 1;");
  }

}

