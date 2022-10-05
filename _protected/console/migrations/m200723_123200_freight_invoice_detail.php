<?php
use yii\db\Migration;

class m200723_123200_freight_invoice_detail extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable(
      '{{%freight_invoice_detail}}',
      [
        'id' => $this->primaryKey(11),
        'freight_invoice_id' => $this->integer()->notNull(),
        'container_id' => $this->integer()->notNull(),
        'unit_id' => $this->integer()->notNull(),
        'quantity' => $this->integer()->notNull(),
        'comment' => $this->text(),
      ], $tableOptions
    );
    $this->addForeignKey('frk-freight_invoice_detail-freight_invoice_id', 'freight_invoice_detail', 'freight_invoice_id', 'freight_invoice', 'id', 'cascade', 'cascade');
    $this->addForeignKey('frk-freight_invoice_detail-container_id', 'freight_invoice_detail', 'container_id', 'container', 'id');
    $this->addForeignKey('frk-freight_invoice_detail-unit_id', 'freight_invoice_detail', 'unit_id', 'unit', 'id');

  }

  public function safeDown() {
    $this->dropForeignKey('frk-freight_invoice_detail-freight_invoice_id', 'freight_invoice_detail');
    $this->dropForeignKey('frk-freight_invoice_detail-container_id', 'freight_invoice_detail');
    $this->dropForeignKey('frk-freight_invoice_detail-unit_id', 'freight_invoice_detail');
    $this->dropTable('{{%freight_invoice_detail}}');
  }

}
