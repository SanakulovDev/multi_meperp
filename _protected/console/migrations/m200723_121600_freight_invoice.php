<?php
use yii\db\Migration;

class m200723_121600_freight_invoice extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable(
      '{{%freight_invoice}}',
      [
        'id' => $this->primaryKey(11),
        'invoice_type' => $this->tinyInteger()->unsigned(),
        'invoice_no' => $this->string()->notNull(),
        'invoice_date' => $this->date()->notNull(),
        'contract' => $this->string()->notNull(),
        'route_id' => $this->integer()->notNull(),
        'carrier_id' => $this->integer()->notNull(),
        'delivery_term_id' => $this->integer()->notNull(),
      ], $tableOptions
    );
    $this->addForeignKey('frk-freight_invoice-delivery_term_id', 'freight_invoice', 'delivery_term_id', 'delivery_term', 'id');
    $this->addForeignKey('frk-freight_invoice-route_id', 'freight_invoice', 'route_id', 'route', 'id');
    $this->addForeignKey('frk-freight_invoice-carrier_id', 'freight_invoice', 'carrier_id', 'carrier', 'id');
  }

  public function safeDown() {
    $this->dropForeignKey('frk-freight_invoice-delivery_term_id', 'freight_invoice');
    $this->dropForeignKey('frk-freight_invoice-route_id', 'freight_invoice');
    $this->dropForeignKey('frk-freight_invoice-carrier_id', 'freight_invoice');
    $this->dropTable('{{%freight_invoice}}');
  }

}
