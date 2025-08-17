<?php
use yii\db\Migration;

class m200723_125000_freight_invoice_detail_cost extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable(
      '{{%freight_invoice_detail_cost}}',
      [
        'id' => $this->primaryKey(11),
        'freight_invoice_detail_id' => $this->integer()->notNull(),
        'cost_type' => $this->tinyInteger()->notNull(),
        'value' => $this->decimal(10, 5)->notNull(),
        'currency_id' => $this->integer()->notNull(),
        'comment' => $this->text(),
      ], $tableOptions
    );
    $this->addForeignKey('frk-freight_invoice_detail_cost-freight_invoice_detail_id', 'freight_invoice_detail_cost', 'freight_invoice_detail_id', 'freight_invoice_detail', 'id', 'cascade', 'cascade');
    $this->addForeignKey('frk-freight_invoice_detail_cost-currency_id', 'freight_invoice_detail_cost', 'currency_id', 'currency', 'id');
  }

  public function safeDown() {
    $this->dropForeignKey('frk-freight_invoice_detail_cost-freight_invoice_detail_id', 'freight_invoice_detail_cost');
    $this->dropForeignKey('frk-freight_invoice_detail_cost-currency_id', 'freight_invoice_detail_cost');
    $this->dropTable('{{%freight_invoice_detail_cost}}');
  }

}
