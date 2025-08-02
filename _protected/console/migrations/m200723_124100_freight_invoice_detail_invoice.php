<?php
use yii\db\Migration;

class m200723_124100_freight_invoice_detail_invoice extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable(
      '{{%freight_invoice_detail_invoice}}',
      [
        'id' => $this->primaryKey(11),
        'freight_invoice_detail_id' => $this->integer()->notNull(),
        'invoice_id' => $this->integer()->notNull(),
      ], $tableOptions
    );
    $this->addForeignKey('frk-freight_invoice_detail_invoice-freight_invoice_detail_id', 'freight_invoice_detail_invoice', 'freight_invoice_detail_id', 'freight_invoice_detail', 'id', 'cascade', 'cascade');
    $this->addForeignKey('frk-freight_invoice_detail_invoice-invoice_id', 'freight_invoice_detail_invoice', 'invoice_id', 'invoice', 'id');
  }

  public function safeDown() {
    $this->dropForeignKey('frk-freight_invoice_detail_invoice-freight_invoice_detail_id', 'freight_invoice_detail_invoice');
    $this->dropForeignKey('frk-freight_invoice_detail_invoice-invoice_id', 'freight_invoice_detail_invoice');
    $this->dropTable('{{%freight_invoice_detail_invoice}}');
  }

}
